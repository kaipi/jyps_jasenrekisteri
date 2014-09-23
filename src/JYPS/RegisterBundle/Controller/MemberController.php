<?php
// src/JYPS/RegisterBundle/Controller/MemberController.php;
namespace JYPS\RegisterBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\SecurityContext;
use JYPS\RegisterBundle\Entity\Member;
use JYPS\RegisterBundle\Entity\MemberFee;
use JYPS\RegisterBundle\Entity\Intrest;
use JYPS\RegisterBundle\Entity\IntrestConfig;
use JYPS\RegisterBundle\Entity\MemberFeeConfig;
use JYPS\RegisterBundle\Form\Type\MemberJoinType;
use JYPS\RegisterBundle\Form\Type\MemberAddType;
use JYPS\RegisterBundle\Form\Type\MemberEditType;
use Doctrine\ORM\EntityRepository;
use Endroid\QrCode\QrCode;

class MemberController extends Controller 
{
 public function indexAction()
 {
  $repository = $this->getDoctrine()
   ->getRepository('JYPSRegisterBundle:Member');

  $query = $repository->createQueryBuilder('m')
    ->where('m.membership_end_date >= :current_date')
    ->setParameter('current_date', new \DateTime("now") )
    ->orderBy('m.surname', 'ASC')
    ->getQuery();
  
  $members = $query->getResult();

  return $this->render('JYPSRegisterBundle:Member:show_members.html.twig', array('members' => $members));
}

 public function showClosedAction()
 {
 $repository = $this->getDoctrine()
   ->getRepository('JYPSRegisterBundle:Member');

  $query = $repository->createQueryBuilder('m')
    ->where('m.membership_end_date <= :current_date')
    ->setParameter('current_date', new \DateTime("now") )
    ->orderBy('m.member_id', 'ASC')
    ->getQuery();
  
  $members = $query->getResult();

  return $this->render('JYPSRegisterBundle:Member:show_members_old.html.twig', array('members' => $members));
}


public function showAllAction($memberid)
{

  $request = $this->get('request');
  
  if (is_null($memberid)) {
        $postData = $request->get('member');
        $memberid = $postData['memberid'];
  }

  $member = $this->getDoctrine()
  ->getRepository('JYPSRegisterBundle:Member')
  ->findOneBy(array('member_id' => $memberid));

  if (!$member) {
    throw $this->createNotFoundException(
      'No member found for memberid '.$memberid
      );
  }

  $memberfees = $this->getDoctrine()
    ->getRepository('JYPSRegisterBundle:MemberFee')
    ->findBy(array('member_id' => $member->getId()),
             array('fee_period' => 'ASC'));
  
  $form = $this->createForm(new MemberEditType(), $member, array('action' => $this->generateUrl('member', array('memberid' => $member->getMemberId())),
  ));

    if ($request->getMethod() == 'POST') {
      $em = $this->getDoctrine()->getEntityManager();

        $form->submit($request);

        if ($form->isValid()) {
           $em->flush();
           $fees = $request->get('Fees_to_be_marked');
           $member_all_fees = $member->getMemberFees();
           if($fees != "") {
             foreach($member_all_fees as $member_fee) {
                if(in_array($member_fee->getId(), $fees)) {
                   $markfee = $this->getDoctrine()
                    ->getRepository('JYPSRegisterBundle:MemberFee')
                    ->findOneBy(array('id' => $member_fee->getId(),));
                  $markfee->setPaid(True);
                  $em->flush($markfee);
                } 
                else {
                   $markfee = $this->getDoctrine()
                    ->getRepository('JYPSRegisterBundle:MemberFee')
                    ->findOneBy(array('id' => $member_fee->getId(),));
                  $markfee->setPaid(False);
                  $em->flush($markfee);
                }
             }
           }
           else {
              foreach($member_all_fees as $member_fee) {
                   $markfee = $this->getDoctrine()
                    ->getRepository('JYPSRegisterBundle:MemberFee')
                    ->findOneBy(array('id' => $member_fee->getId(),));
                  $markfee->setPaid(False);
                  $em->flush($markfee);
              
              }
           }       
         
         return $this->redirect($this->generateUrl('member',array('memberid' => $memberid)));
        }
    }
  return $this->render('JYPSRegisterBundle:Member:show_member.html.twig', array('member' => $member,
    'memberfees' => $memberfees,
    'form' => $form->createView(),
    ));
 
}
public function addMemberAction() 
{
  $member = new Member();

  $all_confs = $this->getDoctrine()
  ->getManager()
  ->getRepository('JYPSRegisterBundle:IntrestConfig');

  $memberfee_confs = $this->getDoctrine()
  ->getManager()
  ->getRepository('JYPSRegisterBundle:MemberFeeConfig');

  $form = $this->createForm(new MemberAddType(), $member, array('action' => $this->generateUrl('join_internal_save'),
   'intrest_configs' => $all_confs,
   'memberfee_configs' => $memberfee_confs,));

  return $this->render('JYPSRegisterBundle:Member:add_member.html.twig', array(
   'form' => $form->createView(),
   ));
}

public function joinMemberAction(Request $request) 
{

  $member = new Member();

  $all_confs = $this->getDoctrine()
  ->getManager()
  ->getRepository('JYPSRegisterBundle:IntrestConfig');

  $memberfee_confs = $this->getDoctrine()
  ->getManager()
  ->getRepository('JYPSRegisterBundle:MemberFeeConfig');

  $form = $this->createForm(new MemberJoinType(), $member, array('action' => $this->generateUrl('join_save'),
   'intrest_configs' => $all_confs,
   'memberfee_configs' => $memberfee_confs,));

  return $this->render('JYPSRegisterBundle:Member:join_member.html.twig', array(
   'form' => $form->createView(),
   ));

}

private function generate_membership_card(Member $member) 
{
 
  $base_image_path = $this->get('kernel')->locateResource('@JYPSRegisterBundle/Resources/public/images/JYPS_Jasenkortti.png');
  $base_image = imagecreatefrompng($base_image_path);
  $output_image = $this->get('kernel')->locateResource('@JYPSRegisterBundle/Resources/savedCards/').'MemberCard_'.$member->getMemberId().'.png';
  
  /* member data to image */
  $width = imagesx($base_image);
  $height = imagesy($base_image);
  $black = imagecolorallocate($base_image, 0, 0, 0);
  $memberid = $member->getMemberId();
  $join_year = $member->getMembershipStartDate()->format('Y');
  $font = $this->get('kernel')->locateResource('@JYPSRegisterBundle/Resources/public/fonts/LucidaGrande.ttf');
  
  imagettftext($base_image, 38, 0, 390, 505, $black, $font, $memberid);
  imagettftext($base_image, 38, 0, 390, 570, $black, $font, $join_year);
  
  /*qr code to image & serialize json for qr code*/ 
  $member_data = array('member_id'=>$member->getMemberId(),
                       'join_year'=>$member->getMembershipStartDate()->format('Y'),
                       'name'=>$member->getFullName());
  $member_qr_data = json_encode($member_data);

  $qrCode = new QrCode();
  $qrCode->setSize(380);
  $qrCode->setText($member_qr_data);
  $qrCode = $qrCode->get('png');
  $qr_image = imagecreatefromstring($qrCode);
  imagecopy($base_image,$qr_image,550,22,0,0,imagesx($qr_image),imagesy($qr_image));
  /*write image to disk */
  imagepng($base_image,$output_image);
  
  return $output_image;
}

public function send_join_info_mail(Member $member, MemberFee $memberfee) 
{
  $intrest_names = array();
  if($member->getIntrests()) {
    foreach($member->getIntrests() as $intrest) {
      $intrest_config = $this->getDoctrine()
      ->getRepository('JYPSRegisterBundle:IntrestConfig')
      ->findOneBy(array('id' => $intrest->getIntrestId())); 
      array_push($intrest_names,$intrest_config->getIntrestname());
    }
  }
  $member_age = date('Y') - $member->getBirthYear();
  $message = \Swift_Message::newInstance()
  ->setSubject('Uusi JYPS-jäsen!')
    ->setFrom('rekisteri@jyps.fi')
    ->setTo(array('pj@jyps.fi','kaisa.m.peltonen@gmail.com','henna.breilin@toivakka.fi'))
    ->setBody($this->renderView(
      'JYPSRegisterBundle:Member:join_member_infomail.txt.twig',
      array('member'=>$member,
            'memberfee'=>$memberfee,
            'intrests'=>$intrest_names,
            'age'=>$member_age)));
  $this->get('mailer')->send($message);
}

public function memberExtraAction()
{
    return $this->render('JYPSRegisterBundle:Member:member_actions.html.twig');

}
public function sendCommunicationMailAction(Request $request) 
{

 $repository = $this->getDoctrine()
   ->getRepository('JYPSRegisterBundle:Member');

  $members = $repository->createQueryBuilder('m')
    ->where('m.membership_end_date <= :ui_date')
    ->setParameter('ui_date', $ui_date)
    ->getQuery();

  foreach($members as $member) {
    $i++;
    $message = \Swift_Message::newInstance()
      ->setSubject($subject)
      ->setFrom('pj@jyps.fi')
      ->setTo(array($member->getEmail()))
      ->setBody($message);
    $this->get('mailer')->send($message);
  }
   $this->get('session')->getFlashBag()->add(
              'notice',
              'Sähköpostit lähetetty '.$i.'kpl');
  return $this->redirect($this->generateUrl('member_actions'));
}

public function sendMagazineLinkAction(Request $request) 
{

 $magazine_url = $request->get('magazine_url');

 $repository = $this->getDoctrine()
   ->getRepository('JYPSRegisterBundle:Member');

  $members = $repository->createQueryBuilder('m')
    ->where('m.membership_end_date >= :current_date AND m.magazine_preference = 1')
    ->setParameter('current_date', new \Datetime("now"))
    ->getQuery();

  foreach($members as $member) {
    $i++;
    $message = \Swift_Message::newInstance()
      ->setSubject("JYPS Ry Jäsenlehti")
      ->setFrom('pj@jyps.fi')
      ->setTo(array($member->getEmail()))
      ->setBody($this->renderView(
      'JYPSRegisterBundle:Member:magazine_link_template.txt.twig',array('magazine_url'=>$magazine_url)));
    
    $this->get('mailer')->send($message);
  }
  $this->get('session')->getFlashBag()->add(
            'notice',
            'Sähköpostit lähetetty '.$i.'kpl');
  
  return $this->redirect($this->generateUrl('member_actions'));

}

public function AddressExcelAction() 
{
  $i = 0;
  $repository = $this->getDoctrine()
   ->getRepository('JYPSRegisterBundle:Member');

  $query = $repository->createQueryBuilder('m')
    ->where('m.membership_end_date >= :today_date AND m.magazine_preference = 0')
    ->setParameter('today_date', new \Datetime("now"))
    ->getQuery();
  $members = $query->getResult();

  $phpExcelObject = $this->get('phpexcel')->createPHPExcelObject();
  $phpExcelObject->getProperties()->setCreator("JYPS Ry Jäsenrekisteri")
           ->setLastModifiedBy("JYPS Ry Jäsenrekisteri")
           ->setTitle("Osoitteet")
           ->setSubject("Osoitteet")
           ->setDescription("Aktiivisten jäsenten osoitetiedot joiden lehden toimitustapa = paperi")
           ->setKeywords("")
           ->setCategory("");
 
  foreach($members as $member) {
    $i++;
    $phpExcelObject->getActiveSheet()->setCellValueByColumnAndRow(0, $i, $member->getFirstName());
    $phpExcelObject->getActiveSheet()->setCellValueByColumnAndRow(1, $i, $member->getSurname());
    $phpExcelObject->getActiveSheet()->setCellValueByColumnAndRow(2, $i, $member->getStreetAddress());
    $phpExcelObject->getActiveSheet()->setCellValueByColumnAndRow(3, $i, $member->getPostalCode());
    $phpExcelObject->getActiveSheet()->setCellValueByColumnAndRow(4, $i, $member->getCity());

  }
  $phpExcelObject->getActiveSheet()->setTitle('Osoitteet');
  // Set active sheet index to the first sheet, so Excel opens this as the first sheet
  $phpExcelObject->setActiveSheetIndex(0);
  // create the writer
  $writer = $this->get('phpexcel')->createWriter($phpExcelObject, 'Excel5');
  // create the response
  $response = $this->get('phpexcel')->createStreamedResponse($writer);
  // adding headers
  $response->headers->set('Content-Type', 'text/vnd.ms-excel; charset=utf-8');
  $response->headers->set('Content-Disposition', 'attachment;filename=jyps_osoitteet.xls');
  $response->headers->set('Pragma', 'public');
  $response->headers->set('Cache-Control', 'maxage=1');
  
  $this->get('session')->getFlashBag()->add(
             'notice',
             'Excel taulukko tuotu!');
 return $response;  

}

public function joinSaveAction(Request $request) 
{

 $member = new Member();
 
 $temp = $request->request->get('memberid');
 
if(isset($temp['intrests'])) {
  $intrests = $temp['intrests'];
}
//this is not good, please refactor :)
$repository = $this->getDoctrine()
->getRepository('JYPSRegisterBundle:Member');
$query = $repository->createQueryBuilder('m')
->select('MAX(m.member_id) AS max_memberid');
$maxmemberid = $query->getQuery()->getResult();
$temparr =  $maxmemberid[0];
$maxmemberid = $temparr['max_memberid'];
$maxmemberid++;

 //extra params for member
$member->setMemberid($maxmemberid);  

$member->setMembershipEndDate(new \DateTime("2038-12-31"));

if(!empty($intrests)) {
 foreach($intrests as $intrest) {
   $new_intrest = new Intrest();
   $new_intrest->setIntrestId($intrest);
   $new_intrest->setIntrest($member);
   $member->addIntrest($new_intrest);
 }
}

$form = $this->createForm(new MemberJoinType, $member);

$form->handleRequest($request);

if ($form->isValid()) {
  $membership_card = $this->generate_membership_card($member);

  //create memberfee
  $memberfee = new MemberFee();
  $memberfee->setFeeAmountWithVat($member->getMemberType()->getMemberfeeAmount());
  $memberfee->setReferenceNumber(date("Y").$member->getMemberId());
  $memberfee->setDueDate(new \DateTime("now"));
  $memberfee->setMemberFee($member);

  $em = $this->getDoctrine()->getManager();
  $em->persist($member);
  $em->persist($memberfee);
  $em->flush();
  $bankaccount = $this->getDoctrine()
  ->getRepository('JYPSRegisterBundle:SystemParameter')
  ->findOneBy(array('key' => 'BankAccount'));

  $virtualbarcode = "4".substr($bankaccount->getStringValue(),6,strlen($bankaccount->getStringValue())).str_pad($memberfee->getFeeAmountWithVat(),strlen($memberfee->getFeeAmountWithVat())-6,'0',STR_PAD_LEFT).
                    '00'.'000'.date_format($memberfee->getDueDate(),'ymd');
  
  //Send mail here, if user exits confirmation page too fast no mail is sent.
  //1) List join
  if($member->getEmail() != "") {
    if($member->getMailingListYleinen() == True) {
      $message = \Swift_Message::newInstance()
      ->setFrom($member->getEmail())
      ->setTo('yleinen-join@jyps.info');
      $this->get('mailer')->send($message);
    }
    //2) information mail 
    $message = \Swift_Message::newInstance()
    ->setSubject('Tervetuloa JYPS Ry:n jäseneksi')
    ->setFrom('pj@jyps.fi')
    ->setTo($member->getEmail())
    ->attach(\Swift_Attachment::fromPath($membership_card))
    ->setBody($this->renderView(
      'JYPSRegisterBundle:Member:join_member_email_base.txt.twig',
      array('member'=>$member,
            'memberfee'=>$memberfee,
            'bankaccount'=>$bankaccount,
            'virtualbarcode'=>$virtualbarcode)));

    $this->get('mailer')->send($message);
    }
    
    $this->send_join_info_mail($member, $memberfee);

    return $this->redirect($this->generateUrl('join_complete'),303);
}
return $this->render('JYPSRegisterBundle:Member:join_member_failed.html.twig');
}

public function joinCompleteAction() {
  return $this->render('JYPSRegisterBundle:Member:join_member_complete.html.twig');
}

public function joinSaveInternalAction(Request $request) 
{

 $member = new Member();
 
 $temp = $request->request->get('memberid');

if(isset($temp['intrests'])) {
  $intrests = $temp['intrests'];
}
//this is not good, please refactor :)
$repository = $this->getDoctrine()
->getRepository('JYPSRegisterBundle:Member');
$query = $repository->createQueryBuilder('m')
->select('MAX(m.member_id) AS max_memberid');
$maxmemberid = $query->getQuery()->getResult();
$temparr =  $maxmemberid[0];
$maxmemberid = $temparr['max_memberid'];
$maxmemberid++;

 //extra params for member
$member->setMemberid($maxmemberid);  

$member->setMembershipEndDate(new \DateTime("2038-12-31"));

if(!empty($intrests)) {
 foreach($intrests as $intrest) {
   $new_intrest = new Intrest();
   $new_intrest->setIntrestId($intrest);
   $new_intrest->setIntrest($member);
   $member->setIntrest($new_intrest);
 }
}

$form = $this->createForm(new MemberAddType, $member);
 
$form->handleRequest($request);

if ($form->isValid()) {
  $membership_card = $this->generate_membership_card($member);
  
  //create memberfee
  $memberfee = new MemberFee();
  $memberfee->setFeeAmountWithVat($member->getMemberType()->getMemberfeeAmount());
  $memberfee->setReferenceNumber(date("Y").$member->getMemberId());
  $memberfee->setDueDate(new \DateTime("now"));
  $memberfee->setMemberFee($member);

  $memberFeeConfig = $this->getDoctrine()
  ->getRepository('JYPSRegisterBundle:MemberFeeConfig')
  ->findOneBy(array('member_type' => $member->getMemberType()));
    
  $send_mail_without_payment_info = False;

  if($memberFeeConfig->getCampaignFee() == True) {
    $send_mail_without_payment_info = True;

    $realMemberFeeConfig =  $this->getDoctrine()
      ->getRepository('JYPSRegisterBundle:MemberFeeConfig')
      ->findOneBy(array('member_type' => $memberFeeConfig->getRealMemberType()));

    $member->setMemberType($realMemberFeeConfig);
    $memberfee->setMemo("KAMPPIS");
  }
  if(isset($temp['mark_fee_paid'])) {
    if($temp['mark_fee_paid'] == True) {
      $memberfee->setPaid(True);
    }
  }
  $em = $this->getDoctrine()->getManager();
  $em->persist($member);
  $em->persist($memberfee);
  
  $em->flush();

  $bankaccount = $this->getDoctrine()
  ->getRepository('JYPSRegisterBundle:SystemParameter')
  ->findOneBy(array('key' => 'BankAccount'));

  $virtualbarcode = "4".substr($bankaccount->getStringValue(),6,strlen($bankaccount->getStringValue())).str_pad($memberfee->getFeeAmountWithVat(),strlen($memberfee->getFeeAmountWithVat())-6,'0',STR_PAD_LEFT).
                    '00'.'000'.date_format($memberfee->getDueDate(),'ymd');
  //Send mail here, if user exits confirmation page too fast no mail is sent.
  //1) List join
  if($member->getEmail() != "") {
    if($member->getMailingListYleinen() == True) {
      $message = \Swift_Message::newInstance()
      ->setFrom($member->getEmail())
      ->setTo('yleinen-join@jyps.info');
      $this->get('mailer')->send($message);
    }
    //2) information mail
    if($send_mail_without_payment_info == True) {
      $message = \Swift_Message::newInstance()
      ->setSubject('Tervetuloa JYPS Ry:n jäseneksi')
      ->setFrom('pj@jyps.fi')
      ->setTo($member->getEmail())
      ->attach(\Swift_Attachment::fromPath($membership_card))
      ->setBody($this->renderView(
        'JYPSRegisterBundle:Member:join_member_email_internal_base.txt.twig',
        array('member'=>$member,
              'memberfee'=>$memberfee,
              'bankaccount'=>$bankaccount,
              'virtualbarcode'=>$virtualbarcode)));
    } 
    else {            
      $message = \Swift_Message::newInstance()
      ->setSubject('Tervetuloa JYPS Ry:n jäseneksi')
      ->setFrom('pj@jyps.fi')
      ->setTo($member->getEmail())    
      ->attach(\Swift_Attachment::fromPath($membership_card))
      ->setBody($this->renderView(
        'JYPSRegisterBundle:Member:join_member_email_internal_base.txt.twig',
        array('member'=>$member,
              'memberfee'=>$memberfee,
              'bankaccount'=>$bankaccount,
              'virtualbarcode'=>$virtualbarcode)));
    }
    
    $this->get('mailer')->send($message);
  }
  
  $this->send_join_info_mail($member, $memberfee);

  $this->get('session')->getFlashBag()->add(
             'notice',
             'Jäsen lisätty');

  return $this->redirect($this->generateUrl('add_member'));
  
}
return $this->render('JYPSRegisterBundle:Member:join_member_failed.html.twig');
}


public function searchMembersAction()
{
  $search_term = $this->get('request')->request->get('search_name');

  $repository = $this->getDoctrine()
  ->getRepository('JYPSRegisterBundle:Member');

  $query = $repository->createQueryBuilder('m')
  ->where('m.firstname LIKE :search_term OR m.surname LIKE :search_term OR m.city LIKE :search_term OR m.postal_code LIKE :search_term')
  ->andWhere('m.membership_end_date > :current_date')
  ->setParameter('search_term',"%$search_term%")
  ->setParameter('current_date', new \DateTime("now") )
  ->getQuery();

  $members = $query->getResult();

  return $this->render('JYPSRegisterBundle:Member:show_members_search.html.twig', array('members' => $members));
}

public function searchOldMembersAction()
{
  $search_term = $this->get('request')->request->get('search_name');

  $repository = $this->getDoctrine()
  ->getRepository('JYPSRegisterBundle:Member');

  $query = $repository->createQueryBuilder('m')
  ->where('m.firstname LIKE :search_term OR m.surname LIKE :search_term OR m.city LIKE :search_term OR m.postal_code LIKE :search_term')
  ->andWhere('m.membership_end_date < :current_date')
  ->setParameter('search_term',"%$search_term%")
  ->setParameter('current_date', new \DateTime("now") )
  ->getQuery();

  $members = $query->getResult();

  return $this->render('JYPSRegisterBundle:Member:show_members_old.html.twig', array('members' => $members));
}

public function endMemberAction()
{
   $memberid = $this->get('request')->request->get('memberid');

   $em = $this->getDoctrine()->getManager();

   $member = $this->getDoctrine()
  ->getRepository('JYPSRegisterBundle:Member')
  ->findOneBy(array('member_id' => $memberid));
   $enddate = new \DateTime("now");
   $member->setMembershipEndDate($enddate);
   $em->flush();

   return $this->redirect($this->generateUrl('all_members'));

}
public function memberStatisticsAction() 
{
  return $this->render('JYPSRegisterBundle:Member:member_statistics.html.twig');
}

public function restoreMemberAction()
{
   $memberid = $this->get('request')->request->get('memberid');

   $em = $this->getDoctrine()->getManager();

   $member = $this->getDoctrine()
  ->getRepository('JYPSRegisterBundle:Member')
  ->findOneBy(array('member_id' => $memberid));
   $enddate = new \DateTime("2038-12-31");
   $member->setMembershipEndDate($enddate);
   $em->flush();

   return $this->redirect($this->generateUrl('showClosed'));

}
}
