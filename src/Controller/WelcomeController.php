<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class WelcomeController extends AbstractController
{
    /**
     * @Route("/", name="welcome")
     */
    public function index(): Response
    {
        return $this->redirectToRoute('admin');
    }


    /**
     * @Route("/result/{id_c}/{id_d}", name="result_user")
     */
    public function infoPage(): Response
    {
        return $this->render('welcome/index.html.twig', [
            'controller_name' => 'WelcomeController',
        ]);
    }
}
