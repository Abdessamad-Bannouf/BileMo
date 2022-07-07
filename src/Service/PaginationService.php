<?php

namespace App\Service;

use JMS\Serializer\SerializationContext;
use JMS\Serializer\SerializerInterface;
use Pagerfanta\Adapter\ArrayAdapter;
use Pagerfanta\Pagerfanta;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpFoundation\Response;

class PaginationService
{
    private $request;
    private $serializer;

    public function __construct(RequestStack $request, SerializerInterface $serializerInterface)
    {
        $this->request = $request->getCurrentRequest();
        $this->serializer = $serializerInterface;
    }

    public function getPagination($repositoryEntity, $limit, $group): Response
    {
        $adapter = new ArrayAdapter($repositoryEntity);
        $pagerfanta = new Pagerfanta($adapter);

        $actualPage = $this->request->query->get('page') ? $this->request->query->get('page'): 1;

        // Check if the actual page is superior to entity count
        if($limit * $actualPage > count($repositoryEntity)) {
            $response = new Response('Paramètres de pages incorrect', 404, [
                "Content-Type' => 'application/json"
            ]);

            return $response;
        }

        $pagerfanta->setMaxPerPage($limit); // 5 items per page
        $pagerfanta->setCurrentPage($actualPage); // 1 by default

        $currentPageResults = $pagerfanta->getCurrentPageResults();

        $json = $this->serializer->serialize($currentPageResults, 'json', SerializationContext::create()->setGroups(array($group)));

        $response = new Response($json, 200, [
            "Content-Type' => 'application/json"
        ]);

        return $response;
    }
}