<?php


namespace App\Controller\Admin;


use App\Controller\DrawController;
use App\Repository\ChoiceRepository;
use App\Repository\DrawRepository;
use App\Repository\ParticipantRepository;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class AdminController extends AbstractController
{
    /**
     * @var UserRepository
     */
    private $repository;
    /**
     * @var EntityManagerInterface
     */
    private $em;

    public function __construct(DrawRepository $drawrepo,UserRepository $repository, EntityManagerInterface $em,ChoiceRepository $repoChoice,ParticipantRepository $repoParti)
    {
        $this->repoDraw  = $drawrepo;
        $this->repository = $repository;
        $this->em = $em;
        $this->repoParti = $repoParti;
        $this->repoChoice = $repoChoice;
    }


    /**
     * @Route("/admin", name="admin.index")
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function  index(){

        return $this->render('admin/index.html.twig');
    }

    /**
     * @Route("/admin/executeOldDraw", name="admin.execute.draws")
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function  executeAllOldDraws(){

        $this->forward('App\Controller\DrawController::checkAndExecute', [
            'em'  => $this->em
        ]);

        return $this->render('admin/index.html.twig');
    }
}