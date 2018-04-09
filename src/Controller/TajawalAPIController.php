<?php

namespace App\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class TajawalAPIController extends Controller
{
    /**
     * @Route("/", name="homepage")
     */
    public function index(Request $request)
    {
        $url = $request->getUriForPath('/api.json');
        return $this->render('tajawal_api/index.html.twig', [
            'controller_name' => 'TajawalAPIController',
        ]);
    }
}
