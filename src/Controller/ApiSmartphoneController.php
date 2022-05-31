<?php

namespace App\Controller;

use App\Entity\Smartphone;
use App\Repository\SmartphoneRepository;
use JMS\Serializer\SerializationContext;
use JMS\Serializer\SerializerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class ApiSmartphoneController extends AbstractController
{
    /**
     * @Route("/api/smartphone", name="api_index_smartphone", methods={"GET"})
     */
    public function showAll(SmartphoneRepository $smartphoneRepository, SerializerInterface $serializer): Response
    {
        $smartphones = $smartphoneRepository->findAll();

        $json = $serializer->serialize($smartphones, 'json', SerializationContext::create());

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