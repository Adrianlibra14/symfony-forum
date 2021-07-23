<?php

namespace App\Controller;

use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class HomeController extends AbstractController
{

    /**
     * @Route("/", name="home_index")
     */
    public function index()
    {
        // Render home view
        return $this->render('home/index.html.twig');
    }
}