<?php

namespace App\Controller;

use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;


define("SENDER", "inballfr@gmail.com");


class MailController extends AbstractController
{

    public function __construct( \Swift_Mailer $mailer)
    {

        $this->mailer = $mailer;
    }

    /**
    * @Route("/email")
    */
    public function sendEmail(String $receiver,String $subject,String $text)
    {
        $message = (new \Swift_Message($subject))
            ->setFrom(SENDER)
            ->setTo($receiver)
            ->setBody($text);
        $this->mailer->send($message);

        $response = new Response(json_encode(array('value' => "OK")));
        return $response;
    }
}
