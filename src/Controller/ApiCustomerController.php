<?php

namespace App\Controller;

use App\Entity\Customer;
use App\Form\CustomerType;
use App\Repository\CustomerRepository;
use App\Service\ValidationService;
use Doctrine\Persistence\ManagerRegistry;
use JMS\Serializer\SerializationContext;
use JMS\Serializer\SerializerInterface;
use JsonException;
use Nelmio\ApiDocBundle\Annotation\Model;
use OpenApi\Annotations as OA;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;

class ApiCustomerController extends AbstractController
{
    private $serializer;
    private $customerRepository;
    private $em;

    public function __construct(SerializerInterface $serializer, CustomerRepository $customerRepository, ManagerRegistry $managerRegistry, ValidationService $validationService)
    {
        $this->serializer = $serializer;
        $this->customerRepository = $customerRepository;
        $this->em = $managerRegistry;
    }

    /**
     * @Route("/api/customer/{id}/user", name="api_index_user_customer", methods={"GET"})
     *  @OA\Response(
     *     response=200,
     *     description="Returns all users of a specific customer",
     *     @OA\JsonContent(
     *        type="array",
     *        @OA\Items(ref=@Model(type=Smartphone::class, groups={"smartphone:single"}))
     *     )
     * )
     */
    public function showUsersByCustomer(Customer $customer = null): Response
    {
        if($customer !== null) {
            $users = $customer->getUsers();
            $json = $this->serializer->serialize($users, 'json', SerializationContext::create()->setGroups(array('customer:single')));

            $response = new Response($json, 200, [
                "Content-Type' => 'application/json"
            ]);

            return $response;
        }

        // Sinon on envoie une erreur 404
        $response = new Response("Customer non trouvé", 404, [
            "Content-Type' => 'application/json"
        ]);
        
        return $response;
    }

    /**
     * @Route("/api/customer", name="api_add_customer", methods={"POST"})
     * @OA\Response(
     *     response=201,
     *     description="Add an customer",
     *     @OA\JsonContent(
     *        type="array",
     *        @OA\Items(ref=@Model(type=Customer::class))
     *     )
     * )
     */
    public function addCustomer(Request $request, ValidationService $validationService, UserPasswordHasherInterface $passwordHasher): Response
    {
        $data = json_decode($request->getContent(), true);
        $customer = new Customer();
        
        $form = $this->createForm(CustomerType::class, $customer);
        $form->submit($data);

        // On appelle le validation service pour checker que tout est ok (entité + voir si un utilisateur est dans la bdd)
        $errors = $validationService->isValid($customer);

        if($errors)
            return $errors;

        // On hash le password
        $hashedPassword = $passwordHasher->hashPassword(
            $customer,
            $customer->getPassword()
        );
        $customer->setPassword($hashedPassword);

        // On persist le customer
        $entityManager = $this->em->getManager();

        $entityManager->persist($customer);
        $entityManager->flush();

        $json = $this->serializer->serialize($customer, 'json', SerializationContext::create()->setGroups(array('customer:add')));

        $response = new Response($json, 201, [
            "Content-Type' => 'application/json"
        ]);

        return $response;
    }

    /**
     * @Route("/api/customer/{id}", name="api_delete_customer", methods={"DELETE"})
     * @OA\Response(
     *     response=200,
     *     description="Delete an customer by id",
     *     @OA\JsonContent(
     *        type="array",
     *        @OA\Items(ref=@Model(type=Customer::class, groups={"customer:delete"}))
     *     )
     * )
     */
    public function deleteCustomer(Customer $customer = null): Response
    {        
        if (!$this->isGranted("REMOVE", $customer)) {
            throw new JsonException("Vous n'avez pas les droits requis pour faire cette requête", JsonResponse::HTTP_UNAUTHORIZED);
        }

        // Recherche un utilisateur uniquement sur son id
        $user = $this->customerRepository->findOneBy(['id' => $customer]);

        //Si l'utilisateur existe, on le supprime
        if($user) {
            $json = $this->serializer->serialize($user, 'json', SerializationContext::create()->setGroups(array('customer:delete')));

            $entityManager = $this->em->getManager();
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

    protected function checkCustomer($customer)
    {
        // Si l'utilisateur n'est pas trouvé
        if (!$customer || !($customer instanceof Customer)) {
            throw new JsonException("Identifiant incorrect ou bien du client n'est pas trouvé", JsonResponse::HTTP_NOT_FOUND);
        }
    }
}