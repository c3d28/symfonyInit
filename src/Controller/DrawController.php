<?php

namespace App\Controller;


use App\Entity\Choice;
use App\Entity\Draw;
use App\Entity\Participant;
use App\Form\DrawType;
use App\Repository\ChoiceRepository;
use App\Repository\DrawRepository;
use App\Repository\ParticipantRepository;
use DateTime;
use phpDocumentor\Reflection\Types\Integer;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Mailer\Mailer;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Response;
use Doctrine\ORM\EntityManagerInterface;

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
    public function list(EntityManagerInterface $em): Response
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
    public function displayDraw(EntityManagerInterface $em,int $id): Response
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

            if ($draw->getType() != "unique" && $request->get("choices") != null) {
                $choices = $request->get("choices");
                $choices_arr = explode(",", $choices);

                foreach ($choices_arr as $choiceString) {
                    $choice = new Choice();
                    $choice->setText($choiceString);
                    $choice->setDraw($draw);
                    $em->persist($choice);
                    $em->flush();
                }
            }else{
                $choice = new Choice();
                $choice->setText('winner');
                $choice->setDraw($draw);
                $em->persist($choice);
                $em->flush();
            }
            $this->addFlash('success', 'Tirage au sort créé ! ');


            $response = $this->forward('App\Controller\MailController::sendEmail', [
                'receiver' => 'c5dr9k@gmail.com',
                'subject' => 'DraftBox - Tirage au sort créé',
                'text' => 'Votre tirage au sort a été créé avec pour id : '. $draw->getId()
            ]);


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
    public function executeButton(EntityManagerInterface $em, int $id): Response
    {
        $this->execute($em,$id);

        return $this->render('home/index.html.twig', [
            'message' => 'OK'
        ]);

    }

    public function execute(EntityManagerInterface $em, int $id){
        $draw = $this->repository->findOneBy(
            ['id' => $id]
        );

        $participants = $this->repoParti->findBy(
            ['draw' => $draw]
        );

        $choices = $this->repoChoice->findBy(
            ['draw' => $draw]
        );



        switch ($draw->getType()) {
            case 'unique':
                try {
                    $this->executeUnique($em,$participants,$draw,$choices);

                } catch (\Exception $e) {
                    $draw->setFinished(true);
                    $em->persist($draw);
                    $em->flush();

                }

                break;
            case 'all_participant':
                try{
                    $this->executeAllParticipant($em,$participants,$draw,$choices);
                } catch (\Exception $e) {
                    $draw->setFinished(true);
                    $em->persist($draw);
                    $em->flush();

                }
                break;
            case 'all_gift':
                try {
                    $this->executeAllGift($em, $participants, $draw, $choices);
                } catch (\Exception $e) {
                    $draw->setFinished(true);
                    $em->persist($draw);
                    $em->flush();

                }
                break;
            default :
                dump("Rien ne s'est passé");
                $draw->setFinished(true);
                $em->persist($draw);
                $em->flush();

                break;
        }


    }

    public function executeUnique(EntityManagerInterface $em, array $participants,Draw $draw,array $choices): EntityManagerInterface{
        $rand_keys = array_rand( $participants,1);
        $winner = $participants[$rand_keys];

        $choice = $choices[0];
        $choice->setParticipant($winner);
        $em->persist($choice);

        $draw->setFinished(true);
        $em->persist($draw);
        $em->flush();

        return $em;
    }

    /**
     * All participant have a gift ( so choices > or equal than the number of particiupant )
     * @param EntityManagerInterface $em
     * @param int $id
     * @return Response
     */
    public function executeAllParticipant(EntityManagerInterface $em, array $participants,Draw $draw,array $choices): EntityManagerInterface
    {
        $nbParticipant = count($participants);
        $nbChoice = count($choices);

        if ($nbChoice >= $nbParticipant) {
            $rand_keys = array_rand($choices, $nbParticipant);

            if($nbParticipant ==1){
                $choice = $choices[$rand_keys];
                $choice->setParticipant($participants[0]);
                $em->persist($choice);
            }else{
                if($nbChoice >= $nbParticipant){
                    $flagParti = 0;

                    foreach ($rand_keys as $key){
                        $choice = $choices[$key];
                        $choice->setParticipant($participants[$flagParti]);
                        $flagParti++;
                        $em->persist($choice);
                    }
                }
            }
            $draw->setFinished(true);
            $em->persist($draw);
            $em->flush();

            return $em;
        }
        //todo else error (pas assez de choix pour tous les participants

    }

    /**
     * All Choices are associated to a participant (so choices < or equal than the number of participant)
     * @param EntityManagerInterface $em
     * @param int $id
     * @return Response
     */
    public function executeAllGift(EntityManagerInterface $em, array $participants,Draw $draw,array $choices): EntityManagerInterface{
        $nbParticipant = count($participants);
        $nbChoice = count($choices);

        if ($nbChoice <= $nbParticipant){
            $rand_keys = array_rand($participants, $nbChoice);
            shuffle($rand_keys);

            $flagChoice = 0;

            foreach ($rand_keys as $key){
                if($flagChoice <= $nbChoice){
                    $choice = $choices[$flagChoice];
                    $choice->setParticipant($participants[$key]);
                    $flagChoice++;
                    $em->persist($choice);
                }else{
                    // stop pick because all gift have been picked.
                }
            }

        }else{
            //Not possible :

        }
        $draw->setFinished(true);
        $em->persist($draw);
        $em->flush();
        return $em;

    }



    /**
     * CheckAndExecutetheDraw
     * @param EntityManagerInterface $em
     * @param int $id
     * @return Response
     */
    public function checkAndExecute(EntityManagerInterface $em)
    {

        //get all draw not finished and date draw before now
        $list = $this->repository->findDrawToExecute();

        dump($list);

        foreach ($list as $draw){
            dump($draw);
            $this->execute($em,$draw->getId());
        }
        return $this->render('admin/index.html.twig');
    }
}