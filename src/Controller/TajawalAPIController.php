<?php

namespace App\Controller;

use App\Model\APIClient;
use App\Model\SearchGateway;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class TajawalAPIController extends Controller
{
    /**
     * @Route("/", name="homepage")
     * @return Response
     * @throws \InvalidArgumentException
     */
    public function index(): Response
    {
        return new Response('Index');
    }

    /**
     *
     * @Route("/search", name="search")
     * @param Request $request
     *
     * @return JsonResponse
     */
    public function search(Request $request): JsonResponse
    {
        $filters = $request->get('f', []);
        $sorting = $request->get('s', []);

        $searchGW = new SearchGateway(APIClient::class);
        $hotels = $searchGW->findHotelsBy($filters, $sorting);

        return new JsonResponse($hotels);
    }
}
