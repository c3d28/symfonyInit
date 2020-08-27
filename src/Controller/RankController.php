<?php

namespace App\Controller;


use App\Entity\Choice;
use App\Entity\ChoicePosition;

use App\Entity\Draw;
use App\Entity\InstagramContest;
use App\Entity\Participant;
use App\Entity\Rank;
use App\Form\ChoicePositionType;
use App\Form\DrawType;
use App\Form\InstagramType;
use App\Form\RankType;
use App\Repository\ChoicePositionRepository;
use App\Repository\ChoiceRepository;
use App\Repository\DrawRepository;
use App\Repository\ParticipantRepository;
use App\Repository\RankRepository;
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

class RankController extends AbstractController
{

    /**
     * @var DrawRepository
     */
    private $repository;
    /**
     * @var EntityManagerInterface
     */
    private $em;

    public function __construct(DrawRepository $repository, ParticipantRepository $repoParti, EntityManagerInterface $em, ChoicePositionRepository $repoChoice,RankRepository $rankrepo)
    {
        $this->repository = $repository;
        $this->repoParti = $repoParti;
        $this->repoChoice = $repoChoice;
        $this->repoRank = $rankrepo;;

        $this->em = $em;
    }

    /**
     * @Route("/rank/{id}/", name="rank.id")
     * @param EntityManagerInterface $em
     * @param Request $request
     * @param $ranks
     * @return Response
     */
    public function displayDraw(EntityManagerInterface $em,int $id,Request $request): Response
    {


        $rank = $this->repoRank->findOneBy(
            ['id' => $id]
        );

        $choices = $this->repoChoice->findBy(
            ['rank' => $rank]
        );

        $choice = new ChoicePosition();
        $form = $this->createForm(ChoicePositionType::class, $choice);
        $form->handleRequest($request);

        $owner = false;
        if($rank->getOwner() == $this->getUser()){
            $owner = true;
        }

        $places = $this->repoChoice->findByRankingByPlace($rank->getId());
        return $this->render('rank/info.html.twig', [
            'controller_name' => 'RankController',
            'rank' => $rank,
            'owner' => $owner,
            'choices' => $choices,
            'places' => $places,
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

            $rank->setFinished(false);
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
     * @Route("/rank/execute/{id}", name="rank.execute")
     * @param EntityManagerInterface $em
     * @param int $int
     * @param Request $request
     * permite to start the draw
     * @return Response
     */
    public function executeButton(EntityManagerInterface $em, int $id): Response
    {
        $this->executeUnique($em,$id);

        return $this->redirectToRoute('rank.id', ['id' => $id]);

    }

    public function executeUnique(EntityManagerInterface $em , $id): EntityManagerInterface{

        $positions = $this->repoChoice->findByRankingNoPlace($id);
        $rand_keys = array_rand( $positions,1);
        $nextWin = $positions[$rand_keys];
        $maxPlace = $this->repoChoice->getMaxPosition($id);

        $choicePositionToEdit = $this->repoChoice->findBy(['id' => $nextWin]);
        if($maxPlace == NULL || $maxPlace == "null" || $maxPlace == ""){
            $position = 1;

        }else {
            $position = $maxPlace + 1;
        }
        $choicePosition = $choicePositionToEdit[0];
        $choicePosition->setPlace($position);
        $choicePosition->setDateDraw(new \DateTime('now'));

        $em->persist($choicePosition);
        $em->flush();

        // check if last
        dump($this->repoChoice->countAllChoice($id));
        if($position == $this->repoChoice->countAllChoice($id) ){
            $rank = $this->repoRank->findOneBy(["id"=>$id]);
            $rank->setFinished(true);
            $em->persist($rank);
            $em->flush();
        }

        return $em;
    }


    /**
     * @Route("/ranks/", name="ranks")
     * @param EntityManagerInterface $em
     * @param Request $request
     * @param $ranks
     * @return Response
     */
    public function list(EntityManagerInterface $em): Response
    {
        $ranks = $this->repoRank->findBy(
            ['owner' => $this->getUser()]);

        return $this->render('rank/list.html.twig', [
            'controller_name' => 'RankController',
            'ranks' => $ranks
        ]);
    }

    /**
     * @Route("/rank/addChoice/{id}/", name="rank.addChoice")
     * @param EntityManagerInterface $em
     * @param Request $request
     * @param $ranks
     * @return Response
     */
    public function addChoice(EntityManagerInterface $em,Request $request,int $id): Response{

        $rank = $this->repoRank->findOneBy(
            ['id' => $id]
        );

        $choice = new ChoicePosition();
        $form = $this->createForm(ChoicePositionType::class, $choice);
        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid() ){
            $choice->setRank($rank);
            $rank->setFinished(0);
            $em->persist($choice);
            $em->persist($rank);

            $em->flush();
        }else{
            dump( $request);
        }

        return $this->redirectToRoute('rank.id', ['id' => $id]);
    }

}