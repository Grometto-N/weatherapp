<?php

namespace App\Controller;

use App\Services\CallApiExtService;
use App\Entity\Cities;
use mysqli;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

use Symfony\Component\HttpFoundation\Session\SessionInterface;

use Symfony\Component\HttpFoundation\Request;
use App\Form\SearchType;
use Doctrine\Persistence\ObjectManager;

class HomeController extends AbstractController
{

    #[Route('/', name: 'homepage')]
    public function index(CallApiExtService $callApiService, Request $request, SessionInterface $session): Response
    {
        // recupération des infos dans la session et ajout des données en base de données
        $datasCities = $session->get('datasCities',[]);
        dump($datasCities);

        if(empty($datasCities)){
            $repository = $this->getDoctrine()->getRepository(Cities::class);
            $cities = $repository->findAll();
            $datasToAdd = array();
            if($cities !== null){
                foreach($cities as $oneCity){
                    $datasToAdd[$oneCity->getCityName()]= $callApiService->getData($oneCity->getCityName());
                }
            }
            
            $session->set('datasCities', $datasToAdd);
        }

        // utilisation d'un formulaire pour le input
        $cityChoice = new Cities();
        $form = $this->createForm(SearchType::class, $cityChoice);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $dataCity = $callApiService->getData($form->getData()->getCityName());

            //  vérification que l'on a bien une ville
            if(count($dataCity) >0){
                // ajout en BDD
                $em = $this->getDoctrine()->getManager();
                $em->persist($form->getData());
                $em->flush();
                // ajout au tableau (via la session)
                $datasCities = $session->get('datasCities',[]);
                $datasCities[$form->getData()->getCityName()] = $dataCity;
                $session->set('datasCities', $datasCities);
            }
            $datasCities = $session->get('datasCities',[]);


            // remise à zéro du formulaire
            unset($form);
            $cityChoice = new Cities();
            $form = $this->createForm(SearchType::class, $cityChoice);
        }

        $username = null;
        if($this->getUser() != null){
            $username = $this->getUser()->getPseudo();
        }


        // transmission des données à la vue
        return $this->render('home/index.html.twig', [
            'controller_name' => 'HomeController',
            'datasCities' => $datasCities,
            'formSearch' => $form->createView(),
            'username' => $username,
        ]);
    }


    #[Route('/remove/{city}', name: 'remove_city')]
    public function remove(String $city, CallApiExtService $callApiService, Request $request,SessionInterface $session)
    {   
        // modifications des données dans la session
        $datasCities = $session->get('datasCities',[]);
        if(!empty($datasCities[$city])){
            unset($datasCities[$city]);
        }

        $session->set('datasCities', $datasCities);

        return $this->redirectToRoute("homepage");
    }
}
