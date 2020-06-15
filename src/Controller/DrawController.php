<?php

namespace App\Controller;


use App\Entity\Choice;
use App\Entity\Draw;
use App\Entity\Participant;
use App\Form\DrawType;
use App\Repository\ChoiceRepository;
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

    public function __construct(DrawRepository $repository, ParticipantRepository $repoParti, EntityManagerInterface $em, ChoiceRepository $repoChoice)
    {
        $this->repository = $repository;
        $this->repoParti = $repoParti;
        $this->repoChoice = $repoChoice;

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


        $listDrawJoin = array();
        $listDrawOwner = array();

        foreach ($partipants as $part) {
            if ($part->getOwner() == true) {
                array_push($listDrawOwner, $part->getDraw()->getId());
            } else {
                array_push($listDrawJoin, $part->getDraw()->getId());
            }
        }

        //
        $drawsOther = $this->repository->findBy(
            ['id' => $listDrawJoin]);
        $drawsOwner = $this->repository->findBy(
            ['id' => $listDrawOwner]);

        return $this->render('draw/list.html.twig', [
            'controller_name' => 'DrawController',
            'participants' => $partipants,
            'drawsOther' => $drawsOther,
            'drawsOwner' => $drawsOwner
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
            ['id' => $id]
        );

        $partipants = $this->repoParti->findBy(
            ['draw' => $draw]
        );

        $choices = $this->repoChoice->findBy(
            ['draw' => $draw]
        );

        $owner = false;
        foreach ($partipants as $part) {
            if ($part->getOwner() == true) {
                if ($part->getUser() == $this->getUser()) {
                    $owner = true;
                }
            }
        }

        return $this->render('draw/info.html.twig', [
            'controller_name' => 'DrawController',
            'draw' => $draw,
            'participants' => $partipants,
            'owner' => $owner,
            'choices' => $choices
        ]);
    }

    /**
     * @Route("/draw-join", name="draw.join", methods={"POST"})
     * @param EntityManagerInterface $em
     * @param Request $request
     * @param $draws
     * @return Response
     */
    public function joinDraw(EntityManagerInterface $em, Request $request): Response
    {

        $draw = $this->repository->findOneBy(
            ['shareCode' => $request->request->get('code')]
        );

        if ($draw != null) {
            if ($this->getUser() != null) {
                $user = $this->getUser();
            } else {
                return $this->render('home/index.html.twig');
            }

            // get list of participants for the user current
            $participants = $this->repoParti->findBy(
                [
                    'user' => $this->getUser(),
                    'draw' => $draw->getId()
                ]
            );

            if ($participants == null) {

                // add new Participant
                $participant = new Participant();
                $participant->setOwner(false);
                $participant->setSubscribed(true);
                $participant->setUser($user);
                $participant->setDraw($draw);
                $em->persist($participant);
                $em->flush();
            }

        } else {
            return $this->render('home/index.html.twig');
        }
        return $this->redirectToRoute('draw.id',array('id' => $draw->getId()));
    }

    /**
     * @Route("/draw", name="draw.new")
     * @param EntityManagerInterface $em
     * @param Draw $draw
     * @param Request $request
     * @return Response
     */
    public function new(EntityManagerInterface $em, Request $request): Response
    {

        $draw = new Draw();
        $form = $this->createForm(DrawType::class, $draw);
        $form->handleRequest($request);
        $user = $this->getUser();

        if ($form->isSubmitted() && $form->isValid()) {
            $draw->setDateCreation(new \DateTime('now'));
            $draw->setShareCode(uniqid());
            $draw->setType("UNIQUEDRAW");
            $draw->setFinished(false);
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

            if ($request->get("choices") != null) {
                $choices = $request->get("choices");
                $choices_arr = explode(",", $choices);

                foreach ($choices_arr as $choiceString) {
                    $choice = new Choice();
                    $choice->setText($choiceString);
                    $choice->setDraw($draw);
                    $em->persist($choice);
                    $em->flush();
                };
            }
            //return $this->redirectToRoute('home');
        }

        return $this->render('draw/index.html.twig', [
            'controller_name' => 'DrawController',
            'form' => $form->createView()
        ]);
    }

    /**
     * @Route("/draw/execute/{id}", name="draw.execute")
     * @param EntityManagerInterface $em
     * @param int $int
     * @param Request $request
     * permite to start the draw
     * @return Response
     */
    public function execute(EntityManagerInterface $em, int $id): Response
    {
        $draw = $this->repository->findOneBy(
            ['id' => $id]
        );

        $partipants = $this->repoParti->findBy(
            ['draw' => $draw]
        );

        $choices = $this->repoChoice->findBy(
            ['draw' => $draw]
        );

        $nbParticipant = count($partipants);
        $nbChoice = count($choices);

        if($nbChoice < $nbParticipant){

        }else{
            $rand_keys = array_rand($choices, $nbParticipant);

            if($nbParticipant ==1){
                $choice = $choices[$rand_keys];
                $choice->setParticipant($partipants[0]);
                $em->persist($choice);
            }else{
                if($nbChoice >= $nbParticipant){
                    $flagParti = 0;

                    foreach ($rand_keys as $key){
                        $choice = $choices[$key];
                        $choice->setParticipant($partipants[$flagParti]);
                        $flagParti++;
                        $em->persist($choice);
                    }
                }
            }
        }
        $draw->setFinished(true);
        $em->persist($draw);


        $em->flush();

        return $this->render('home/index.html.twig', [
            'message' => 'OK'
        ]);

    }

}
