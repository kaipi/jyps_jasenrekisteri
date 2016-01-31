<?php
namespace JYPS\RegisterBundle\Controller;

use Endroid\QrCode\QrCode;
use JYPS\RegisterBundle\Entity\Member;

class MemberCardGenerator {

	public static function generateMembershipCard(Member $member, $base_image_path, $font, $image_path) {
		$base_image = imagecreatefrompng($base_image_path);
		$output_image = $image_path . 'MemberCard_' . $member->getMemberId() . '.png';
		/* member data to image */
		$black = imagecolorallocate($base_image, 0, 0, 0);
		$memberid = $member->getMemberId();
		$join_year = $member->getMembershipStartDate()->format('Y');
		imagettftext($base_image, 38, 0, 190, 500, $black, $font, $member->getFullName());
		imagettftext($base_image, 38, 0, 390, 555, $black, $font, $memberid);
		imagettftext($base_image, 38, 0, 390, 610, $black, $font, date('Y'));
		/*qr code to image & serialize json for qr code*/
		$member_data = array('member_id' => $member->getMemberId(),
			'join_year' => $member->getMembershipStartDate()->format('Y'),
			'name' => $member->getFullName());
		$member_qr_data = json_encode($member_data);
		$qrCode = new QrCode();
		$qrCode->setSize(380);
		$qrCode->setText($member_qr_data);
		$qrCode = $qrCode->get('png');
		$qr_image = imagecreatefromstring($qrCode);
		imagecopy($base_image, $qr_image, 550, 22, 0, 0, imagesx($qr_image), imagesy($qr_image));
		/*write image to disk */
		imagepng($base_image, $output_image);
		return $output_image;
	}
}
