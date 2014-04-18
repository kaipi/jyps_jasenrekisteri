<?php
// src/JYPS/RegisterBundle/Controller/MemberController.php;
namespace JYPS\RegisterBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\SecurityContext;
use JYPS\RegisterBundle\Entity\Member;
use JYPS\RegisterBundle\Entity\MemberFee;
use JYPS\RegisterBundle\Entity\Intrest;
use JYPS\RegisterBundle\Entity\MemberFeeConfig;
use JYPS\RegisterBundle\Form\Type\MemberJoinType;
use JYPS\RegisterBundle\Form\Type\MemberAddType;
use JYPS\RegisterBundle\Form\Type\MemberEditType;
use Doctrine\ORM\EntityRepository;

class MemberController extends Controller 
{
 public function indexAction()
 {
  $repository = $this->getDoctrine()
   ->getRepository('JYPSRegisterBundle:Member');

  $query = $repository->createQueryBuilder('m')
    ->where('m.membership_end_date >= :current_date')
    ->setParameter('current_date', new \DateTime("now") )
    ->orderBy('m.member_id', 'ASC')
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
      $testimonial = $em->getRepository('JYPSRegisterBundle:Member')->find($memberid);
        $form->submit($request);

        if ($form->isValid()) {
            $em->flush();

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

 /* $memberfee_confs = $this->getDoctrine()
  ->getManager()
  ->getRepository('JYPSRegisterBundle:MemberFeeConfig');
*/

$memberfee_confs = $this->getDoctrine()
    ->getRepository('JYPSRegisterBundle:MemberFeeConfig')
    ->findBy(array('show_on_join_form' => 0),
             array('membertype' => 'ASC'));

  $form = $this->createForm(new MemberJoinType(), $member, array('action' => $this->generateUrl('join_save'),
   'intrest_configs' => $all_confs,
   'memberfee_configs' => $memberfee_confs,));

  return $this->render('JYPSRegisterBundle:Member:join_member.html.twig', array(
   'form' => $form->createView(),
   ));

}
public function joinSaveAction(Request $request) 
{

 $member = new Member();
 
 $temp = $request->request->get('memberid');
 if(isset($temp['membertype'])) {
  $membertype = $temp['membertype'];
} 
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
$member->setMemberType($membertype);
$member->setMembershipEndDate(new \DateTime("2054-12-31"));
 //create memberfee
$memberFeeConfig = $this->getDoctrine()
->getRepository('JYPSRegisterBundle:MemberFeeConfig')
->findOneBy(array('membertype' => $membertype));
$memberfee = new MemberFee();
$memberfee->setFeeAmountWithVat($memberFeeConfig->getMemberfeeAmount());
$memberfee->setReferenceNumber(date("Y").$member->getMemberId());
$memberfee->setDueDate(new \DateTime("now"));
$memberfee->setMemberFee($member);

if(!empty($intrests)) {
 foreach($intrests as $intrest) {
   $new_intrest = new Intrest();
   $new_intrest->setIntrestId($intrest);
   $new_intrest->setIntrest($member);
 }
}

$form = $this->createForm(new MemberJoinType, $member);
 

/*data = $request->request->all();

print("REQUEST DATA<br/>");
foreach ($data as $k => $d) {
    print("$k: <pre>"); print_r($d); print("</pre>");
}

$children = $form->all();

print("<br/>FORM CHILDREN<br/>");
foreach ($children as $ch) {
    print($ch->getName() . "<br/>");
}

$data = array_diff_key($data, $children);
//$data contains now extra fields

print("<br/>DIFF DATA<br/>");
foreach ($data as $k => $d) {
    print("$k: <pre>"); print_r($d); print("</pre>");
}

*/

$form->handleRequest($request);
//print $form->getErrorsAsString();
if ($form->isValid()) {

  $em = $this->getDoctrine()->getManager();
  $em->persist($member);
  $em->persist($memberfee);
  if(!empty($intrests)) {
    $em->persist($new_intrest);
  }
  $em->flush();
  $bankaccount = $this->getDoctrine()
  ->getRepository('JYPSRegisterBundle:SystemParameter')
  ->findOneBy(array('key' => 'BankAccount'));

  $virtualbarcode = "4".substr($bankaccount->getStringValue(),6,strlen($bankaccount->getStringValue())).str_pad($memberfee->getFeeAmountWithVat(),strlen($memberfee->getFeeAmountWithVat())-6,'0',STR_PAD_LEFT).
                    '00'.'000'.date_format($memberfee->getDueDate(),'ymd');
  //Send mail here, if user exits confirmation page too fast no mail is sent.
  //1) List join
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
  ->setBody($this->renderView(
    'JYPSRegisterBundle:Member:join_member_email_base.txt.twig',
    array('member'=>$member,
          'memberfee'=>$memberfee,
          'bankaccount'=>$bankaccount,
          'virtualbarcode'=>$virtualbarcode)));

  $this->get('mailer')->send($message);
  
  return $this->render('JYPSRegisterBundle:Member:join_member_complete.html.twig');
  
}
return $this->render('JYPSRegisterBundle:Member:join_member_failed.html.twig');
}


public function joinSaveInternalAction(Request $request) 
{

 $member = new Member();
 
 $temp = $request->request->get('memberid');
 if(isset($temp['membertype'])) {
  $membertype = $temp['membertype'];
} 
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
$member->setMemberType($membertype);
$member->setMembershipEndDate(new \DateTime("2054-12-31"));
 //create memberfee
$memberFeeConfig = $this->getDoctrine()
->getRepository('JYPSRegisterBundle:MemberFeeConfig')
->findOneBy(array('membertype' => $membertype));
$memberfee = new MemberFee();
$memberfee->setFeeAmountWithVat($memberFeeConfig->getMemberfeeAmount());
$memberfee->setReferenceNumber(date("Y").$member->getMemberId());
$memberfee->setDueDate(new \DateTime("now"));
$memberfee->setMemberFee($member);

if(!empty($intrests)) {
 foreach($intrests as $intrest) {
   $new_intrest = new Intrest();
   $new_intrest->setIntrestId($intrest);
   $new_intrest->setIntrest($member);
 }
}

$form = $this->createForm(new MemberJoinType, $member);
 

/*data = $request->request->all();

print("REQUEST DATA<br/>");
foreach ($data as $k => $d) {
    print("$k: <pre>"); print_r($d); print("</pre>");
}

$children = $form->all();

print("<br/>FORM CHILDREN<br/>");
foreach ($children as $ch) {
    print($ch->getName() . "<br/>");
}

$data = array_diff_key($data, $children);
//$data contains now extra fields

print("<br/>DIFF DATA<br/>");
foreach ($data as $k => $d) {
    print("$k: <pre>"); print_r($d); print("</pre>");
}

*/

$form->handleRequest($request);
//print $form->getErrorsAsString();
if ($form->isValid()) {

  $em = $this->getDoctrine()->getManager();
  $em->persist($member);
  $em->persist($memberfee);
  if(!empty($intrests)) {
    $em->persist($new_intrest);
  }
  $em->flush();

  $bankaccount = $this->getDoctrine()
  ->getRepository('JYPSRegisterBundle:SystemParameter')
  ->findOneBy(array('key' => 'BankAccount'));

  $virtualbarcode = "4".substr($bankaccount->getStringValue(),6,strlen($bankaccount->getStringValue())).str_pad($memberfee->getFeeAmountWithVat(),strlen($memberfee->getFeeAmountWithVat())-6,'0',STR_PAD_LEFT).
                    '00'.'000'.date_format($memberfee->getDueDate(),'ymd');
  //Send mail here, if user exits confirmation page too fast no mail is sent.
  //1) List join
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
  ->setBody($this->renderView(
    'JYPSRegisterBundle:Member:join_member_email_internal_base.txt.twig',
    array('member'=>$member,
          'memberfee'=>$memberfee,
          'bankaccount'=>$bankaccount,
          'virtualbarcode'=>$virtualbarcode)));

  $this->get('mailer')->send($message);
  
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
  ->where('m.firstname LIKE :search_term OR m.surname LIKE :search_term AND m.membership_end_date > :current_date')
  ->setParameter('search_term',"%$search_term%")
  ->setParameter('current_date', new \DateTime("now") )
  ->getQuery();

  $members = $query->getResult();

  return $this->render('JYPSRegisterBundle:Member:show_members_search.html.twig', array('members' => $members));
}
public function updateMemberAction(Request $request) 
{

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
}
