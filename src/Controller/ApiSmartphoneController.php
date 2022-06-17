<?php

namespace App\Controller;

use App\Entity\Smartphone;
use App\Repository\SmartphoneRepository;
use JMS\Serializer\SerializationContext;
use JMS\Serializer\SerializerInterface;
use Pagerfanta\Adapter\ArrayAdapter;
use Pagerfanta\Pagerfanta;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class ApiSmartphoneController extends AbstractController
{
    /**
     * @Route("/api/smartphone", name="api_index_smartphone", methods={"GET"})
     */
    public function showAll(Request $request, SmartphoneRepository $smartphoneRepository, SerializerInterface $serializer): Response
    {
        $smartphones = $smartphoneRepository->findAll();

        $adapter = new ArrayAdapter($smartphones);
        $pagerfanta = new Pagerfanta($adapter);

        // Get the actual page in url (default: 1)
        $actualPage = $request->query->get('page') ? $request->query->get('page'): 1;

        $limit = 5;

        // Check if the actual page is superior to smartphones count
        if($limit * $actualPage > count($smartphones)) {
            $response = new Response('Paramètres de pages incorrect', 404, [
                "Content-Type' => 'application/json"
            ]);

            return $response;
        }

        $pagerfanta->setMaxPerPage($limit); // 5 items per page
        $pagerfanta->setCurrentPage($actualPage); // 1 by default

        $currentPageResults = $pagerfanta->getCurrentPageResults();

        $json = $serializer->serialize($currentPageResults, 'json', SerializationContext::create());

        $response = new Response($json, 200, [
            "Content-Type' => 'application/json"
        ]);
        
        return $response;
    }

    /**
     * @Route("/api/smartphone/{id}", name="api_show_smartphone", methods={"GET"})
     */
    public function showProduct(int $id, SmartphoneRepository $smartphoneRepository, SerializerInterface $serializer): Response
    {
        $smartphone = $smartphoneRepository->findBy(['id' => $id]);

        $json = $serializer->serialize($smartphone, 'json', SerializationContext::create());

        if(! $smartphone) {
            $response = new Response('Smartphone non trouvé', 404, [
                "Content-Type' => 'application/json"
            ]);

            return $response;
        }
        
        $response = new Response($json, 200, [
            "Content-Type' => 'application/json"
        ]);
        
        return $response;
    }
}