<?php

namespace App\Controller;


use App\Entity\Choice;
use App\Entity\ChoiceOFA1;
use App\Entity\ChoiceOFA2;
use App\Entity\ChoicePosition;

use App\Entity\Draw;
use App\Entity\InstagramContest;
use App\Entity\Participant;
use App\Entity\Rank;
use App\Form\ChoiceOFAType;
use App\Form\DrawType;
use App\Form\InstagramType;
use App\Form\RankType;
use App\Repository\ChoiceOFA1Repository;
use App\Repository\ChoiceOFA2Repository;
use App\Repository\ChoiceRepository;
use App\Repository\DrawRepository;
use App\Repository\ParticipantRepository;
use DateTime;
use InstagramScraper\Instagram;
use InstagramScraper\Model\Media;
use phpDocumentor\Reflection\Types\Integer;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Mailer\Mailer;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Response;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;

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

    public function __construct(DrawRepository $repository, ParticipantRepository $repoParti, EntityManagerInterface $em, ChoiceRepository $repoChoice,ChoiceOFA1Repository $repoChoiceOFA1,ChoiceOFA2Repository $repoChoiceOFA2)
    {
        $this->repository = $repository;
        $this->repoParti = $repoParti;
        $this->repoChoice = $repoChoice;
        $this->repoChoiceOFA1 = $repoChoiceOFA1;
        $this->repoChoiceOFA2 = $repoChoiceOFA2;

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

        if($draw->getType() == "OFA"){
            $choices1 = $this->repoChoiceOFA1->findBy(
                ['draw' => $draw]
            );
            $choices2 = $this->repoChoiceOFA2->findBy(
                ['draw' => $draw]
            );
        }



        $owner = false;
        foreach ($partipants as $part) {
            if ($part->getOwner() == true) {
                if ($part->getUser() == $this->getUser()) {
                    $owner = true;
                }
            }
        }
        if($draw->getType() == "OFA"){
            $listAuthorized = array("Robin","c3d28");

            if(in_array($partipants[0]->getUser()->getUsername(),$listAuthorized)){
                return $this->render('draw/oneForAll/infoRhezon.html.twig', [
                    'controller_name' => 'DrawController',
                    'draw' => $draw,
                    'participants' => $partipants,
                    'owner' => $owner,
                    'choices1' => $choices1,
                    'choices2' => $choices2
                ]);
            }

            return $this->render('draw/oneForAll/info.html.twig', [
                'controller_name' => 'DrawController',
                'draw' => $draw,
                'participants' => $partipants,
                'owner' => $owner,
                'choices1' => $choices1,
                'choices2' => $choices2
            ]);


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
     * @Route("/draw/instagram/edit", name="draw.instagram.edit")
     * @param EntityManagerInterface $em
     * @param Draw $draw
     * @param Request $request
     * @return Response
     */
    public function editInstagram(EntityManagerInterface $em, Request $request): Response
    {

        $draw = new Draw();
        $form = $this->createForm(DrawType::class, $draw);
        $form->handleRequest($request);
        $user = $this->getUser();
        if ($form->isSubmitted() && $form->isValid()) {

        }


        return $this->render('draw/instagram.edit.html.twig', [
            'controller_name' => 'DrawController'
        ]);
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
     * @Route("/rank", name="draw.rank")
     * @param EntityManagerInterface $em
     * @param Draw $draw
     * @param Request $request
     * @return Response
     */
    public function newRank(EntityManagerInterface $em, Request $request): Response
    {

        $rank = new Rank();
        $form = $this->createForm(RankType::class, $rank);
        $form->handleRequest($request);
        $user = $this->getUser();

        if ($form->isSubmitted() && $form->isValid()) {
            $rank->setDateCreation(new \DateTime('now'));
            $rank->setDateDraw(new \DateTime('now'));

            $rank->setOwner($this->getUser());
            $this->em->persist($rank);
            $this->em->flush();

            $em->persist($rank);
            $em->flush();

            if ($request->get("choices") != null) {
                $choices = $request->get("choices");
                $choices_arr = explode(",", $choices);

                foreach ($choices_arr as $choiceString) {
                    $choice = new ChoicePosition();
                    $choice->setText($choiceString);
                    $choice->setRank($rank);
                    $em->persist($choice);
                    $em->flush();
                }
            }
            $this->addFlash('success', 'Tirage de classement créé ! ');


            $response = $this->forward('App\Controller\MailController::sendEmail', [
                'receiver' => 'c5dr9k@gmail.com',
                'subject' => 'DraftBox - Tirage de classement successif créé',
                'text' => 'Votre tirage au sort a été créé avec pour id rank: '. $rank->getId()
            ]);


        }

        return $this->render('rank/index.html.twig', [
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

    public function execute(EntityManagerInterface $em, int $id)
    {
        $draw = $this->repository->findOneBy(
            ['id' => $id]
        );

        $participants = $this->repoParti->findBy(
            ['draw' => $draw]
        );

        $choices = $this->repoChoice->findBy(
            ['draw' => $draw]
        );

        $owner = $this->repoParti->findOwnerFromDraw($id);

            switch ($draw->getType()) {
                case 'unique':
                    try {
                        $this->executeUnique($em, $participants, $draw, $choices);

                    } catch (\Exception $e) {
                        $draw->setFinished(true);
                        $em->persist($draw);
                        $em->flush();

                    }

                    break;
                case 'all_participant':
                    try {
                        $this->executeAllParticipant($em, $participants, $draw, $choices);
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
     * @Route("/draw/new/oneForAll", name="draw.oneForAll")
     * @param EntityManagerInterface $em
     * @param Draw $draw
     * @param Request $request
     * @return Response
     */
    public function newOneForAll(EntityManagerInterface $em, Request $request): Response
    {

        $draw = new Draw();
        $form = $this->createForm(ChoiceOFAType::class, $draw);
        $form->handleRequest($request);
        $user = $this->getUser();

        if ($form->isSubmitted() && $form->isValid()) {
            $draw->setDateCreation(new \DateTime('now'));
            $draw->setDateDraw(new \DateTime('2029-07-07 12:04:00'));


            $draw->setShareCode(uniqid());
            $draw->setFinished(false);
            $draw->setType("OFA");
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

            if ($draw->getType() == "OFA") {
                $choices1 = $request->get("choices1");
                $choices_arr1 = explode(",", $choices1);

                $choices2 = $request->get("choices2");
                $choices_arr2 = explode(",", $choices2);

                foreach ($choices_arr1 as $choiceString) {
                    $choice = new ChoiceOFA1();
                    $choice->setText($choiceString);
                    $choice->setDraw($draw);
                    $em->persist($choice);
                    $em->flush();
                }
                foreach ($choices_arr2 as $choiceString) {
                    $choice = new ChoiceOFA2();
                    $choice->setText($choiceString);
                    $choice->setDraw($draw);
                    $em->persist($choice);
                    $em->flush();
                }
            }
            $this->addFlash('success', 'Tirage au sort créé ! ');


            $response = $this->forward('App\Controller\MailController::sendEmail', [
                'receiver' => 'c5dr9k@gmail.com',
                'subject' => 'DraftBox - Tirage au sort OFA créé',
                'text' => 'Votre tirage au sort a été créé avec pour id : '. $draw->getId()
            ]);
        }
        return $this->render('draw/oneForAll/index.html.twig', [
            'controller_name' => 'DrawController',
            'form' => $form->createView()
        ]);
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

        foreach ($list as $draw){
            $this->execute($em,$draw->getId());
        }
        return $this->render('admin/index.html.twig');
    }

    /**
     * @Route("/OFA/execute/{id}", name="ofa.execute")
     * @param EntityManagerInterface $em
     * @param int $int
     * @param Request $request
     * permite to start the draw
     * @return Response
     */
    public function executeButtonOFA(EntityManagerInterface $em, int $id): Response
    {
        $this->executeOFA($em,$id);

        return $this->redirectToRoute('draw.id', ['id' => $id]);

    }


    public function executeOFA(EntityManagerInterface $em , $id): EntityManagerInterface{

        $choices1Free = $this->repoChoiceOFA1->findChoice1WithoutChoice2($id);
        if($choices1Free == null){
            // il n'y a plus de participant
            $draw = $this->repository->findOneBy([
                'id' => $id
            ]);
            $draw->setFinished(true);
            $em->persist($draw);
            $em->flush();
            return $em;
        }
        $nextWin = $this->getNextWinner($choices1Free);
        $choices2Free = $this->repoChoiceOFA2->findChoice2WithoutChoice1($id);
        $nextWin2 = $this->getNextWinner($choices2Free);

        $choice1 = $this->repoChoiceOFA1->findOneBy([
            'id' => $nextWin
        ]);
        $choice1->setChoiceOfa2($nextWin2);
        $choice2=$this->repoChoiceOFA2->findOneBy([
            'id' => $nextWin2
        ]);
        $choice2->setChoiceOfa1($nextWin);

        $em->persist($choice1);
        $em->persist($choice2);
        $em->flush();



        return $em;
    }

    /**
     * @param $choices1Free
     * @return mixed
     */
    public function getNextWinner($listChoice)
    {
        $rand_keys = array_rand($listChoice, 1);
        $nextWin = $listChoice[$rand_keys];
        return $nextWin;
    }


}