<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\UserType;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

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

    /**
     * @Route("/api/user/add", name="api_add_user", methods={"POST"})
     */
    public function addUser(Request $request, SerializerInterface $serializer, EntityManagerInterface $entityManagerInterface, UserPasswordHasherInterface $passwordHasher, UserRepository $userRepository): Response
    {
        // On récupère le json envoyé au back 
        $jsonPost = $request->getContent();

        // On déserialise pour convertir le json avec l'entité User
        $user = $serializer->deserialize($jsonPost, User::class, 'json', ['groups' => 'user:add']);

        // On crée le form user
        $form = $this->createForm(UserType::class, $user);  

        // On recherche un utilisateur par mail (id unique)
        $checkUser = $userRepository->findByEmail(['email' => $user->getEmail()]);
        
        // Si l'on trouve un resultat, on renvoie un message disant que l'utilisateur existe 
        // et on retourne une réponse 
        if($checkUser){
            $response = new Response("Mail déjà existant", 200, [
                "Content-Type' => 'application/json"
            ]);
    
            return $response;
        }

         // On récupère le password brut, on hash le password et on le set
        $plaintextPassword = $user->getPassword(); 

        $hashedPassword = $passwordHasher->hashPassword(
            $user,
            $plaintextPassword
        );

        $user->setPassword($hashedPassword);

        // On persist l'utilisateur et on renvoie une réponse 201
        $entityManagerInterface->persist($user);
        $entityManagerInterface->flush();

        $response = new Response($jsonPost, 201, [
            "Content-Type' => 'application/json"
        ]);

        return $response;
    }
}
