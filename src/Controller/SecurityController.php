<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\RegisterType;
use App\Form\UserType;
use App\Repository\DrawRepository;
use App\Repository\ParticipantRepository;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;

class SecurityController extends AbstractController
{

    /**
     * @var UserRepository
     */
    private $userRepo;
    /**
     * @var EntityManagerInterface
     */
    private $em;

    public function __construct(UserRepository $userRepo, EntityManagerInterface $em)
    {
        $this->userRepo = $userRepo;
        $this->em = $em;
    }
    /**
     * @Route("/login", name="app_login")
     */
    public function login(AuthenticationUtils $authenticationUtils): Response
    {

        // get the login error if there is one
        $error = $authenticationUtils->getLastAuthenticationError();
        // last username entered by the user
        $lastUsername = $authenticationUtils->getLastUsername();

        //$this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
        if($this->getUser() != null){
            return $this->redirectToRoute('home');
        }
        return $this->render('security/login.html.twig', ['last_username' => $lastUsername, 'error' => $error]);
    }

    /**
     * @Route("/logout", name="app_logout")
     */
    public function logout()
    {
        throw new \Exception('This method can be blank - it will be intercepted by the logout key on your firewall');
    }

    /**
     * @Route("/register", name="app_register")
     */
    public function register(AuthenticationUtils $authenticationUtils,Request $request,UserPasswordEncoderInterface $encoder): Response
    {
        $submittedToken = $request->request->get('register');
        dump($submittedToken);

        if (!$this->isCsrfTokenValid('register', $submittedToken['_token'])) {
            dump("error");

        }

        $user = new User();

        $form = $this->createForm(RegisterType::class,$user);
        dump("before");

        if ($request->isMethod('POST')) {
            $form->submit($request->request->get($form->getName()));
            if ($form->isSubmitted() && $form->isValid()) {

                $encoded = $encoder->encodePassword($user, $request->request->get("password"));
                $user->setPassword($encoded);
                $user->setUsername($request->request->get("username"));
                $user->setMail($request->request->get("mail"));
                //$user->setUsername()
                $this->em->persist($user);
                $this->em->flush();
                dump("redirect");

                return $this->render('home/index.html.twig');
            }
        }
        dump("after");

        return $this->render('home/index.html.twig');
        /*$plainPassword = 'admin';
        $encoded = $encoder->encodePassword($user, $plainPassword);
        dump($encoded);
        $user->setPassword($encoded);
        $user->setMail('c5dr9k@gmail.com');
        $em = $this->getDoctrine()->getManager();
        $em->persist($user);
        $em->flush();*/
    }
}
