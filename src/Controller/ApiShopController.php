<?php

namespace App\Controller;

use App\Entity\Shop;
use App\Repository\ShopRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\SerializerInterface;

class ApiShopController extends AbstractController
{
    /**
     * @Route("/api/shop/{id}/user", name="api_index_user_shop", methods={"GET"})
     */
    public function showUsersByShop(Shop $shop, ShopRepository $shopRepository, SerializerInterface $serializer): Response
    {

        // todo : Récupérer les utilisateurs par shop
            // Solution 1 : Passer par une méthode custom du Query Builder et faire une jointure => En cours

        //$shop = $shopRepository->getUsersByShop($shop);
        $shop = $shop->getUsers();

        //$normalizers = [new ObjectNormalizer()];
        //$serializer = new Serializer($normalizers, []);

        //$usersNormalises = $normalizer->normalize($shop, null, ['groups' => 'shop:list']);

        $json = $serializer->serialize($shop, 'json', ['groups' => 'shop:list']);

        $response = new Response($json, 200, [
            "Content-Type' => 'application/json"
        ]);

        return $response;
    }
}