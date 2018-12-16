<?php

namespace App\Service;

use CodeItNow\BarcodeBundle\Utils\QrCode;

/**
 * Class QRCodeService
 * @package App\Service
 */
class QRCodeService {

    /**
     * @var QrCode
     */
    private $_qrCode;

    /**
     * QRCodeService constructor.
     */
    public function __construct(){
        $this->_qrCode = new QrCode();
    }

    /**
     * Transform reservation Code in QRCode
     * @param $reservationCode
     * @return QrCode
     */
    public function createQRCode($reservationCode){
        try {
            $this->_qrCode
                ->setText($reservationCode)
                ->setSize(300)
                ->setPadding(10)
                ->setErrorCorrection('high')
                ->setForegroundColor(array('r' => 0, 'g' => 0, 'b' => 0, 'a' => 0))
                ->setBackgroundColor(array('r' => 255, 'g' => 255, 'b' => 255, 'a' => 0))
                ->setLabelFontSize(16)
                ->setImageType(QrCode::IMAGE_TYPE_PNG);
        } catch (\Exception $e) {

        }
        return $this->_qrCode;
    }

}
