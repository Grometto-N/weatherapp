<?php

namespace App\Services;


use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Security\Core\Security;

use App\Entity\User;
use App\Entity\Cities;
use App\Services\CallApiExtService;

use Doctrine\ORM\EntityManagerInterface;


// gestion des données à afficher via la section

class DataCitiesService
{

    private SessionInterface $session;
    private EntityManagerInterface $em;
    private User $user;
    
    public function __construct(SessionInterface $session, Security $security, EntityManagerInterface $em)
    {   
        $this->session = $session;
        $this->user = $security->getUser();
        $this->em = $em;
    }

    // initialisation des données dans le tableau de session
    public function initializeDatas(CallApiExtService $callApiService)
    {
        // récupération utilisateur
        $userDB = null;
        if($this->user != null){
            $userDB = $this->em->getRepository(User::class)->findOneBy(
                ['email' => $this->user->getEmail()],
            );
        }

        // recupération des infos depuis la BDD et ajout au tableau de la session
        $datasCities = $this->session->get('datasCities',[]);
        if($this->user != null){
            $cities = $userDB->getFavorite();
            
            // $cities = $em->getRepository(Cities::class)->findAll();
            if($cities !== null){
                foreach($cities as $oneCity){
                    $datasCities[$oneCity->getCityName()]= $callApiService->getData($oneCity->getCityName());
                }
            }
            
            $this->session->set('datasCities', $datasCities);
        }
    }

    // ajout d'une ville dans le tableau en session
    public function add(string $city, array $dataCity):void 
    {
        $datasCities = $this->session->get('datasCities',[]);
        $datasCities[$city] = $dataCity;
        $this->session->set('datasCities', $datasCities);
    }


    // suppression d'une ville dans la session
    public function remove(string $city) : void
    {
        
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