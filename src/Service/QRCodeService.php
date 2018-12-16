<?php

namespace App\Service;

use CodeItNow\BarcodeBundle\Utils\QrCode;

class QRCodeService {

    private $_qrCode;

    public function __construct(){
        $this->_qrCode = new QrCode();
    }

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
