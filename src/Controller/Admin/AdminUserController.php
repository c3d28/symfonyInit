<?php


namespace App\Controller\Admin;


use App\Entity\User;
use App\Form\EditUserType;
use App\Form\UserType;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class AdminUserController extends AbstractController
{
    /**
     * @var UserRepository
     */
    private $repository;
    /**
     * @var EntityManagerInterface
     */
    private $em;

    public function __construct(UserRepository $repository, EntityManagerInterface $em)
    {

        $this->repository = $repository;
        $this->em = $em;
    }



    /**
     * @Route("/admin/user", name="admin.user.index")
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function  index(){
        $users = $this->repository->findAll();
        return $this->render('admin/user/index.html.twig',compact('users'));
    }


    /**
     * @Route("/admin/user/edit/{id}", name="admin.user.edit")
     * @param User $user
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function  edit(User $user, Request $request,UserPasswordEncoderInterface $encoder){
        $form = $this->createForm(EditUserType::class,$user);
        $form->handleRequest($request);

        dump($user);
        $encoded = $encoder->encodePassword($user, $user->getPassword());


        if ($form->isSubmitted() && $form->isValid()){
            $user->setPassword($encoded);
            $this->em->persist($user);
            $this->em->flush();
            return $this->redirectToRoute('admin.user.index');
        }
        return $this->render('admin/user/edit.html.twig',[
            'user' => $user,
            'form' => $form->createView()
        ]);
    }
}