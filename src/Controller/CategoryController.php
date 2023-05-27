<?php

namespace App\Controller;

use App\Entity\Category;
use App\Form\CategoryType;
use App\Repository\ProgramRepository;
use App\Repository\CategoryRepository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;


#[Route('/category', name:'category_')]
class CategoryController extends AbstractController
{
    #[Route('/', name: 'index')]
    public function index(CategoryRepository $categoryRepository, RequestStack $requestStack): Response
    {
        $session = $requestStack->getSession();
        $categories = $categoryRepository->findAll();
        return $this->render('category/index.html.twig', ['categories' => $categories]);
    }

    #[Route('/new', name: 'new')]
    public function new(Request $request, CategoryRepository $categoryRepository): Response
    {
        $category = new Category();

        // Create the form, linked with $category
        $form = $this->createForm(CategoryType::class, $category);

        $form->handleRequest($request);
        // Was the form submitted ?
        if ($form->isSubmitted()) {
            $categoryRepository->save($category, true);
            // Redirect to categories list
            $this->addFlash('success', 'Une nouvelle catégory à été créee');
            return $this->redirectToRoute('category_index');
        }
        
        // Render the form
        return $this->render('category/new.html.twig', [
            'form' => $form,
        ]);
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