<?php

namespace App\Controller;

use App\Repository\ProgramRepository;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

#[Route('/', name:'app_')]
class DefaultController extends AbstractController
{
    #[Route('/', name:'index')]
    public function index(ProgramRepository $programRepository): Response
    {
        $programs = $programRepository->findAll();
        return $this->render('index.html.twig',['programs' => $programs]);
    }
}