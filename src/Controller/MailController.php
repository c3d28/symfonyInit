<?php

namespace App\Controller\Service;

use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Email;

define("SENDER", "inballfr@gmail.com");


class MailerController extends AbstractController
{
    /**
    * @Route("/email")
    */
    public function sendEmail(MailerInterface $mailer,String $receiver,String $subject)
    {
        $email = (new Email())
        ->from(new NamedAddress(SENDER, 'Mailtrap'))
        ->to($receiver)
        ->subject($subject)
        ->text('Hey! Learn the best practices of building HTML emails and play with ready-to-go templates. Mailtrap’s Guide on How to Build HTML Email is live on our blog')
        ->html('<html>
        <body>
        <p><br>Hey</br>
            Learn the best practices of building HTML emails and play with ready-to-go templates.</p>
        <p><a href="https://blog.mailtrap.io/build-html-email/">Mailtrap’s Guide on How to Build HTML Email</a> is live on our blog</p>
        <img src="cid:logo"> ... <img src="cid:new-cover-image">
        </body>
        </html>');

        $mailer->send($email);
    }
}
