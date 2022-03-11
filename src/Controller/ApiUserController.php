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
     * @Route("/api/user", name="api_index_user", methods={"GET"})
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

    /**
     * @Route("/api/user/{id}", name="api_post_user_single", methods={"GET"})
     */
    public function showClient(int $id, UserRepository $userRepository, SerializerInterface $serializer): Response
    {
        $user = $userRepository->findBy(['id' => $id]);

        //$normalizers = [new ObjectNormalizer()];
        //$serializer = new Serializer($normalizers, []);

        //$smartphonesNormalises = $normalizer->normalize($smartphones, null, ['groups' => 'smartphone:read']);

        $json = $serializer->serialize($user, 'json', ['groups' => 'user:single']);

        if(! $user) {
            $response = new Response('Utilisateur non trouvé', 404, [
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
