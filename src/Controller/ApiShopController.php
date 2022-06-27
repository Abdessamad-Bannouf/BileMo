<?php

namespace App\Controller;

use App\Entity\Shop;
use App\Repository\ShopRepository;
use JMS\Serializer\SerializationContext;
use JMS\Serializer\SerializerInterface;
use Nelmio\ApiDocBundle\Annotation\Model;
use OpenApi\Annotations as OA;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class ApiShopController extends AbstractController
{
    private $serializer;

    public function __construct(SerializerInterface $serializer)
    {
        $this->serializer = $serializer;
    }

    /**
     * @Route("/api/shop/{id}/user", name="api_index_user_shop", methods={"GET"})
     *  @OA\Response(
     *     response=200,
     *     description="Returns all users of a specific shop",
     *     @OA\JsonContent(
     *        type="array",
     *        @OA\Items(ref=@Model(type=Smartphone::class, groups={"smartphone:single"}))
     *     )
     * )
     */
    public function showUsersByShop(Shop $shop): Response
    {
        $users = $shop->getUsers();

        $json = $this->serializer->serialize($users, 'json', SerializationContext::create()->setGroups(array('shop:single')));

        $response = new Response($json, 200, [
            "Content-Type' => 'application/json"
        ]);

        return $response;
    }
}