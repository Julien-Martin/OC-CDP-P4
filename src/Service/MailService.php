<?php

namespace App\Service;

use App\Entity\Reservation;
use Swift_Mailer;
use Swift_Message;
use Swift_Image;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;

class MailService {

    private $_params;
    private $_mailer;
    private $_templating;
    private $_qrCodeService;
    private $_message;
    private $_qrCode;

    public function __construct(ParameterBagInterface $parameterBag, Swift_Mailer $mailer, QRCodeService $QRCodeService, \Twig_Environment $templating){
        $this->_params = $parameterBag;
        $this->_mailer = $mailer;
        $this->_qrCodeService = $QRCodeService;
        $this->_templating = $templating;
    }

    public function createMail(Reservation $reservation){
        $this->_message = new Swift_Message();
        $this->_qrCode = $this->_qrCodeService->createQRCode($reservation->getReservationCode());
        $imgUrl = $this->_message->embed(Swift_Image::fromPath('img/logo.png'));
        $qrImg = $this->_message->embed(Swift_Image::fromPath('data:'.$this->_qrCode->getContentType().';base64,'.$this->_qrCode->generate()));
        try {
            $this->_message
                ->setFrom($this->_params->get('email'))
                ->setTo($reservation->getEmail())
                ->setSubject('Billet pour le Musée du Louvre')
                ->setBody($this->_templating->render('payment/email.html.twig', [
                    'reservation' => $reservation,
                    'logo' => $imgUrl,
                    'qrImg' => $qrImg
                ]), 'text/html');
        } catch (\Twig_Error_Loader $e) {
        } catch (\Twig_Error_Runtime $e) {
        } catch (\Twig_Error_Syntax $e) {
        }
        return $this->_message;
    }

    public function sendMail(Swift_Message $message){
        $this->_mailer->send($message);
    }

}
