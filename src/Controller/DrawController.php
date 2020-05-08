<?php

namespace App\Controller;


use App\Entity\Draw;
use App\Entity\Participant;
use App\Form\DrawType;
use App\Repository\DrawRepository;
use App\Repository\ParticipantRepository;
use phpDocumentor\Reflection\Types\Integer;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Response;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Validator\Constraints\DateTime;

class DrawController extends AbstractController
{

    /**
     * @var DrawRepository
     */
    private $repository;
    /**
     * @var EntityManagerInterface
     */
    private $em;

    public function __construct(DrawRepository $repository,ParticipantRepository $repoParti, EntityManagerInterface $em)
    {
        $this->repository = $repository;
        $this->repoParti = $repoParti;

        $this->em = $em;
    }

    /**
     * @Route("/draws/", name="draws")
     * @param EntityManagerInterface $em
     * @param Request $request
     * @param $draws
     * @return Response
     */
    public function list(): Response
    {
        // get list of participants for the user current
        $partipants = $this->repoParti->findBy(
            ['user' => $this->getUser()]
        );

        $listDraw = array();

        foreach ($partipants as $part){
            $listDraw = $part->getDraw()->getId();
        }

        //
        $draws = $this->repository->findBy(
            ['id' => array($listDraw)]);

        return $this->render('draw/list.html.twig', [
            'controller_name' => 'DrawController',
            'participants' => $partipants,
            'draws' => $draws
        ]);
    }

    /**
     * @Route("/draw/{id}/", name="draw.id")
     * @param EntityManagerInterface $em
     * @param Request $request
     * @param $draws
     * @return Response
     */
    public function displayDraw(int $id): Response
        {
        $draw = $this->repository->findOneBy(
            ['id' => $id ]
        );

        $partipants = $this->repoParti->findBy(
            ['draw' => $draw]
        );
            return $this->render('draw/info.html.twig', [
                'controller_name' => 'DrawController',
                'draw' => $draw,
                'participants' => $partipants
        ]);
    }

    /**
     * @Route("/draw-join", name="draw.join", methods={"POST"})
     * @param EntityManagerInterface $em
     * @param Request $request
     * @param $draws
     * @return Response
     */
    public function joinDraw(EntityManagerInterface $em,Request $request): Response
    {
        dump($request->request->get('code'));

        $draw = $this->repository->findOneBy(
            ['shareCode' => $request->request->get('code') ]
        );

        if($draw != null){
            dump($draw);

            $user = $this->getUser();

            // get list of participants for the user current
            $participants = $this->repoParti->findBy(
                [
                    'user' => $this->getUser(),
                    'draw' => $draw->getId()
                ]
            );

            if ($participants == null){

                // add new Participant
                $participant = new Participant();
                $participant->setOwner(true);
                $participant->setSubscribed(true);
                $participant->setUser($user);
                $participant->setDraw($draw);
                $em->persist($participant);
                $em->flush();
            }

        }else{
            return $this->render('home/index.html.twig');
        }
        return $this->render('draw/info.html.twig', [
            'controller_name' => 'DrawController',
            'draw' => $draw,
            'participants' => $participants
        ]);
    }

    /**
     * @Route("/draw", name="draw.new")
     * @param EntityManagerInterface $em
     * @param Draw $draw
     * @param Request $request
     * @return Response
     */
    public function new(EntityManagerInterface $em,Request $request): Response
    {

        $draw = new Draw();
        $form = $this->createForm(DrawType::class,$draw);
        $form->handleRequest($request);
        $user = $this->getUser();

        if ($form->isSubmitted() && $form->isValid()){
            $draw->setDateCreation(new \DateTime('now'));
            $draw->setShareCode(uniqid());
            $this->em->persist($draw);
            $this->em->flush();


            // add new Participant
            $participant = new Participant();
            $participant->setOwner(true);
            $participant->setSubscribed(true);
            $participant->setUser($user);
            $participant->setDraw($draw);
            $em->persist($participant);
            $em->flush();
            return $this->redirectToRoute('home');
        }

        return $this->render('draw/index.html.twig', [
            'controller_name' => 'DrawController',
            'form' => $form->createView()
        ]);
    }
}
