<?php

namespace App\Controller;

use App\Repository\UserRepository;
use Doctrine\Persistence\ManagerRegistry;
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

        //$usersNormalises = $normalizer->normalize($users, null, ['groups' => 'user:list']);

        $json = $serializer->serialize($users, 'json', ['groups' => 'user:list']);
        
        $response = new Response($json, 200, [
            "Content-Type' => 'application/json"
        ]);
        
        return $response;
    }

    /**
     * @Route("/api/user/{id}", name="api_show_user", methods={"GET"})
     */
    public function showClient(int $id, UserRepository $userRepository, SerializerInterface $serializer): Response
    {
        $user = $userRepository->findBy(['id' => $id]);

        //$normalizers = [new ObjectNormalizer()];
        //$serializer = new Serializer($normalizers, []);

        //$usersNormalises = $normalizer->normalize($users, null, ['groups' => 'user:single']);

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

    /**
     * @Route("/api/user/{id}", name="api_delete_user", methods={"DELETE"})
     */
    public function deleteUser(int $id, ManagerRegistry $doctrine, UserRepository $userRepository, SerializerInterface $serializer): Response
    {        
        // Recherche un utilisateur uniquement sur son id
        $user = $userRepository->findOneBy(['id' => $id]);

        //Si l'utilisateur existe, on le supprime
        if($user) {
            $entityManager = $doctrine->getManager();
            
            $entityManager->remove($user);
            $entityManager->flush();

            $json = $serializer->serialize($user, 'json', ['groups' => 'user:single']);

            $response = new Response($json, 200, [
                "Content-Type' => 'application/json"
            ]);

            return $response;
        }
        
        // Sinon on envoie une erreur 404
        $response = new Response("Utilisateur non trouvé", 404, [
            "Content-Type' => 'application/json"
        ]);
        
        return $response;
    }
}
