<?php

namespace App\Controller;

use App\Services\CallApiExtService;
use App\Services\DataCitiesService;
use App\Entity\Cities;
use App\Entity\User;
use mysqli;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

use Symfony\Component\HttpFoundation\Session\SessionInterface;

use Symfony\Component\HttpFoundation\Request;
use App\Form\SearchType;
use Doctrine\Persistence\ObjectManager;
use Doctrine\ORM\EntityManagerInterface;

class HomeController extends AbstractController
{

    #[Route('/', name: 'homepage')]
    public function index(CallApiExtService $callApiService, Request $request, SessionInterface $session, EntityManagerInterface $em): Response
    {   
        // récupération utilisateur
        $username = null;
        $user =null;
        if($this->getUser() != null){
            $username = $this->getUser()->getPseudo();
            $user = $em->getRepository(User::class)->findOneBy(
                ['email' => $this->getUser()->getEmail()],
            );
        }

        // recupération des infos depuis la BDD et ajout au tableau de la session
        $datasCities = $session->get('datasCities',[]);
        if($user != null){
            $cities = $user->getFavorite();
            
            // $cities = $em->getRepository(Cities::class)->findAll();
            if($cities !== null){
                foreach($cities as $oneCity){
                    $datasCities[$oneCity->getCityName()]= $callApiService->getData($oneCity->getCityName());
                }
            }
            
            $session->set('datasCities', $datasCities);
        }


        // utilisation d'un formulaire pour le input
        $cityChoice = new Cities();
        $form = $this->createForm(SearchType::class, $cityChoice);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $newCity = ucfirst($form->getData()->getCityName());
            $dataCity = $callApiService->getData($newCity);

            //  vérification que l'on a bien une ville 
            if(count($dataCity) >0){
                // ajout au tableau (via la session)
                $datasCities = $session->get('datasCities',[]);
                $datasCities[$newCity] = $dataCity;
                $session->set('datasCities', $datasCities);

                // ajout en BDD si on a un user
                if($user != null){
                    // recherche de la ville et du user
                    $cityToAdd = $em->getRepository(Cities::class)->findOneBy(
                        ['CityName' => $newCity],
                    );
                    
                    
                    // gestion de la BDD pour la ville
                    if($cityToAdd === null){
                        // on créer la ville en BDD
                        $cityToAdd = new Cities();
                        $cityToAdd->setCityName( $newCity);
                        $em->persist($cityToAdd);
                    }

                    // ajout à l'utilisateur
                    $user->addFavorite($cityToAdd);
                    $em->flush();
                }
            }

            // remise à zéro du formulaire
            unset($form);
            $cityChoice = new Cities();
            $form = $this->createForm(SearchType::class, $cityChoice);
        }

        

        $datasCities = $session->get('datasCities',[]);
        // transmission des données à la vue
        return $this->render('home/index.html.twig', [
            'controller_name' => 'HomeController',
            'datasCities' => $datasCities,
            'formSearch' => $form->createView(),
            'username' => $username,
        ]);
    }


    #[Route('/remove/{city}', name: 'remove_city')]
    public function remove(String $city, SessionInterface $session, EntityManagerInterface $em)
    {   
        // modifications des données dans la session
        $datasCities = $session->get('datasCities',[]);
        if(!empty($datasCities[$city])){
            unset($datasCities[$city]);
        }
        
        // modification dans la BDD si on a un user
        if($this->getUser()  != null){
            $user = $em->getRepository(User::class)->findOneBy(
                ['email' => $this->getUser()->getEmail()],
            );
            $cityToRemove = $em->getRepository(Cities::class)->findOneBy(
                ['CityName' => $city],
            );
            $user->removeFavorite($cityToRemove);
            $em->flush();
        }

        $session->set('datasCities', $datasCities);
        // $dataCitiesServcice->remove($city);

        return $this->redirectToRoute("homepage");
    }
}
