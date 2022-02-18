<?php

namespace App\Controller;

use App\Repository\SmartphoneRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;
use Symfony\Component\Serializer\Serializer;
use Symfony\Component\Serializer\SerializerInterface;

class ApiSmartphoneController extends AbstractController
{
    /**
     * @Route("/api/smartphone", name="api_post_smartphone", methods={"GET"})
     */
    public function showAll(SmartphoneRepository $smartphoneRepository, SerializerInterface $serializer): Response
    {
        $smartphones = $smartphoneRepository->findAll();

        //$normalizers = [new ObjectNormalizer()];
        //$serializer = new Serializer($normalizers, []);

        //$smartphonesNormalises = $normalizer->normalize($smartphones, null, ['groups' => 'smartphone:read']);

        $json = $serializer->serialize($smartphones, 'json', ['groups' => 'smartphone:list']);
        
        $response = new Response($json, 200, [
            "Content-Type' => 'application/json"
        ]);
        
        return $response;
    }
}
