<?php

namespace App\Controller;

use App\Entity\Smartphone;
use App\Repository\SmartphoneRepository;
use JMS\Serializer\SerializationContext;
use JMS\Serializer\SerializerInterface;
use Nelmio\ApiDocBundle\Annotation\Model;
use OpenApi\Annotations as OA;
use Pagerfanta\Adapter\ArrayAdapter;
use Pagerfanta\Pagerfanta;
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
    public function showAll(): Response
    {
        $smartphones = $this->smartphoneRepository->findAll();

        $adapter = new ArrayAdapter($smartphones);
        $pagerfanta = new Pagerfanta($adapter);

        // Get the actual page in url (default: 1)
        $actualPage = $this->request->query->get('page') ? $this->request->query->get('page'): 1;

        $limit = 5;

        // Check if the actual page is superior to smartphones count
        if($limit * $actualPage > count($smartphones)) {
            $response = new Response('Paramètres de pages incorrect', 404, [
                "Content-Type' => 'application/json"
            ]);

            return $response;
        }

        $pagerfanta->setMaxPerPage($limit); // 5 items per page
        $pagerfanta->setCurrentPage($actualPage); // 1 by default

        $currentPageResults = $pagerfanta->getCurrentPageResults();

        $json = $this->serializer->serialize($currentPageResults, 'json', SerializationContext::create()->setGroups(array('smartphone:list')));

        $response = new Response($json, 200, [
            "Content-Type' => 'application/json"
        ]);
        
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