<?php

namespace App\Controller;

use App\Repository\UserRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\SerializerInterface;

class ApiUserController extends AbstractController
{
    /**
     * @Route("/api/user", name="api_post_user", methods={"GET"})
     */
    public function showAll(UserRepository $userRepository, SerializerInterface $serializer): Response
    {
        $users = $userRepository->findAll();

        //$normalizers = [new ObjectNormalizer()];
        //$serializer = new Serializer($normalizers, []);

        //$usersNormalises = $normalizer->normalize($users, null, ['groups' => 'smartphone:read']);

        $json = $serializer->serialize($users, 'json', ['groups' => 'user:list']);
        
        $response = new Response($json, 200, [
            "Content-Type' => 'application/json"
        ]);
        
        return $response;
    }
}
