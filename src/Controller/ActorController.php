<?php

namespace App\Controller;


use App\Entity\Actor;
use App\Form\ActorType;
use App\Form\ProgramType;
use App\Repository\ActorRepository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

#[Route('/actor', name: 'actor_')]
class ActorController extends AbstractController
{
    #[Route('/', name: 'index')]
    public function index(ActorRepository $actorRepository): Response
    {   
        $actor = $actorRepository->findAll();

        return $this->render('actor/index.html.twig', ['actors' => $actor]);
    }

    #[Route('/new', name: 'new')]
    public function new(Request $request, ActorRepository $actorRepository): Response
    {
        $actor = new Actor();

        // Create the form, linked with $category
        $form = $this->createForm(ActorType::class, $actor);

        $form->handleRequest($request);
        // Was the form submitted ?
        if ($form->isSubmitted() && $form->isValid()) {
            $actorRepository->save($actor, true);
 
            $this->addFlash('success', 'Un nouvel acteur a été ajoutée');
            // Redirect to categories list
            return $this->redirectToRoute('actor_index');
        }
        
        // Render the form
        return $this->render('actor/new.html.twig', [
            'form' => $form,
        ]);
    }

    #[Route('/{name}', name: 'show')]
    public function show(Actor $actor): Response
    {   
        return $this->render('actor/show.html.twig', ['actor' => $actor]);
    }

}
