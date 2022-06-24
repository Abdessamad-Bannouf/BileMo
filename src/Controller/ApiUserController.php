<?php

namespace App\Controller;

use App\Entity\Shop;
use App\Entity\User;
use App\Form\UserType;
use App\Repository\ShopRepository;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\Persistence\ManagerRegistry;
use JMS\Serializer\DeserializationContext;
use JMS\Serializer\SerializationContext;
use JMS\Serializer\SerializerBuilder;
use JMS\Serializer\SerializerInterface;
use Nelmio\ApiDocBundle\Annotation\Model;
use Nelmio\ApiDocBundle\Annotation\Security;
use OpenApi\Annotations as OA;
use Pagerfanta\Adapter\ArrayAdapter;
use Pagerfanta\Pagerfanta;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class ApiUserController extends AbstractController
{
    /**
     * @Route("/api/user", name="api_index_user", methods={"GET"})
     * @OA\Response(
     *     response=200,
     *     description="Returns the list of users",
     *     @OA\JsonContent(
     *        type="array",
     *        @OA\Items(ref=@Model(type=User::class, groups={"user:list"}))
     *     )
     * )
     */
    public function showAll(Request $request, UserRepository $userRepository, SerializerInterface $serializer): Response
    {
        $users = $userRepository->findAll();

        $adapter = new ArrayAdapter($users);
        $pagerfanta = new Pagerfanta($adapter);

        // Get the actual page in url (default: 1)
        $actualPage = $request->query->get('page') ? $request->query->get('page'): 1;

        $limit = 5;

        // Check if the actual page is superior to users count
        if($limit * $actualPage > count($users)) {
            $response = new Response('Paramètres de pages incorrect', 404, [
                "Content-Type' => 'application/json"
            ]);

            return $response;
        }

        $pagerfanta->setMaxPerPage($limit); // 5 items per page
        $pagerfanta->setCurrentPage($actualPage); // 1 by default

        $currentPageResults = $pagerfanta->getCurrentPageResults();

        $json = $serializer->serialize($currentPageResults, 'json', SerializationContext::create()->setGroups(array('user:list')));

        $response = new Response($json, 200, [
            "Content-Type' => 'application/json"
        ]);
        
        return $response;
    }

    /**
     * @Route("/api/user/{id}", name="api_show_user", methods={"GET"})
     *  @OA\Response(
     *     response=200,
     *     description="Return an user by id",
     *     @OA\JsonContent(
     *        type="array",
     *        @OA\Items(ref=@Model(type=User::class, groups={"user:single"}))
     *     )
     * )

     */
    public function showClient(int $id, UserRepository $userRepository, SerializerInterface $serializer): Response
    {
        $user = $userRepository->findBy(['id' => $id]);
        
        $json = $serializer->serialize($user, 'json', SerializationContext::create()->setGroups(array('user:single')));

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
     * @OA\Response(
     *     response=200,
     *     description="Delete an user by id",
     *     @OA\JsonContent(
     *        type="array",
     *        @OA\Items(ref=@Model(type=User::class, groups={"user:delete"}))
     *     )
     * )
     */
    public function deleteUser(int $id, ManagerRegistry $doctrine, UserRepository $userRepository, SerializerInterface $serializer): Response
    {        
        // Recherche un utilisateur uniquement sur son id
        $user = $userRepository->findOneBy(['id' => $id]);

        //Si l'utilisateur existe, on le supprime
        if($user) {
            $json = $serializer->serialize($user, 'json', SerializationContext::create()->setGroups(array('user:delete')));
            $entityManager = $doctrine->getManager();
            
            $entityManager->remove($user);
            $entityManager->flush();

            //$json = $serializer->serialize($user, 'json', SerializationContext::create()->setGroups(array('user:single')));

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
     * @Route("/api/user", name="api_add_user", methods={"POST"})
     * @OA\Response(
     *     response=201,
     *     description="Add an user",
     *     @OA\JsonContent(
     *        type="array",
     *        @OA\Items(ref=@Model(type=User::class))
     *     )
     * )
     */
    public function addUser(Request $request, SerializerInterface $serializer, ShopRepository $shopRepository, EntityManagerInterface $entityManagerInterface, UserPasswordHasherInterface $passwordHasher, UserRepository $userRepository): Response
    {
        // On récupère le json envoyé au back 
        $data = json_decode($request->getContent(), true);
        $user = new User();
        
        $form = $this->createForm(UserType::class, $user);
        $form->submit($data);

        // On récupère le password brut, on hash le password et on le set
        $plaintextPassword = $user->getPassword(); 

        $hashedPassword = $passwordHasher->hashPassword(
            $user,
            $plaintextPassword
        );
        
        $user->setPassword($hashedPassword);

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

        // On persist l'user
        $entityManagerInterface->persist($user);
        $entityManagerInterface->flush();

        $json = $serializer->serialize($user, 'json', SerializationContext::create()->setGroups(array('user:single')));

        $response = new Response($json, 201, [
            "Content-Type' => 'application/json"
        ]);

        return $response;
    }
}
