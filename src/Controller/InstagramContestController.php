<?php

namespace App\Controller;


use App\Entity\Choice;
use App\Entity\Draw;
use App\Entity\InstagramContest;
use App\Entity\Participant;
use App\Form\DrawType;
use App\Form\InstagramType;
use App\Repository\ChoiceRepository;
use App\Repository\DrawRepository;
use App\Repository\InstagramContestRepository;
use App\Repository\ParticipantRepository;
use DateTime;
use InstagramScraper\Instagram;
use InstagramScraper\Model\Comment;
use InstagramScraper\Model\Media;
use phpDocumentor\Reflection\Types\Integer;
use Phpfastcache\Helper\Psr16Adapter;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Mailer\Mailer;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Response;
use Doctrine\ORM\EntityManagerInterface;

class InstagramContestController extends AbstractController
{

    /**
     * @var InstagramContestRepository
     */
    private $repository;
    /**
     * @var EntityManagerInterface
     */
    private $em;

    public function __construct(InstagramContestRepository $repository,  EntityManagerInterface $em)
    {
        $this->repository = $repository;

        $this->em = $em;
    }

    /**
     * @Route("/instagramContests/", name="instagramContests")
     * @param EntityManagerInterface $em
     * @param Request $request
     * @param $draws
     * @return Response
     */
    public function list(EntityManagerInterface $em): Response
    {
        if ($this->getUser() != null) {
            $instagramContests = $this->repository->findBy(['owner' => $this->getUser()]);
        }




        return $this->render('instagram/list.html.twig', [
            'controller_name' => 'InstagramController',
            'instagramContests' => $instagramContests
        ]);
    }

    /**
     * @Route("/instagram/new", name="draw.instagram.new")
     * @param EntityManagerInterface $em
     * @param InstagramContest $instagramContest
     * @param Request $request
     * @return Response
     */
    public function newInstagram(EntityManagerInterface $em, Request $request): Response
    {

        $instagramContest = new InstagramContest();
        $form = $this->createForm(InstagramType::class, $instagramContest);
        $form->handleRequest($request);
        $user = $this->getUser();
        if ($form->isSubmitted() && $form->isValid()) {
            dump($form);

            dump($request);
            $instagramContest->setDateCreation(new \DateTime('now'));
            $instagramContest->setDate("date");
            $instagramContest->setFinished(false);
            $instagramContest->setWinnerInstagram("");
            if ($user!= null) {
                dump($user);
                $instagramContest->setOwner($user);
            }

            $em->persist($instagramContest);
            $em->flush();
        }

        return $this->render('draw/instagram.html.twig', [
            'controller_name' => 'DrawController',
            'form' => $form->createView()

        ]);
    }

    /**
     * @Route("/instagram/{id}/", name="instagram.id")
     * @param EntityManagerInterface $em
     * @param Request $request
     * @param $draws
     * @return Response
     */
    public function displayDraw(EntityManagerInterface $em,int $id): Response
    {

        $owner = false;
        $contest = $this->repository->findOneBy(
            ['id' => $id]
        );

        if($this->getUser() == $contest->getOwner()){
            $owner = true;
        }

        return $this->render('instagram/info.html.twig', [
            'controller_name' => 'InstagramContestController',
            'contest' => $contest,
            'owner' => $owner
        ]);
    }

    public function executebgg(){
        $instagram = new \InstagramScraper\Instagram();
        $media = $instagram->getMediaByUrl('https://www.instagram.com/p/CDd6dokC_Mj');
        $comments = $instagram->getMediaCommentsById($media->getId(),1000);

        dump(       $comments);
    }

    /**
     * @Route("/instagram/execute/{id}", name="instagram.execute")
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
        $contest = $this->repository->findOneBy(
            ['id' => $id]
        );

        $instagram = \InstagramScraper\Instagram::withCredentials('drawboxfr', 'Specom28', new Psr16Adapter('Files'));
        $instagram->login();
        $instagram->saveSession();

        $media = $instagram->getMediaByUrl($contest->getUrlPost());
        $comments = $instagram->getMediaCommentsById($media->getId(),1000);
        $participants = [];

        foreach ($comments as $comment){
            $participants[] = $comment->getOwner()->getUsername();
        }

        $rand_keys = array_rand( $participants,1);
        $winner = $participants[$rand_keys];

        dump($participants);
        dump($winner);

        $contest->setWinnerInstagram($winner);
        $contest->setFinished(true);

        $this->em->persist($contest);
        $this->em->flush();


    }



}