<?php

namespace App\Controller;

use App\Repository\DishRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class HomeController extends AbstractController
{
    /**
     * @Route("/", name="home")
     */
    public function index(DishRepository $ds)
    {

        $dishes = $ds->findAll();

        $random = array_rand($dishes, 2);

        return $this->render('home/index.html.twig', [
            "dishes1" => $dishes[$random[0]],
            "dishes2" => $dishes[$random[1]],
        ]);
    }

}
