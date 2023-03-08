<?php

namespace App\Services;

use Symfony\Contracts\HttpClient\HttpClientInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Session\SessionInterface;

class DataCitiesService
{
    protected $session ;

    public function __construct(SessionInterface $session)
    {
        $this->$session = $session;
    }

    public function initializeDatas()
    {
        // $datasCities = $this->$session->get('datasCities',[]);
        // if(empty($datasCities)){
        //     $repository = $this->getDoctrine()->getRepository(Cities::class);
        //     $cities = $repository->findAll();
        //     $datasToAdd = array();
        //     if($cities !== null){
        //         foreach($cities as $oneCity){
        //             $datasToAdd[$oneCity->getCityName()]= $callApiService->getData($oneCity->getCityName());
        //         }
        //     }
            
        //     $session->set('datasCities', $datasToAdd);
        //}
    }

    public function remove(string $city){
         // modifications des données dans la session
        $datasCities = $this->$session->get('datasCities',[]);
        if(!empty($datasCities[$city])){
            unset($datasCities[$city]);
        }

        $this->$session->set('datasCities', $datasCities);
    }
}