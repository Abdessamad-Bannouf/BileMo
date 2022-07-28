<?php

namespace App\Controller;

use App\Entity\User;
use App\Repository\UserRepository;
use App\Service\PaginationService;
use Doctrine\Persistence\ManagerRegistry;
use JMS\Serializer\SerializationContext;
use JMS\Serializer\SerializerInterface;
use JsonException;
use Nelmio\ApiDocBundle\Annotation\Model;
use OpenApi\Annotations as OA;
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
    private $managerRegistry;

    public function __construct(RequestStack $requestStack, UserRepository $userRepository, SerializerInterface $serializer, ManagerRegistry $managerRegistry)
    {
        $this->request = $requestStack->getCurrentRequest();
        $this->userRepository = $userRepository;
        $this->serializer = $serializer;
        $this->managerRegistry = $managerRegistry;
    }

    /**
     * @Route("/api/user", name="api_index_user", methods={"GET"})
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
     * @Route("/api/user/{id}", name="api_show_user", methods={"GET"})
     *  @OA\Response(
     *     response=200,
     *     description="Return an user by id",
     *     @OA\JsonContent(
     *        type="array",
     *        @OA\Items(ref=@Model(type=User::class, groups={"user:single"}))
     *     )
     * )
     */
    public function showClient(User $user = null): Response
    {
        /*$this->checkUser($user);

        // if user can't be seen by the current user
        if (!$this->isGranted("SEE", $user)) {
            throw new JsonException("Vous n'avez pas les droits requis pour faire cette requête", JsonResponse::HTTP_UNAUTHORIZED);
        }*/

        $user = $this->userRepository->findBy(['id' => $user]);
        
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

    protected function checkUser($user)
    {
        // Si l'utilisateur n'est pas trouvé
        if (!$user || !($user instanceof User)) {
            throw new JsonException("Identifiant incorrect ou bien du client n'est pas trouvé", JsonResponse::HTTP_NOT_FOUND);
        }
    }
}
