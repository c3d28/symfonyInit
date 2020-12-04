<?php

namespace App\Controller;


use App\Entity\ChoiceChristmas;
use App\Entity\ChoicePosition;
use App\Entity\Christmas;
use App\Form\ChoiceChristmasType;
use App\Form\ChoicePositionType;
use App\Form\ChristmasType;
use App\Form\RankType;
use App\Repository\ChoiceChristmasRepository;
use App\Repository\ChristmasRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Response;
use Doctrine\ORM\EntityManagerInterface;

class ChristmasController extends AbstractController
{

    /**
     * @var ChristmasRepository
     */
    private $repository;

    /**
     * @var ChoiceChristmasRepository
     */
    private $repo;

    /**
     * @var EntityManagerInterface
     */
    private $em;

    public function __construct(ChristmasRepository $repository,ChoiceChristmasRepository  $repoChoice, EntityManagerInterface $em)
    {
        $this->repository = $repository;

        $this->repo = $repoChoice;
        $this->em = $em;
    }

    /**
     * @Route("/christmas/{id}/", name="christmas.id")
     * @param EntityManagerInterface $em
     * @param Request $request
     * @param $christmas
     * @return Response
     */
    public function displayChristmas(EntityManagerInterface $em,int $id,Request $request): Response
    {
        $christmas = $this->repository->findOneBy(
            ['id' => $id]
        );

        $choices = $this->repo->findBy(
            ['christmas' => $christmas]
        );
        $choice = new ChoicePosition();
        $form = $this->createForm(ChoiceChristmasType::class, $choice);
        $form->handleRequest($request);

        $owner = false;
        if($christmas->getOwner() == $this->getUser()){
            $owner = true;
        }

        //$places = $this->repoChoice->findByRankingByPlace($christmas->getId());
        return $this->render('christmas/info.html.twig', [
            'controller_name' => 'RankController',
            'christmas' => $christmas,
            'owner' => $owner,
            'choices' => $choices,
            //'places' => $places,
            'form' => $form->createView()
        ]);
    }


    /**
     * @Route("/christmas", name="draw.christmas")
     * @param EntityManagerInterface $em
     * @param Christmas $christmas
     * @param Request $request
     * @return Response
     */
    public function newChristmas(EntityManagerInterface $em, Request $request): Response
    {

        $christmas = new Christmas();
        $form = $this->createForm(ChristmasType::class, $christmas);
        $form->handleRequest($request);
        $user = $this->getUser();

        if ($form->isSubmitted() && $form->isValid()) {
            $christmas->setDateCreation(new \DateTime('now'));
            $christmas->setDateDraw(new \DateTime('now'));

            $christmas->setFinished(false);
            $christmas->setOwner($this->getUser());
            $this->em->persist($christmas);
            $this->em->flush();

            $em->persist($christmas);
            $em->flush();

            if ($request->get("choices") != null) {
                $choices = $request->get("choices");
                $choices_arr = explode(",", $choices);

                foreach ($choices_arr as $choiceString) {
                    $choice = new ChoiceChristmas();
                    $choice->setText($choiceString);
                    $choice->setChristmas($christmas);
                    $em->persist($choice);
                    $em->flush();
                }
            }
            $this->addFlash('success', 'Tirage du Santa Secret effectué ! ');


            $response = $this->forward('App\Controller\MailController::sendEmail', [
                'receiver' => 'c5dr9k@gmail.com',
                'subject' => 'DraftBox - SantaSecret effectué',
                'text' => 'Votre tirage au sort a été créé avec pour id christmas: ' . $christmas->getId() . ' les participants ont reçu un mail.'
            ]);


        }

        return $this->render('christmas/index.html.twig', [
            'controller_name' => 'DrawController',
            'form' => $form->createView()
        ]);
    }

    /**
     * @Route("/christmas/execute/{id}", name="christmas.execute")
     * @param EntityManagerInterface $em
     * @param int $int
     * @param Request $request
     * permite to start the draw
     * @return Response
     */
    public function executeButton(EntityManagerInterface $em, int $id): Response
    {
        $this->execute($em,$id);

        return $this->redirectToRoute('christmas.id', ['id' => $id]);

    }

    public function execute(EntityManagerInterface $em , $id): EntityManagerInterface{
        $christmas = $this->repository->find($id);
        $choices = $this->repo->findByRankingNoPlace($id);
        dump($choices);
        shuffle($choices);
        dump($choices);
        $choiceSize = sizeof($choices);
        $count = 0;
        foreach ($choices as $choice) {
            $position = $count + 1;
            if($count > $choiceSize - 2 ){
                $position = 0;
            }
            //$choicesPossible = $this->repo->findByChoiceNotHimSelfAndWithoutGiftTo($id,$choice->getText());
            //dump($choicesPossible);
            $choice->setGiftTo($choices[$position]->getText());
            dump($choice);
            $em->persist($choice);
            $em->flush();
            $count++ ;
        }

        //Envoyer un mail à chaque participant
        foreach ($choices as $choice){
            $choice->getText();
            $this->forward('App\Controller\MailController::sendEmail', [
                'receiver' =>  $choice->getText(),
                'subject' => 'DraftBox - Votre SantaSecret a été désigné : '.$christmas->getName(),
                'text' => 'Nous avons effectué le tirage au sort du santa secret n°: ' . $christmas->getId() . '<br/> Vous devez offrir un cadeau à : <b>'.$choice->getGiftTo().'</b>'
            ]);
        }

        $christmas->setFinished(true);
        $em->persist($christmas);
        $em->flush();
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


}