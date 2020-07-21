<?php

namespace JYPS\RegisterBundle\Controller;

use Aws\Ses\SesClient;
use Aws\Sns\SnsClient;
use Egulias\EmailValidator\EmailValidator;
use Egulias\EmailValidator\Validation\RFCValidation;
use GuzzleHttp\Client as GuzzleClient;
use JYPS\RegisterBundle\Entity\MemberFee;
use JYPS\RegisterBundle\Form\Type\MemberFeePayment;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Psr\Log\LoggerInterface;

class MemberFeeController extends Controller
{
    public function showAllAction()
    {
        $memberfeeconfigs = $this->getDoctrine()
            ->getRepository('JYPSRegisterBundle:MemberFeeConfig')
            ->findAll();

        $repository = $this->getDoctrine()->getRepository(
            'JYPSRegisterBundle:MemberFee'
        );
        $feeyears = $repository
            ->createQueryBuilder('memberfee')
            ->select('memberfee.fee_period')
            ->where('memberfee.paid= :paymentstatus')
            ->orderBy('memberfee.fee_period', 'DESC')
            ->setParameter('paymentstatus', 0)
            ->distinct()
            ->getQuery();

        $distinct_years = $feeyears->getResult();

        return $this->render(
            'JYPSRegisterBundle:MemberFee:show_memberfee.html.twig',
            [
                'memberfee_configs' => $memberfeeconfigs,
                'years' => $distinct_years,
            ]
        );
    }
    public function showUnpaidFeesAction(Request $request)
    {
        $year = $request->request->get('year');
        $total_amount = 0;
        $total_qty = 0;
        $memberfees = $this->getDoctrine()
            ->getRepository('JYPSRegisterBundle:MemberFee')
            ->findBy(
                ['fee_period' => $year, 'paid' => 0],
                ['member_id' => 'ASC']
            );
        $ok_fees = [];
        foreach ($memberfees as $memberfee) {
            $member = $memberfee->getMemberFee();

            if ($member->getMembershipEndDate() > new \DateTime('now')) {
                $total_amount =
                    $total_amount + $memberfee->getFeeAmountWithVat();
                $total_qty++;
                $ok_fees[] = $memberfee;
            }
        }
        return $this->render(
            'JYPSRegisterBundle:MemberFee:show_unpaid_fees.html.twig',
            [
                'memberfees' => $ok_fees,
                'year' => $year,
                'qty' => $total_qty,
                'total_amount' => $total_amount,
            ]
        );
    }
    public function sendSMSReminderAction($memberid)
    {
        $SnSclient = new SnsClient([
            'profile' => 'jyps',
            'region' => 'eu-west-1',
            'version' => '2010-03-31',
        ]);
        $member = $this->getDoctrine()
            ->getRepository('JYPSRegisterBundle:Member')
            ->findOneBy(['member_id' => $memberid]);
        $memberfee = $this->getDoctrine()
            ->getRepository('JYPSRegisterBundle:MemberFee')
            ->findOneBy([
                'member_id' => $member->getId(),
                'fee_period' => date('Y'),
                'paid' => 0,
            ]);
        try {
            $result = $SnSclient->publish([
                'SenderId' => 'JypsRy',
                'Message' =>
                    'Hei, rekisterimme mukaan et ole vielä maksanut tämän vuoden jäsenmaksuasi. Maksu: https://jasenrekisteri.jyps.fi/pay/' .
                    $memberfee->getReferenceNumber() .
                    ' Terveisin JYPS ry.',
                'PhoneNumber' => $member->getInternationalTelephone(),
            ]);
            $this->get('session')
                ->getFlashBag()
                ->add('notice', 'SMS lähetetty');
        } catch (AwsException $e) {
            $this->get('session')
                ->getFlashBag()
                ->add('notice', 'Lähetys epäonnistui');
        }

        return $this->redirect(
            $this->generateUrl('member', ['memberid' => $member->getMemberId()])
        );
    }
    public function sendReminderLetterAction(Request $request)
    {
        //sns aws client
        $SnSclient = new SnsClient([
            'profile' => 'jyps',
            'region' => 'eu-west-1',
            'version' => '2010-03-31',
        ]);

        $join_date_limit = $request->request->get('join_date_limit');
        $send_sms = $request->request->get('send_sms');
        $memberfees = $this->getDoctrine()
            ->getRepository('JYPSRegisterBundle:MemberFee')
            ->findBy(['paid' => 0], ['member_id' => 'ASC']);
        $qty = 0;
        $smsqty = 0;
        $error_qty = 0;
        $error_members = [];
        $error_smsmember = [];
        $smserrors = 0;
        //1month treshold from previous reminder
        $treshold_date = new \Datetime('now');
        $treshold_date->sub(new \DateInterval('P1M'));
        $bankaccount = $this->getDoctrine()
            ->getRepository('JYPSRegisterBundle:SystemParameter')
            ->findOneBy(['key' => 'BankAccount']);
        foreach ($memberfees as $memberfee) {
            $member = $memberfee->getMemberFee();
            if (
                $member->getMembershipStartDate()->format('Y-m-d') <
                    $join_date_limit &&
                $member->getMembershipEndDate() > new \DateTime('now') &&
                $member->getEmail() != '' &&
                $member->getParent() === null &&
                ($member->getReminderSentDate() <= $treshold_date ||
                    $member->getReminderSentDate() === null)
            ) {
                //sms
                if ($member->getTelephone() !== null && $send_sms === 'on') {
                    try {
                        $result = $SnSclient->publish([
                            'Message' =>
                                'Hei, rekisterimme mukaan et ole vielä maksanut tämän vuoden jäsenmaksuasi. Maksu: https://jasenrekisteri.jyps.fi/pay/' .
                                $memberfee->getReferenceNumber() .
                                ' Terveisin JYPS ry.',
                            'PhoneNumber' => $member->getInternationalTelephone(),
                        ]);
                        $smsqty++;
                    } catch (AwsException $e) {
                        $smserrors++;
                        $error_smsmember[] = $member;
                    }
                    $em = $this->getDoctrine()->getManager();
                    $member->setReminderSentDate(new \DateTime('now'));
                    $em->flush($member);
                } else {
                    $validator = new EmailValidator();
                    if (
                        $validator->isValid(
                            $member->getEmail(),
                            new RFCValidation()
                        )
                    ) {
                        //email
                        $message = new \Swift_Message();
                        $message
                            ->setSubject('JYPS ry jäsenmaksumuistutus')
                            ->setFrom('jasenrekisteri@jyps.fi')
                            ->setTo($member->getEmail())
                            ->setBody(
                                $this->renderView(
                                    'JYPSRegisterBundle:MemberFee:reminder_letter_email.txt.twig',
                                    [
                                        'member' => $member,
                                        'memberfee' => $memberfee,
                                        'bankaccount' => $bankaccount,
                                        'virtualbarcode' => $memberfee->getVirtualBarcode(
                                            $bankaccount
                                        ),
                                        'year' => date('Y'),
                                    ]
                                )
                            );
                        $this->get('mailer')->send($message);
                        $qty++;
                        $em = $this->getDoctrine()->getManager();
                        $member->setReminderSentDate(new \DateTime('now'));
                        $em->flush($member);
                    } else {
                        $error_qty++;
                        $error_members[] = $member;
                    }
                }
            }
        }

        return $this->render(
            'JYPSRegisterBundle:MemberFee:sent_reminder_report.html.twig',
            [
                'reminder_qty' => $qty,
                'error_qty' => $error_qty,
                'error_members' => $error_members,
                'sms_qty' => $smsqty,
                'smserror_member' => $error_smsmember,
                'sms_errors' => $smserrors,
            ]
        );
    }
    public function markFeesPaidAction(Request $request)
    {
        $fees = $request->get('Fees_to_be_marked');
        $em = $this->getDoctrine()->getManager();

        foreach ($fees as $fee) {
            $markfee = $this->getDoctrine()
                ->getRepository('JYPSRegisterBundle:MemberFee')
                ->findOneBy(['id' => $fee]);
            $markfee->setPaid(true);
            $em->flush($markfee);
        }
        return $this->showUnpaidFeesAction($request);
    }
    public function markOneFeeAsPaidAction(Request $request)
    {
        $feeid = $request->get('Fee_Id');
        $memberid = $request->get('Member_id');

        $em = $this->getDoctrine()->getManager();
        $markfee = $this->getDoctrine()
            ->getRepository('JYPSRegisterBundle:MemberFee')
            ->findOneBy(['id' => $feeid]);
        $markfee->setPaid(true);
        $em->flush($markfee);

        $member = $this->getDoctrine()
            ->getRepository('JYPSRegisterBundle:Member')
            ->findOneBy(['member_id' => $memberid]);

        return $member->showAllAction($memberid);
    }

    public function createMemberFeesAction(Request $request)
    {
        $d = $request->request->get('due_date');
        $duedate = new \DateTime($d);

        $em = $this->getDoctrine()->getManager();

        $repository = $this->getDoctrine()->getRepository(
            'JYPSRegisterBundle:Member'
        );

        $query = $repository
            ->createQueryBuilder('m')
            ->where('m.membership_end_date >= :current_date')
            ->setParameter('current_date', new \DateTime('now'))
            ->getQuery();
        $members = $query->getResult();
        $total_amount = 0;
        $total_qty = 0;

        foreach ($members as $member) {
            $setpaid = false;
            /*if member is child of familymember -> flag it as paid */
            $memberparent = $member->getParent();
            if (!empty($memberparent)) {
                $setpaid = true;
            }
            $memberFeeConfig = $member->getMemberType();
            if ($memberFeeConfig === null) {
                continue;
            }
            $amount = $memberFeeConfig->getMemberfeeAmount();
            //Fee is prepaid, create fee and mark paid, unmark member prepaid status
            if ($member->getNextMemberfeePaid() === true) {
                $setpaid = true;
                $member->setNextMemberfeePaid(false);
                $em = $this->getDoctrine()->getManager();
                $em->persist($member);
                $em->flush($member);
            } else {
                $setpaid = false;
            }
            //flag fee paid, and amount as zero
            if ($memberFeeConfig->getCreatefees() == 'JOIN_ONLY') {
                $setpaid = true;
                $amount = 0;
            }

            //Check that fee does not exists fe. already joined members who get fee when joining.
            $createfee = true;
            $fees = $member->getMemberFees();
            foreach ($fees as $fee) {
                if ($fee->getFeePeriod() == date('Y')) {
                    $createfee = false;
                    break;
                }
            }

            if ($createfee === true) {
                $total_amount = $total_amount + $amount;
                $total_qty = $total_qty + 1;
                $memberfee = new MemberFee();
                $memberfee->setMemberId($member->getMemberid());
                $memberfee->setFeeAmountWithVat($amount);
                $memberfee->setReferenceNumber(
                    date('Y') . $member->getMemberId()
                );
                $memberfee->setDueDate($duedate);
                $memberfee->setMemberFee($member);
                $memberfee->setFeePeriod(date('Y'));
                $memberfee->setPaid($setpaid);

                $em = $this->getDoctrine()->getManager();
                $em->persist($memberfee);
                $em->flush($memberfee);
            }
        }
        $this->get('session')
            ->getFlashBag()
            ->add(
                'notice',
                'Jäsenmaksut luotu, Kokonaissumma: ' .
                    $total_amount .
                    ' Määrä(kpl): ' .
                    $total_qty .
                    ''
            );
        return $this->redirect($this->generateUrl('memberfees'));
    }
    public function sendOneMemberFeeEmailAction(Request $request)
    {
        $SesClient = new SesClient([
            'profile' => 'jyps',
            'version' => '2010-12-01',
            'region' => 'eu-west-1',
        ]);
        $member_id = $request->request->get('member_id');

        $fee_period = date('Y');
        $bankaccount = $this->getDoctrine()
            ->getRepository('JYPSRegisterBundle:SystemParameter')
            ->findOneBy(['key' => 'BankAccount']);

        $member = $this->getDoctrine()
            ->getRepository('JYPSRegisterBundle:Member')
            ->findOneBy(['id' => $member_id]);

        $memberfee = $this->getDoctrine()
            ->getRepository('JYPSRegisterBundle:MemberFee')
            ->findOneBy([
                'member_id' => $member_id,
                'fee_period' => $fee_period,
            ]);
        if ($memberfee === null) {
            $memberfee = $this->getDoctrine()
                ->getRepository('JYPSRegisterBundle:MemberFee')
                ->findOneBy([
                    'member_id' => $member_id,
                    'fee_period' => $fee_period - 1,
                ]);
        }
        $validator = new EmailValidator();

        if (
            $validator->isValid($member->getEmail(), new RFCValidation()) &&
            !is_null($member->getEmail()) &&
            $member->getEmail() != ''
        ) {
            try {
                $result = $SesClient->sendEmail([
                    'Destination' => [
                        'ToAddresses' => [$member->getEmail()],
                    ],
                    'ReplyToAddresses' => ['jasenrekisteri@jyps.fi'],
                    'Source' => 'jasenrekisteri@jyps.fi',
                    'Message' => [
                        'Body' => [
                            'Text' => [
                                'Charset' => 'UTF-8',
                                'Data' => $this->renderView(
                                    'JYPSRegisterBundle:MemberFee:memberfee_email.txt.twig',
                                    [
                                        'member' => $member,
                                        'memberfee' => $memberfee,
                                        'bankaccount' => $bankaccount,
                                        'virtualbarcode' => $memberfee->getVirtualBarcode(
                                            $bankaccount
                                        ),
                                        'year' => date('Y'),
                                    ]
                                ),
                            ],
                        ],
                        'Subject' => [
                            'Charset' => 'UTF-8',
                            'Data' =>
                                'JYPS ry:n jäsenmaksu vuodelle ' . date('Y'),
                        ],
                    ],
                ]);
                $messageId = $result['MessageId'];
                echo "Email sent! Message ID: $messageId" . "\n";
            } catch (AwsException $e) {
                // output error message if fails
                echo $e->getMessage();
                echo 'The email was not sent. Error message: ' .
                    $e->getAwsErrorMessage() .
                    "\n";
                echo "\n";
            }
        }
        $this->get('session')
            ->getFlashBag()
            ->add('notice', 'Sähköposti lähetetty');
        return $this->redirect(
            $this->generateUrl('member', [
                'memberid' => $member->getMemberId(),
            ])
        );
    }
    public function sendMemberFeeEmailsAction(Request $request)
    {
        $errors = 0;
        $sent = 0;
        $SesClient = new SesClient([
            'profile' => 'jyps',
            'version' => '2010-12-01',
            'region' => 'eu-west-1',
        ]);
        $em = $this->getDoctrine()->getManager();

        $bankaccount = $this->getDoctrine()
            ->getRepository('JYPSRegisterBundle:SystemParameter')
            ->findOneBy(['key' => 'BankAccount']);

        $repository = $this->getDoctrine()->getRepository(
            'JYPSRegisterBundle:Member'
        );
        $query = $repository
            ->createQueryBuilder('m')
            ->where(
                'm.membership_end_date >= :current_date and m.membership_start_date <= :period_start'
            )
            ->setParameter('current_date', new \DateTime('now'))
            ->setParameter(
                'period_start',
                new \DateTime('first day of January ' . date('Y'))
            )
            ->getQuery();

        $members = $query->getResult();
        foreach ($members as $member) {
            $memberfee = $this->getDoctrine()
                ->getRepository('JYPSRegisterBundle:MemberFee')
                ->findOneBy([
                    'member_id' => $member->getId(),
                    'fee_period' => date('Y'),
                    'email_sent' => null,
                    'paid' => 0,
                ]);

            if (empty($memberfee) || $member->getParent() !== null) {
                continue;
            }
            $validator = new EmailValidator();

            $errors = '';
            if (
                $validator->isValid($member->getEmail(), new RFCValidation()) &&
                !is_null($member->getEmail()) &&
                $member->getEmail() != ''
            ) {
                try {
                    $result = $SesClient->sendEmail([
                        'Destination' => [
                            'ToAddresses' => [$member->getEmail()],
                        ],
                        'ReplyToAddresses' => ['jasenrekisteri@jyps.fi'],
                        'Source' => 'jasenrekisteri@jyps.fi',
                        'Message' => [
                            'Body' => [
                                'Text' => [
                                    'Charset' => 'UTF-8',
                                    'Data' => $this->renderView(
                                        'JYPSRegisterBundle:MemberFee:memberfee_email.txt.twig',
                                        [
                                            'member' => $member,
                                            'memberfee' => $memberfee,
                                            'bankaccount' => $bankaccount,
                                            'virtualbarcode' => $memberfee->getVirtualBarcode(
                                                $bankaccount
                                            ),
                                            'year' => date('Y'),
                                        ]
                                    ),
                                ],
                            ],
                            'Subject' => [
                                'Charset' => 'UTF-8',
                                'Data' =>
                                    'JYPS ry:n jäsenmaksu vuodelle ' .
                                    date('Y'),
                            ],
                        ],
                    ]);
                    $messageId = $result['MessageId'];
                } catch (AwsException $e) {
                    // output error message if fails
                    echo $e->getMessage();
                    echo 'The email was not sent. Error message: ' .
                        $e->getAwsErrorMessage() .
                        "\n";
                    echo "\n";
                }

                $memberfee->setEmailSent(1);
                $em->flush($memberfee);

                $sent++;
            } else {
                $errors++;
            }
        }
        $this->get('session')
            ->getFlashBag()
            ->add(
                'notice',
                'Jäsenmaksut lähetetty, OK:' . $sent . ' NOK:' . $errors . ''
            );
        return $this->redirect($this->generateUrl('memberfees'));
    }
    public function memberFeePaymentFormAction(Request $request, $reference)
    {
        $memberfee = $this->getDoctrine()
            ->getRepository('JYPSRegisterBundle:MemberFee')
            ->findOneBy(['reference_number' => $reference]);
        if ($memberfee->getPaid()) {
            return $this->render(
                'JYPSRegisterBundle:MemberFee:payment_already_paid.html.twig'
            );
        }
        $member = $this->getDoctrine()
            ->getRepository('JYPSRegisterBundle:Member')
            ->findOneBy(['id' => $memberfee->getMemberId()]);
        $memberfeeconfig = $member->getMemberType();

        $defaultData = ['message' => 'Type your message here'];

        $form = $this->createForm(MemberFeePayment::class, $defaultData);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // call pt api and redirect to url got from pt
            $data = $form->getData();

            $additional_target = $data['additional_target'];
            $additional_sum = $data['additional_sum'];

            $productArray = [
                [
                    'title' => $member->getMemberId(),
                    'code' => 'JASENMAKSU' + date('Y'),
                    'amount' => 1,
                    'price' => $memberfee->getFeeAmountWithVat(),
                    'vat' => 0,
                    'discount' => 0,
                    'type' => 1,
                ],
            ];

            if (
                $additional_target !== 'EiTukimaksua' &&
                $additional_sum !== 0
            ) {
                array_push($productArray, [
                    'title' => $member->getMemberId(),
                    'code' => $additional_target,
                    'amount' => 1,
                    'price' => $additional_sum,
                    'vat' => 0,
                    'discount' => 0,
                    'type' => 1,
                ]);
            }

            $paytrailRequest = [
                'orderNumber' => $reference,
                'currency' => 'EUR',
                'locale' => 'fi_FI',
                'urlSet' => [
                    'success' => $this->GetSystemParameter(
                        'PaymentCompleteURL'
                    )->getStringValue(),
                    'failure' => $this->GetSystemParameter(
                        'PaymentCancelledURL'
                    )->getStringValue(),
                    'pending' => $this->GetSystemParameter(
                        'PaymentCompleteURL'
                    )->getStringValue(),
                    'notification' => $this->GetSystemParameter(
                        'PaymentCompleteURL'
                    )->getStringValue(),
                ],
                'orderDetails' => [
                    'includeVat' => 1,
                    'contact' => [
                        'telephone' => $member->getTelephone(),
                        'mobile' => $member->getTelephone(),
                        'email' => $member->getEmail(),
                        'firstName' => $member->getFirstName(),
                        'lastName' => $member->getSurname(),
                        'companyName' => '',
                        'address' => [
                            'street' => $member->getStreetAddress(),
                            'postalCode' => $member->getPostalCode(),
                            'postalOffice' => $member->getPostalCode(),
                            'country' => 'FI',
                        ],
                    ],
                    'products' => $productArray,
                ],
            ];

            $data = json_encode($paytrailRequest);
            $client = new GuzzleClient();
            $res = $client->request(
                'POST',
                'https://payment.paytrail.com/api-payment/create',
                [
                    'auth' => [
                        $this->GetSystemParameter(
                            'PaytrailMerchantId'
                        )->getStringValue(),
                        $this->GetSystemParameter(
                            'PaytrailMerchantAuthCode'
                        )->getStringValue(),
                    ],
                    'body' => $data,
                    'headers' => [
                        'Content-Type' => 'application/json',
                        'X-Verkkomaksut-Api-Version' => '1',
                    ],
                ]
            );

            $ptrestponse = json_decode($res->getBody());

            return $this->redirect($ptrestponse->{'url'});
        }
        return $this->render(
            'JYPSRegisterBundle:MemberFee:paytrail_payment.html.twig',
            [
                'form' => $form->createView(),
                'memberfee' => $memberfee,
                'member' => $member,
                'memberfeeconfig' => $memberfeeconfig,
                'change_allowed_from' => $memberfeeconfig->getChangeAllowedFrom(),
            ]
        );
    }

    public function paymentCompleteAction(Request $request)
    {
        $ordernumber = $request->query->get('ORDER_NUMBER');
        $return_auth = $request->query->get('RETURN_AUTHCODE');
        $timestamp = $request->query->get('TIMESTAMP');
        $payment_method = $request->query->get('METHOD');
        $payment_transaction_id = $request->query->get('PAID');
        $check_hash = strtoupper(
            md5(
                $ordernumber .
                    '|' .
                    $timestamp .
                    '|' .
                    $payment_transaction_id .
                    '|' .
                    $payment_method .
                    '|' .
                    $this->GetSystemParameter(
                        'PaytrailMerchantAuthCode'
                    )->getStringValue()
            )
        );

        if ($check_hash == $return_auth) {
            $fee = $this->getDoctrine()
                ->getRepository('JYPSRegisterBundle:MemberFee')
                ->findOneBy(['reference_number' => $ordernumber]);
            $fee->setPaid(true);
            $em = $this->getDoctrine()->getManager();
            $em->persist($fee);
            $em->flush();
            return $this->render(
                'JYPSRegisterBundle:MemberFee:paytrail_payment_completed.html.twig'
            );
        } else {
            return $this->render(
                'JYPSRegisterBundle:MemberFee:paytrail_payment_failed.html.twig',
                ['return_auth' => $return_auth]
            );
        }
    }
    public function paymentCancelledAction(Request $request)
    {
        return $this->render(
            'JYPSRegisterBundle:MemberFee:paytrail_payment_failed.html.twig'
        );
    }
    private function makeMemberCard($member)
    {
        $baseimage = $this->get('kernel')->locateResource(
            '@JYPSRegisterBundle/Resources/public/images/JYPS_Jasenkortti.png'
        );
        $font = $this->get('kernel')->locateResource(
            '@JYPSRegisterBundle/Resources/public/fonts/LucidaGrande.ttf'
        );
        $card_image = $this->get('kernel')->locateResource(
            '@JYPSRegisterBundle/Resources/savedCards/'
        );

        return MemberCardGenerator::generateMembershipCard(
            $member,
            $baseimage,
            $font,
            $card_image
        );
    }
    private function getSystemParameter($parameter_name)
    {
        $value = $this->getDoctrine()
            ->getRepository('JYPSRegisterBundle:SystemParameter')
            ->findOneBy(['key' => $parameter_name]);
        return $value;
    }
}
