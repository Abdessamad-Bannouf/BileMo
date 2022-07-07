<?php

namespace App\Controller;

use App\Entity\Smartphone;
use App\Repository\SmartphoneRepository;
use App\Service\PaginationService;
use JMS\Serializer\SerializationContext;
use JMS\Serializer\SerializerInterface;
use Nelmio\ApiDocBundle\Annotation\Model;
use OpenApi\Annotations as OA;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class ApiSmartphoneController extends AbstractController
{
    private $request;
    private $smartphoneRepository;
    private $serializer;

    public function __construct(RequestStack $requestStack, SmartphoneRepository $smartphoneRepository, SerializerInterface $serializer)
    {
        $this->request = $requestStack->getCurrentRequest();
        $this->smartphoneRepository = $smartphoneRepository;
        $this->serializer = $serializer;
    }

    /**
     * @Route("/api/smartphone", name="api_index_smartphone", methods={"GET"})
     * @OA\Response(
     *     response=200,
     *     description="Returns the list of smartphones",
     *     @OA\JsonContent(
     *        type="array",
     *        @OA\Items(ref=@Model(type=Smartphone::class, groups={"smartphone:list"}))
     *     )
     * )

     */
    public function showAll(PaginationService $paginationService): Response
    {
        $smartphones = $this->smartphoneRepository->findAll();

        $response = $paginationService->getPagination($smartphones, 5, 'smartphone:list');
        
        return $response;
    }

    /**
     * @Route("/api/smartphone/{id}", name="api_show_smartphone", methods={"GET"})
     *  @OA\Response(
     *     response=200,
     *     description="Returns a smartphone by id",
     *     @OA\JsonContent(
     *        type="array",
     *        @OA\Items(ref=@Model(type=Smartphone::class, groups={"smartphone:single"}))
     *     )
     * )
     */
    public function showProduct(int $id): Response
    {
        $smartphone = $this->smartphoneRepository->findBy(['id' => $id]);

        $json = $this->serializer->serialize($smartphone, 'json', SerializationContext::create()->setGroups(array('smartphone:single')));

        if(! $smartphone) {
            $response = new Response('Smartphone non trouvé', 404, [
                "Content-Type' => 'application/json"
            ]);

            return $response;
        }
        
        $response = new Response($json, 200, [
            "Content-Type' => 'application/json"
        ]);
        
        return $response;
    }
}