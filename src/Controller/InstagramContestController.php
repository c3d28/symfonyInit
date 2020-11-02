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
            $instagramContest->setDateCreation(new \DateTime('now'));
            $instagramContest->setDate("date");
            $instagramContest->setFinished(false);
            $instagramContest->setWinnerInstagram("");
            if ($user!= null) {
                $instagramContest->setOwner($user);
            }

            $em->persist($instagramContest);
            $em->flush();

            return $this->redirectToRoute("instagram.id",array('id'=> $instagramContest->getId()));
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


        return $this->redirectToRoute("instagram.id",array('id'=> $id));
        //return $this->render('instagram/info.html.twig', [
        //    'controller_name' => 'InstagramContestController',
        //]);
    }

    public function execute(EntityManagerInterface $em, int $id)
    {
        $contest = $this->repository->findOneBy(
            ['id' => $id]
        );

        $instagram = \InstagramScraper\Instagram::withCredentials('drawboxfr', 'DrawBoxFr2020', new Psr16Adapter('Files'));
        $instagram->login();
        $instagram->saveSession();



        $media = $instagram->getMediaByUrl($contest->getUrlPost());
        $comments = $instagram->getMediaCommentsById($media->getId(),10000);
        $participants = [];

        foreach ($comments as $comment){
            $flagCanAdd = true;
            if($contest->getHashtag() != null){
                //i have to check if hashtag is in the text
                if(strpos($comment->getText(), $contest->getHashtag())){
                    $flagCanAdd = true;
                }else{
                    $flagCanAdd = false;
                }
            }

            if($flagCanAdd){
                $participants[] = $comment->getOwner()->getUsername();

            }
        }
        $participants = array_unique($participants);

        $accountUsername = $instagram->getAccount($media->getOwner()->getUsername());

        //remove the owner of media
        if (($key = array_search($accountUsername, $participants)) !== false) {
            unset($participants[$key]);
        }

        $rand_keys = array_rand( $participants,1);
        $winner = $participants[$rand_keys];


        $this->em->persist($contest);
        $this->em->flush();


    }



}