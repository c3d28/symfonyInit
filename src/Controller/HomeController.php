<?php

namespace App\Controller;

use App\Entity\Deal;
use App\Entity\User;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Response;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class HomeController extends AbstractController
{
    /**
     * @Route("/home", name="home")
     */
    public function index(EntityManagerInterface $em,UserPasswordEncoderInterface $encoder): Response
    {
    	/**

        $deal = new Deal();
    	$deal->setTitle('Test');
		$deal->setDescription('desc');
    	$em = $this->getDoctrine()->getManager();
    	$em->persist($deal);
    	$em->flush();
        
        $user = new User();
    	$user->setUsername('admin2');
        $plainPassword = 'admin';
        $encoded = $encoder->encodePassword($user, $plainPassword);
        $user->setPassword($encoded);
        $user->setMail('c5dr9k@gmail.com');
    	$em = $this->getDoctrine()->getManager();
    	$em->persist($user);
    	$em->flush();
        **/
    	// get 
    	$repo = $em->getRepository(Deal::class);

    	$deals = $repo->findAll();

        return $this->render('home/index.html.twig', [
            'controller_name' => 'HomeController',
            'deals' => $deals,
        ]);
    }
}
