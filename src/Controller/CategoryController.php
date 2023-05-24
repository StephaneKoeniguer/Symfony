<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Repository\CategoryRepository;
use App\Repository\ProgramRepository;


#[Route('/category', name:'category_')]
class CategoryController extends AbstractController
{
    #[Route('/', name: 'index')]
    public function index(CategoryRepository $categoryRepository): Response
    {
        $categories = $categoryRepository->findAll();
        return $this->render('category/index.html.twig', ['categories' => $categories]);
    }

    #[Route('/{categoryName}', methods: ['GET'], name: 'show')]
    public function show(string $categoryName, CategoryRepository $categoryRepository, ProgramRepository $programRepository): Response
    {
        $categorie = $categoryRepository->findOneBy(['name' => $categoryName]);

        if (!$categorie) {
            throw $this->createNotFoundException('Aucune série trouvée' . ' ' . $categoryName);
        }

        $program = $programRepository->findBy(
            ['category' => $categorie],
            ['id' => 'DESC']
        );

        return $this->render('category/show.html.twig', ['programs' => $program, 'category' => $categorie]);

    } 
}