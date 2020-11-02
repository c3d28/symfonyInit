<?php

namespace App\Controller;

use App\Entity\PcmCyclists;
use App\Entity\PcmTeams;
use App\Repository\PcmCyclistsRepository;
use App\Repository\PcmTeamsRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\DomCrawler\Crawler;

class PCMController extends AbstractController
{


    public function __construct(PcmCyclistsRepository $repository, PcmTeamsRepository $teamRepo)
    {
        $this->repo = $repository;
        $this->teamRepo = $teamRepo;

    }

    /**
     * @Route("/cyclist/{idPCM}/", name="cyclist.idPCM")
     * @param EntityManagerInterface $em
     * @param Request $request
     * @param $ranks
     * @return Response
     */
    public function displayCyclist(EntityManagerInterface $em,int $idPCM,Request $request): Response
    {
        $cyclist = $this->repo->findOneBy([
            'IDCyclist' => $idPCM
        ]);
        dump($cyclist);

        $team = $this->teamRepo->findOneBy([
            'IDTeam' => $cyclist->getFkIDteam()
        ]
        );
        dump($team);

        return $this->render('pcm/fiche.html.twig', [
            'controller_name' => 'PCMController',
            'cyclist' => $cyclist,
            'team' => $team
        ]);
    }





    /**
     * @Route("/importDataPCM", name="import_data_pcm",methods={"GET"})
     */
    public function importCyclist(EntityManagerInterface $em, PcmCyclistsRepository $repo)
    {
        // ENTRER LE NUM DE FICHIER
        //$idFile=1; //952 cyclist
        //$idFile=2; //808 cyclist
        //$idFile=3; //929 cyclist
        //$idFile=4; //965 cyclist
        //$idFile=5; //758 cyclist
        //$idFile=6; //753 cyclist + 1 after without fkIDteam and IrRegion
        //$idFile=0; //

        for($i=1;$i<7;$i++){
            $this->extractDataForCyclist($em, $repo, $i);
        }

        return $this->render('home/index.html.twig', [
            'controller_name' => 'HomeController'
        ]);

    }

    /**
     * @Route("/importTeamPCM", name="import_team_pcm",methods={"GET"})
     */
    public function importTeamPCM(EntityManagerInterface $em, PcmTeamsRepository $repo)
    {

        $crawler = new Crawler();
        $crawler->addXmlContent(file_get_contents('DYN_team.xml'));
        $results = $crawler->filter("NewDataSet");

        $crawler = $crawler->filter("NewDataSet > DYN_team");

        $teams = $crawler->each(
            function (Crawler $node, $i) {
                $rowTeam = $node->children()->each(
                    function (Crawler $node2,$i){
                        $name = $node2->first()->nodeName();
                        $value = $node2->first()->text();
                        return array($name => $value);
                    });
                return $rowTeam;
            }
        );

        foreach ($teams as $team){
            $theTeam = array();
            foreach ($team as $ligne) {
                foreach ($ligne as $key => $value) {
                    $theTeam[$key] = $value;
                }
            }

            dump($theTeam["IDteam"]);
            $elem = $repo->findOneBy([
                'IDTeam' => $theTeam["IDteam"]
            ]);

            if($elem == null){
                $pcmTeam = new PcmTeams();

                if(!array_key_exists("gene_sz_name",$theTeam) ){
                    $theTeam["gene_sz_name"] = -100;
                }
                $pcmTeam->setIDTeam($theTeam["IDteam"]);
                $pcmTeam->setName($theTeam["gene_sz_name"]);

                $em->persist($pcmTeam);
                $em->flush();
                dump("Team created");
            }
        }
        return $this->render('home/index.html.twig', [
            'controller_name' => 'HomeController'
        ]);

    }

    /**
     * @param EntityManagerInterface $em
     * @param PcmCyclistsRepository $repo
     * @param int $idFile
     */
    public function extractDataForCyclist(EntityManagerInterface $em, PcmCyclistsRepository $repo, int $idFile): void
    {
        $crawler = new Crawler();
        $crawler->addXmlContent(file_get_contents('DYN_cyclist' . $idFile . '.xml'));
        $results = $crawler->filter("NewDataSet");

        $crawler = $crawler->filter("NewDataSet > DYN_cyclist");

        $cyclists = $crawler->each(
            function (Crawler $node, $i) {
                $rowCyclist = $node->children()->each(
                    function (Crawler $node2, $i) {
                        $name = $node2->first()->nodeName();
                        $value = $node2->first()->text();
                        return array($name => $value);
                    });
                return $rowCyclist;
            }
        );


        foreach ($cyclists as $cyclist) {
            $theCyclist = array();
            foreach ($cyclist as $ligne) {
                foreach ($ligne as $key => $value) {
                    $theCyclist[$key] = $value;
                }
            }
            $elem = $repo->findOneBy([
                'IDCyclist' => $theCyclist["IDcyclist"]
            ]);

            if ($elem == null) {
                $pcmCyclist = new PcmCyclists();
                if (!array_key_exists("fkIDregion", $theCyclist)) {
                    $theCyclist["fkIDregion"] = -100;
                }
                if (!array_key_exists("fkIDteam", $theCyclist)) {
                    $theCyclist["fkIDteam"] = -100;
                }
                $pcmCyclist->setIDCyclist($theCyclist["IDcyclist"]);
                $pcmCyclist->setBirthdate($theCyclist["gene_i_birthdate"]);
                $pcmCyclist->setCharacIAcceleration($theCyclist["charac_i_acceleration"]);
                $pcmCyclist->setCharacIBaroudeur($theCyclist["charac_i_baroudeur"]);
                $pcmCyclist->setCharacICobble($theCyclist["charac_i_cobble"]);
                $pcmCyclist->setCharacIDownhilling($theCyclist["charac_i_downhilling"]);
                $pcmCyclist->setCharacIEndurance($theCyclist["charac_i_endurance"]);
                $pcmCyclist->setCharacIHill($theCyclist["charac_i_hill"]);
                $pcmCyclist->setCharacIMountain($theCyclist["charac_i_mountain"]);
                $pcmCyclist->setCharacIPlain($theCyclist["charac_i_plain"]);
                $pcmCyclist->setCharacIRecuperation($theCyclist["charac_i_recuperation"]);
                $pcmCyclist->setCharacIResistance($theCyclist["charac_i_resistance"]);
                $pcmCyclist->setCharacITimetrial($theCyclist["charac_i_timetrial"]);
                $pcmCyclist->setCharacISprint($theCyclist["charac_i_sprint"]);
                $pcmCyclist->setCharcIPrologue($theCyclist["charac_i_prologue"]);
                $pcmCyclist->setFirstname($theCyclist["gene_sz_firstname"]);
                $pcmCyclist->setLastname($theCyclist["gene_sz_lastname"]);
                $pcmCyclist->setFkIDregion($theCyclist["fkIDregion"]);
                $pcmCyclist->setPopularity($theCyclist["gene_f_popularity"]);
                $pcmCyclist->setSize($theCyclist["gene_i_size"]);
                $pcmCyclist->setWeight($theCyclist["gene_i_weight"]);
                $pcmCyclist->setFkIDteam($theCyclist["fkIDteam"]);

                $em->persist($pcmCyclist);
                $em->flush();
                dump("Cyclist created");
            }
        }
    }


}