<?php

namespace App\Controller;

use App\Entity\Customer;
use App\Repository\CustomerRepository;
use JMS\Serializer\SerializationContext;
use JMS\Serializer\SerializerInterface;
use Nelmio\ApiDocBundle\Annotation\Model;
use OpenApi\Annotations as OA;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class ApiCustomerController extends AbstractController
{
    private $serializer;

    public function __construct(SerializerInterface $serializer)
    {
        $this->serializer = $serializer;
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
    public function showUsersByCustomer(Customer $customer): Response
    {
        $users = $customer->getUsers();

        $json = $this->serializer->serialize($users, 'json', SerializationContext::create()->setGroups(array('chstomer:single')));

        $response = new Response($json, 200, [
            "Content-Type' => 'application/json"
        ]);

        return $response;
    }
}