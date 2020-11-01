<?php


namespace App\Services;

use Symfony\Contracts\HttpClient\HttpClientInterface;


class FutDb
{

    private $client;

    public function __construct(HttpClientInterface $client)
    {
        $this->client = $client;
    }

    public function fetchClubsAll(){
        //$count = $this->countClubs();
        $count = 797;
        $jsonCompil = array();

        $nbPages = $count/20;
        if($count%20 > 0 ){
            $nbPages++;
        }

        for ($i = 3; $i <= 40; $i++) {
            $result = $this->fetchClubs($i,20);
                array_push($jsonCompil,$result);
        }

        dump($jsonCompil);

        return json_encode($jsonCompil);

    }


    public function fetchClubs(int $page, int $limit){

        dump('before '.$page);
        $response = $this->client->request(
            'GET',
            'https://futdb.app/api/clubs?page='.$page.'&limit='.$limit,
            [
            'headers' =>
                [
                    'Accept' => 'application/json',
                    'X-AUTH-TOKEN' => '38d8679e-f263-4e44-95b3-91779e471364'
                ]
            ]
        );


        $content = $response->getContent();

        $jsonArr = json_decode($content, true);
        $jsonCompil = $jsonArr['items'];
        dump($jsonCompil) ;
        return $jsonCompil;
    }

    public function fetchLeagues(int $page, int $limit){

        dump('before '.$page);
        $response = $this->client->request(
            'GET',
            'https://futdb.app/api/leagues?page='.$page.'&limit='.$limit,
            [
                'headers' =>
                    [
                        'Accept' => 'application/json',
                        'X-AUTH-TOKEN' => '38d8679e-f263-4e44-95b3-91779e471364'
                    ]
            ]
        );


        $content = $response->getContent();

        $jsonArr = json_decode($content, true);
        $jsonCompil = $jsonArr['items'];
        dump($jsonCompil) ;
        return $jsonCompil;
    }

    public  function  countClubs(): int{
        $response = $this->client->request(
            'GET',
            'https://futdb.app/api/clubs',
            [
                'headers' =>
                    [
                        'Accept' => 'application/json',
                        'X-AUTH-TOKEN' => '38d8679e-f263-4e44-95b3-91779e471364'
                    ]
            ]
        );

        $content = $response->getContent();

        $json = json_decode($content, true);
        $count = $json['count_total'];
        return $count;

    }

}