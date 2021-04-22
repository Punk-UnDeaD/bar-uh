<?php

namespace App\Main\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Attribute\AsController;
use Symfony\Component\Routing\Annotation\Route;

#[AsController]
class FrontController extends AbstractController
{
    #[Route('/', name: 'front')]
    public function index(): Response
    {
        return $this->render('@Main/index.html.twig');
    }
}
