<?php

namespace App\Service;

use App\Entity\Reservation;
use Swift_Mailer;
use Swift_Message;
use Swift_Image;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;

/**
 * Class MailService
 * @package App\Service
 */
class MailService {

    /**
     * @var ParameterBagInterface
     */
    private $_params;
    /**
     * @var Swift_Mailer
     */
    private $_mailer;
    /**
     * @var \Twig_Environment
     */
    private $_templating;
    /**
     * @var QRCodeService
     */
    private $_qrCodeService;

    /**
     * MailService constructor.
     * @param ParameterBagInterface $parameterBag
     * @param Swift_Mailer $mailer
     * @param QRCodeService $QRCodeService
     * @param \Twig_Environment $templating
     */
    public function __construct(ParameterBagInterface $parameterBag, Swift_Mailer $mailer, QRCodeService $QRCodeService, \Twig_Environment $templating){
        $this->_params = $parameterBag;
        $this->_mailer = $mailer;
        $this->_qrCodeService = $QRCodeService;
        $this->_templating = $templating;
    }

    /**
     * @param Reservation $reservation
     * @return Swift_Message
     */
    public function createMail(Reservation $reservation){
        $message = new Swift_Message();
        $qrCode = $this->_qrCodeService->createQRCode($reservation->getReservationCode());
        $imgUrl = $message->embed(Swift_Image::fromPath('img/logo.png'));
        $qrImg = $message->embed(Swift_Image::fromPath('data:'.$qrCode->getContentType().';base64,'.$qrCode->generate()));
        try {
            $message
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
        return $message;
    }

    public function sendMail(Swift_Message $message){
        $this->_mailer->send($message);
    }

}
