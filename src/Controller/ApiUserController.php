<?php

namespace App\Controller;

use App\Entity\Customer;
use App\Entity\User;
use App\Form\UserType;
use App\Repository\UserRepository;
use App\Service\PaginationService;
use App\Service\ValidationService;
use Doctrine\Persistence\ManagerRegistry;
use JMS\Serializer\SerializationContext;
use JMS\Serializer\SerializerInterface;
use JsonException;
use Nelmio\ApiDocBundle\Annotation\Model;
use OpenApi\Annotations as OA;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Entity;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;

class ApiUserController extends AbstractController
{
    private $request;
    private $userRepository;
    private $serializer;
    private $em;

    public function __construct(RequestStack $requestStack, UserRepository $userRepository, SerializerInterface $serializer, ManagerRegistry $managerRegistry)
    {
        $this->request = $requestStack->getCurrentRequest();
        $this->userRepository = $userRepository;
        $this->serializer = $serializer;
        $this->em = $managerRegistry;
    }

    /**
     * @Route("/api/users", name="api_index_user", methods={"GET"})
     * @OA\Response(
     *     response=200,
     *     description="Returns the list of users",
     *     @OA\JsonContent(
     *        type="array",
     *        @OA\Items(ref=@Model(type=User::class, groups={"user:list"}))
     *     )
     * )
     */
    public function showAll(PaginationService $paginationService): Response
    {
        $users = $this->userRepository->findAll();

        $response = $paginationService->getPagination($users, 5, 'user:list');
        
        return $response;
    }

    /**
     * @Route("/api/customers/{customer_id}/users/{id}", name="api_show_user", methods={"GET"})
     * @Entity("customer", expr="repository.find(customer_id)")
     *  @OA\Response(
     *     response=200,
     *     description="Return an user by id",
     *     @OA\JsonContent(
     *        type="array",
     *        @OA\Items(ref=@Model(type=User::class, groups={"user:single"}))
     *     )
     * )
     */
    public function showClient(Customer $customer = null, User $user = null): Response
    {
        $this->checkUser($user);

        // if user can't be seen by the current user
        /*if (!$this->isGranted("SEE", $user)) {
            throw new JsonException("Vous n'avez pas les droits requis pour faire cette requête", JsonResponse::HTTP_UNAUTHORIZED);
        }*/

        $user = $this->userRepository->findBy(['id' => $user, 'customer' => $customer]);

        $json = $this->serializer->serialize($user, 'json', SerializationContext::create()->setGroups(array('user:single')));

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
     * @Route("/api/customers/{id}/users", name="api_add_user", methods={"POST"})
     * @OA\Response(
     *     response=201,
     *     description="Add an user link to a customer",
     *     @OA\JsonContent(
     *        type="array",
     *        @OA\Items(ref=@Model(type=Customer::class))
     *     )
     * )
     */
    public function addUser(Customer $customer, Request $request, ValidationService $validationService): Response
    {
        $data = json_decode($request->getContent(), true);
        $user = new User();
        
        $form = $this->createForm(UserType::class, $user);
        $form->submit($data);

        // On set le customer passé en url
        $user->setCustomer($customer);
        
        // On appelle le validation service pour checker que tout est ok (entité + voir si un utilisateur est dans la bdd)
        $errors = $validationService->isValid($user);

        if($errors)
            return $errors;

        // On persist le customer
        $em = $this->em->getManager();

        $em->persist($user);
        $em->flush();

        $json = $this->serializer->serialize($user, 'json', SerializationContext::create());

        $response = new Response($json, 201, [
            "Content-Type' => 'application/json"
        ]);

        return $response;
    }

    /**
     * @Route("/api/users/{id}", name="api_delete_user", methods={"DELETE"})
     * @OA\Response(
     *     response=200,
     *     description="Delete an user by id",
     *     @OA\JsonContent(
     *        type="array",
     *        @OA\Items(ref=@Model(type=User::class, groups={"user:delete"}))
     *     )
     * )
     */
    public function deleteUser(User $user = null): Response
    {   
        $this->checkUser($user);
    
        // Recherche un utilisateur uniquement sur son id
        $user = $this->userRepository->findOneBy(['id' => $user]);

        //Si l'utilisateur existe, on le supprime
        if($user) {
            $json = $this->serializer->serialize($user, 'json', SerializationContext::create()->setGroups(array('user:delete')));

            $em = $this->em->getManager();
            $em->remove($user);
            $em->flush();

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

    protected function checkUser($user)
    {
        // Si l'utilisateur n'est pas trouvé
        if (!$user || !($user instanceof User)) {
            throw new JsonException("Identifiant incorrect ou bien du client n'est pas trouvé", JsonResponse::HTTP_NOT_FOUND);
        }
    }
}
