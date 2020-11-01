<?php

namespace App\Controller;

use App\Entity\Deal;
use App\Entity\User;

use App\Services\FutDb;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;

use Symfony\Component\HttpFoundation\Response;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Symfony\Contracts\HttpClient\HttpClientInterface;

use Symfony\Component\Serializer\Encoder\JsonEncoder;
    use Symfony\Component\Serializer\Encoder\XmlEncoder;
    use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;
    use Symfony\Component\Serializer\Serializer;

class HomeController extends AbstractController
{

     public FutDb $futDbService;

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
