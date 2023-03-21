<?php

namespace App\Services;


use Symfony\Component\Security\Core\Security;

use App\Entity\User;
use App\Entity\Cities;

use Doctrine\ORM\EntityManagerInterface;


// gestion des données à afficher via la section

class HandleDBService
{

    private EntityManagerInterface $em;
    private User $user;
    
    public function __construct(Security $security, EntityManagerInterface $em)
    {   
        $this->user = $security->getUser();
        $this->em = $em;
    }

    // ajout d'une ville à l'utilisateur
    public function addCity(string $city) : void
    {   
        $userDB = $this->em->getRepository(User::class)->findOneBy(
            ['email' => $this->user->getEmail()],
        );

        $cityToAdd = $this->em->getRepository(Cities::class)->findOneBy(
            ['CityName' => $city],
        );

        if($cityToAdd === null){
            // on créer la ville en BDD
            $cityToAdd = new Cities();
            $cityToAdd->setCityName($city);
            $this->em->persist($cityToAdd);
        }

        $userDB->addFavorite($cityToAdd);
        $this->em->flush();
    }
}