<?php

namespace App\Controller;

use App\Entity\TeamFifa;
use App\Repository\ChoiceRepository;
use App\Repository\DrawRepository;
use App\Repository\ParticipantRepository;
use App\Repository\TeamFifaRepository;
use App\Services\FutDb;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\SerializerInterface;


class FifaController extends AbstractController
{


    public function __construct(TeamFifaRepository $repository)
    {
        $this->repo = $repository;

    }

    /**
     * @Route("/create_teams", name="create_teams",methods={"GET"})
$     */
    public function createAction(Request $request, SerializerInterface $serializer,FutDb $futDbService,EntityManagerInterface $em)
    {
        $result = $futDbService->fetchClubsAll();
        $result = json_decode($result);
        for($i=0;$i< count($result) ; $i++){
            $listClub = $result[$i];
            foreach ($listClub as $club){
                $teamFifa = $serializer->deserialize(json_encode($club), TeamFifa::class, 'json');

                $entity = $this->repo->findOneBy([
                    'name' => $teamFifa->getName()
                ]);
                if($entity == null){
                    $teamFifa->setCode($club->id);
                    $em->persist($teamFifa);
                    $em->flush();
                }
            }
        }


        return $this->render('home/index.html.twig', [
            'controller_name' => 'HomeController'
        ]);
    }
}