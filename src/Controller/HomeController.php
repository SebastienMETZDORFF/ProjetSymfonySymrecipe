<?php

namespace App\Controller;

use App\Repository\RecipeRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class HomeController extends AbstractController {

    /**
     * This function displays the home page
     *
     * @param RecipeRepository $repository
     * @return Response
     */
    #[Route('/', 'home.index', methods: ['GET'])]
    public function index(RecipeRepository $repository): Response {
        return $this->render('pages/home.html.twig', [
            'recipes' => $repository->findPublicRecipe(3)
        ]
    );
    }
}