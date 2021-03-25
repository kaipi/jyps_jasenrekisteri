<?php
namespace JYPS\RegisterBundle\Controller;

use Endroid\QrCode\QrCode;
use Endroid\QrCode\ErrorCorrectionLevel\ErrorCorrectionLevelLow;
use Endroid\QrCode\RoundBlockSizeMode\RoundBlockSizeModeMargin;
use JYPS\RegisterBundle\Entity\Member;
use Endroid\QrCode\Writer\PngWriter;
use Endroid\QrCode\Encoding\Encoding;
use Endroid\QrCode\Color\Color;

class MemberCardGenerator
{

    public static function generateMembershipCard(Member $member, $base_image_path, $font, $image_path)
    {
        $writer = new PngWriter();
        $base_image = imagecreatefrompng($base_image_path);
        $output_image = $image_path . 'MemberCard_' . $member->getMemberId() . '.png';
        /* member data to image */
        $black = imagecolorallocate($base_image, 0, 0, 0);
        $memberid = $member->getMemberId();
        imagettftext($base_image, 38, 0, 190, 500, $black, $font, $member->getFullName());
        imagettftext($base_image, 38, 0, 390, 555, $black, $font, $memberid);
        imagettftext($base_image, 38, 0, 390, 610, $black, $font, date('Y'));
        /*qr code to image & serialize json for qr code*/
        $member_data = array('member_id' => $member->getMemberId(),
            'join_year' => $member->getMembershipStartDate()->format('Y'),
            'name' => $member->getFullName());
        $member_qr_data = json_encode($member_data);
        $qrCode = QrCode::create($member_qr_data)
            ->setEncoding(new Encoding('UTF-8'))
            ->setErrorCorrectionLevel(new ErrorCorrectionLevelLow())
            ->setSize(380)
            ->setMargin(10)
            ->setRoundBlockSizeMode(new RoundBlockSizeModeMargin())
            ->setForegroundColor(new Color(0, 0, 0))
            ->setBackgroundColor(new Color(255, 255, 255));

        $result = $writer->write($qrCode);
        $qr_image = imagecreatefromstring($result->getString());
        imagecopy($base_image, $qr_image, 550, 22, 0, 0, imagesx($qr_image), imagesy($qr_image));
        /*write image to disk */
        imagepng($base_image, $output_image);
        return $output_image;
    }
}
