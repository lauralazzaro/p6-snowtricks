<?php

namespace App\Controller;

use App\Entity\Comment;
use App\Entity\Trick;
use App\Form\CommentType;
use App\Form\TrickType;
use App\Repository\CommentRepository;
use App\Repository\TrickRepository;
use App\Services\ImageHelper;
use App\Services\VideoHelper;
use Cocur\Slugify\Slugify;
use Doctrine\ORM\EntityManagerInterface;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;

#[Route('/trick')]
class TrickController extends AbstractController
{
    #[Route('/', name: 'app_trick_index', methods: ['GET'])]
    public function index(TrickRepository $trickRepository): Response
    {
        return $this->render(
            'trick/index.html.twig',
            [
                'tricks' => $trickRepository->findAll()
            ]
        );
    }

    #[Route('/new', name: 'app_trick_new', methods: ['GET', 'POST'])]
    public function new(
        Request $request,
        TrickRepository $trickRepository,
        VideoHelper $videoHelper,
        ImageHelper $imgHelper
    ): Response {
        if (!$this->getUser()) {
            throw new AccessDeniedException();
        }

        $trick = new Trick();
        $form = $this->createForm(TrickType::class, $trick);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $slugify = new Slugify();
            $slug = $slugify->slugify($trick->getName());
            $trick->setSlug($slug);

            $user = $this->getUser();
            $trick->setUser($user);

            $imgHelper->uploadImage(
                $form->get('image')->getData(),
                $trick,
                $this->getParameter('images_directory')
            );

            $videoHelper->saveVideoUrl(
                $form->get('video')->getData(),
                $trick
            );

            $trickRepository->add($trick, true);

            $this->addFlash('success', 'Trick "' . $trick->getName() . '" created!');

            return $this->redirectToRoute('app_trick_index', [], Response::HTTP_SEE_OTHER);
        }
        return $this->renderForm(
            'trick/new.html.twig',
            [
                'trick' => $trick,
                'form' => $form,
            ]
        );
    }

    #[Route('/{slug}', name: 'app_trick_show', methods: ['GET', 'POST'])]
    public function show(
        Request $request,
        CommentRepository $commentRepository,
        PaginatorInterface $paginator,
        EntityManagerInterface $em,
        Trick $trick = null
    ): Response {
        if (!$trick) {
            throw $this->createNotFoundException('No tricks found');
        }

        $comment = new Comment();
        $form = $this->createForm(CommentType::class, $comment);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $user = $this->getUser();

            $comment->setUser($user);

            $comment->setTrick($trick);

            $commentRepository->add($comment, true);

            $this->addFlash('success', 'Comment added!');
        }

        $dql = "SELECT a FROM App\Entity\Comment a WHERE a.trick = " . $trick->getId();
        $query = $em->createQuery($dql);

        $pagination = $paginator->paginate(
            $query, /* query NOT result */
            $request->query->getInt('page', 1), /*page number*/
            5 /*limit per page*/
        );

        $pagination->setCustomParameters(
            [
                'align' => 'center',
                'style' => 'bottom'
            ]
        );

        // parameters to template
        return $this->render(
            'trick/show.html.twig',
            [
                'trick' => $trick,
                'pagination' => $pagination,
                'formComment' => $form->createView()
            ]
        );
    }

    #[Route('/{slug}/edit', name: 'app_trick_edit', methods: ['GET', 'POST'])]
    public function edit(
        Request $request,
        Trick $trick,
        TrickRepository $trickRepo,
        ImageHelper $imgHelper,
        VideoHelper $videoHelper
    ): Response {
        if ($trick->getUser() !== $this->getUser()) {
            $this->addFlash('warning', 'Your can edit only the tricks that you created');
            return $this->redirectToRoute('app_trick_index', [], Response::HTTP_SEE_OTHER);
        }

        $form = $this->createForm(TrickType::class, $trick);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $imgHelper->uploadImage(
                $form->get('image')->getData(),
                $trick,
                $this->getParameter('images_directory')
            );

            $videoHelper->saveVideoUrl(
                $form->get('video')->getData(),
                $trick
            );

            $slugify = new Slugify();
            $slug = $slugify->slugify($trick->getName());

            $trick->setSlug($slug);

            $user = $this->getUser();
            $trick->setUser($user);

            $trick->setUpdatedAt(new \DateTimeImmutable());

            $trickRepo->update($trick, true);

            $this->addFlash('success', 'Trick "' . $trick->getName() . '" modified!');

            return $this->redirectToRoute('app_trick_index', [], Response::HTTP_SEE_OTHER);
        }
        return $this->renderForm(
            'trick/edit.html.twig',
            [
                'trick' => $trick,
                'form' => $form,
            ]
        );
    }

    #[Route('/{slug}/delete/{token}', name: 'app_trick_delete', methods: ['POST'])]
    public function delete(Trick $trick, TrickRepository $trickRepository): Response
    {
        $trickRepository->remove($trick, true);
        $this->addFlash('danger', 'Trick "' . $trick->getName() . '" removed!');

        return $this->redirectToRoute('app_trick_index', [], Response::HTTP_SEE_OTHER);
    }
}
