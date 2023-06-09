<?php

namespace App\Controller;

use App\Entity\Comment;
use App\Form\CommentType;
use App\Repository\CommentRepository;
use App\Repository\EpisodeRepository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;


#[Route('/comment')]
class CommentController extends AbstractController
{
    #[Route('/', name: 'app_comment_index', methods: ['GET'])]
    #[IsGranted('ROLE_ADMIN')]
    public function index(CommentRepository $commentRepository): Response
    {
        return $this->render('comment/index.html.twig', [
            'comments' => $commentRepository->findAll(),
        ]);
    }

    #[Route('/new/{id}', name: 'app_comment_new', methods: ['GET', 'POST'])]
    public function new(int $id, EpisodeRepository $episodeRepository, Request $request, CommentRepository $commentRepository, TokenStorageInterface $tokenStorage): Response
    {
        $comment = new Comment();
        $form = $this->createForm(CommentType::class, $comment);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $user = $tokenStorage->getToken()->getUser();
            $episode = $episodeRepository->find($id);
            $comment->setAuthor($user);
            $comment->setEpisode($episode);
            $comment->setOwner($this->getUser());
            $commentRepository->save($comment, true);

            return $this->redirectToRoute('app_episode_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('comment/new.html.twig', [
            'comment' => $comment,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_comment_show', methods: ['GET'])]
    public function show(Comment $comment): Response
    {
        return $this->render('comment/show.html.twig', [
            'comment' => $comment,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_comment_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Comment $comment, CommentRepository $commentRepository): Response
    {
        // If not the owner and admin, throws a 403 Access Denied exception
        if (!$this->isGranted('ROLE_ADMIN') && $this->getUser() !== $comment->getOwner()) {
            
            throw $this->createAccessDeniedException('Seul le propriétaire peut modifier le commentaire');
        }

        $form = $this->createForm(CommentType::class, $comment);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $commentRepository->save($comment, true);

            return $this->redirectToRoute('app_comment_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('comment/edit.html.twig', [
            'comment' => $comment,
            'form' => $form,
        ]);
    }

    #[Route('/delete/{id}', name: 'app_comment_delete', methods: ['GET', 'POST'])]
    public function delete(Request $request, Comment $comment, CommentRepository $commentRepository): Response
    {
        // If not the owner and admin, throws a 403 Access Denied exception
        if (!$this->isGranted('ROLE_ADMIN') && $this->getUser() !== $comment->getOwner()) {
            
            throw $this->createAccessDeniedException('Seul le propriétaire peut supprimer le commentaire');
        }

        $commentRepository->remove($comment, true);

        return $this->redirectToRoute('app_episode_index', [], Response::HTTP_SEE_OTHER);
    }
}
