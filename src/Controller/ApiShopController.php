<?php

namespace App\Controller;

use App\Entity\Shop;
use App\Repository\ShopRepository;
use JMS\Serializer\SerializationContext;
use JMS\Serializer\SerializerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class ApiShopController extends AbstractController
{
    /**
     * @Route("/api/shop/{id}/user", name="api_index_user_shop", methods={"GET"})
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