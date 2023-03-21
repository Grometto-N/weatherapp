<?php

namespace App\Controller;

use App\Services\CallApiExtService;
use App\Services\DataCitiesService;
use App\Services\HandleDBService;

use App\Entity\Cities;
use App\Entity\User;
use App\Form\SearchType;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\Security;


use Doctrine\ORM\EntityManagerInterface;

class HomeController extends AbstractController
{

    #[Route('/', name: 'homepage')]
    public function index(CallApiExtService $callApiService, DataCitiesService $datasCities, Request $request, SessionInterface $session, Security $security, HandleDBService $handleDB): Response
    {   
        // récupération des données à afficher
        $datasCities->initializeDatas($callApiService);

        // utilisation d'un formulaire pour gérer le input
        $cityChoice = new Cities();
        $form = $this->createForm(SearchType::class, $cityChoice);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            // récupération des données de météo (gestion du format de string de l'input)
            $newCity = ucfirst($form->getData()->getCityName());
            $dataCity = $callApiService->getData($newCity);

            //  vérification que l'on a bien une ville 
            if(count($dataCity) >0){
                // ajout au tableau (via la session)
                $datasCities->add($newCity, $dataCity);

                // ajout en BDD si on a un user
                if($security->getUser() != null){
                    
                    $handleDB->addCity($newCity);
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
            'username' => $security->getUser()->getPseudo(),
        ]);
    }


    #[Route('/remove/{city}', name: 'remove_city')]
    public function remove(string $city, DataCitiesService $datasCities)  
    {   
        // modifications des données dans la session 
        $datasCities->remove($city);

        return $this->redirectToRoute("homepage");
    }
}
