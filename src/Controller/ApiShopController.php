<?php

namespace App\Controller;

use App\Entity\Shop;
use App\Repository\ShopRepository;
use JMS\Serializer\SerializationContext;
use JMS\Serializer\SerializerInterface;
use Nelmio\ApiDocBundle\Annotation\Model;
use Nelmio\ApiDocBundle\Annotation\Security;
use OpenApi\Annotations as OA;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class ApiShopController extends AbstractController
{
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
    public function showUsersByShop(Shop $shop, ShopRepository $shopRepository, SerializerInterface $serializer): Response
    {
        $users = $shop->getUsers();

        $json = $serializer->serialize($users, 'json', SerializationContext::create()->setGroups(array('shop:single')));

        $response = new Response($json, 200, [
            "Content-Type' => 'application/json"
        ]);

        return $response;
    }
}