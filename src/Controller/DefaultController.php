<?php

namespace App\Controller;

use App\Form\SearchProgramType;
use App\Repository\ProgramRepository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

#[Route('/', name:'app_')]
class DefaultController extends AbstractController
{
    #[Route('/', name:'index')]
    public function index(ProgramRepository $programRepository, RequestStack $requestStack, Request $request): Response
    {
        $session = $requestStack->getSession();
        $form = $this->createForm(SearchProgramType::class);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
        $searchProgram = $form->getData()['program'];
        $searchActor = $form->getData()['actor'];
        $programs = $programRepository->findLikeName($searchProgram, $searchActor);
        } else {
        $programs = $programRepository->findAll();
        }

        return $this->render('index.html.twig', [
        'programs' => $programs,
        'form' => $form,
        ]);
    }
}