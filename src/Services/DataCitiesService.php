<?php

namespace App\Services;


use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Security\Core\Security;
use App\Entity\User;

use Doctrine\ORM\EntityManagerInterface;


class DataCitiesService
{

    private SessionInterface $session;
    // private Security $security;
    private EntityManagerInterface $em;
    private User $user;
    
    public function __construct(SessionInterface $session, Security $security, EntityManagerInterface $em)
    {   
        $this->session = $session;
        $this->user = $security->getUser();
        $this->em = $em;
    }

    // public function initializeDatas()
    // {
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
    // }

    public function remove(string $city) {
        
         // modifications des données dans la session
        $datasCities = $this->session->get('datasCities',[]);
        if(!empty($datasCities[$city])){
            unset($datasCities[$city]);
        }
        $this->session->set('datasCities', $datasCities);

        // modification dans la BDD si on a un user
        if($this->user != null){
            $user = $this->em->getRepository(User::class)->findOneBy(
                ['email' => $this->user->getEmail()],
            );
            $cityToRemove = $this->em->getRepository(Cities::class)->findOneBy(
                ['CityName' => $city],
            );
            $user->removeFavorite($cityToRemove);
            $this->em->flush();
        }

    }
}