<?php

namespace App\Controller;

use App\Entity\Season;
use App\Entity\Episode;
use App\Entity\Program;
use App\Form\ProgramType;
use App\Form\SearchProgramType;
use App\Service\ProgramDuration;
use Symfony\Component\Mime\Email;
use App\Repository\SeasonRepository;
use App\Repository\EpisodeRepository;
use App\Repository\ProgramRepository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\String\Slugger\SluggerInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Entity;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;


#[Route('/program', name: 'program_')]
class ProgramController extends AbstractController
{
    #[Route('/', name: 'index')]
    public function index(Request $request, ProgramRepository $programRepository, RequestStack $requestStack): Response
    {
        $session = $requestStack->getSession();
        $form = $this->createForm(SearchProgramType::class);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
        $search = $form->getData()['search'];
        $programs = $programRepository->findBy(['title' => $search]);
        } else {
        $programs = $programRepository->findAll();
        }

        return $this->render('program/index.html.twig', [
        'programs' => $programs,
        'form' => $form,
        ]);

    }

    #[Route('/new', name: 'new')]
    public function new(Request $request, MailerInterface $mailer, ProgramRepository $programRepository, SluggerInterface $slugger): Response
    {
        $program = new Program();

        // Create the form, linked with $category
        $form = $this->createForm(ProgramType::class, $program);

        $form->handleRequest($request);
        // Was the form submitted ?
        if ($form->isSubmitted() && $form->isValid()) {
            $slug = $slugger->slug($program->getTitle());
            $program->setSlug($slug);
            $program->setOwner($this->getUser());
            $programRepository->save($program, true);

            $email = (new Email())
                ->from($this->getParameter('mailer_from'))
                ->to('skoeniguer@free.fr')
                ->subject('Une nouvelle série vient d\'être publiée !')
                ->html($this->renderView('Program/newProgramEmail.html.twig', ['program' => $program]));

            $mailer->send($email);
            
            $this->addFlash('success', 'Un nouveau program à été crée');
            // Redirect to categories list
            return $this->redirectToRoute('program_index');
        }
        
        // Render the form
        return $this->render('program/new.html.twig', [
            'form' => $form,
        ]);
    }


    #[Route('/{slug}', methods: ['GET'], name: 'show')]
    public function show(Program $program, ProgramDuration $programDuration): Response
    {
        if (!$program) {
            throw $this->createNotFoundException(
                'No program with id : '.$id.' found in program\'s table.'
            );
        }
        return $this->render('program/show.html.twig', ['program' => $program, 'programDuration' => $programDuration->calculate($program)]);

    }

    #[Route('/{slug}/seasons/{season}', name: 'season_show')]
    public function showSeason(Program $program, Season $season, EpisodeRepository $episodeRepository): Response
    {
        
        $episode = $episodeRepository->findBy(['season' => $season]);

        return $this->render('program/season_show.html.twig', ['program' => $program, 'seasons' => $season, 'episodes' => $episode]);

    }

    #[Route('/{program}/seasons/{season}/episode/{episode}', name:'episode_show')]
    #[Entity('program', options: ['mapping' => ['program' => 'slug']])]
    #[Entity('episode', options: ['mapping' => ['episode' => 'slug']])]
    public function showEpisode(Program $program, Season $season, Episode $episode)
    {
        return $this->render('program/episode_show.html.twig', ['program' => $program, 'seasons' => $season, 'episode' => $episode]);

    }

    #[Route('/{slug}/edit', name: 'edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Program $program, ProgramRepository $programRepository): Response
    {
        // If not the owner and admin, throws a 403 Access Denied exception
        if (!$this->isGranted('ROLE_ADMIN') && $this->getUser() !== $program->getOwner()) {
            
            throw $this->createAccessDeniedException('Seul le propriétaire peut modifier le program');
        }

        $form = $this->createForm(ProgramType::class, $program);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $programRepository->save($program, true);

            $this->addFlash('success', 'Le programme veins d\'être modifié');
            return $this->redirectToRoute('program_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('program/new.html.twig', [
            'program' => $program,
            'form' => $form,
        ]);
    }

}

