<?php

namespace App\Controller;

use App\Entity\Comment;
use App\Entity\Image;
use App\Entity\Trick;
use App\Entity\Video;
use App\Form\CommentType;
use App\Form\TrickType;
use App\Repository\CommentRepository;
use App\Repository\ImageRepository;
use App\Repository\TrickRepository;
use App\Repository\VideoRepository;
use Cocur\Slugify\Slugify;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\HttpFoundation\File\UploadedFile;
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
        return $this->render('trick/index.html.twig', [
            'tricks' => $trickRepository->findAll()
        ]);
    }

    #[Route('/new', name: 'app_trick_new', methods: ['GET', 'POST'])]
    public function new(Request $request, TrickRepository $trickRepository, ImageRepository $imageRepository, VideoRepository $videoRepository): Response
    {
        if(!$this->getUser()){
            throw new AccessDeniedException();
        }

        $trick = new Trick();
        $form = $this->createForm(TrickType::class, $trick);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            /** @var UploadedFile $imageFile */
            $imageUploaded = $form->get('image')->getData();

            if ($imageUploaded) {
                foreach ($imageUploaded as $imageFile) {
                    $originalFilename = pathinfo($imageFile->getClientOriginalName(), PATHINFO_FILENAME);
                    $newFilename = $originalFilename . '-' . uniqid() . '.' . $imageFile->guessExtension();

                    try {
                        $imageFile->move(
                            $this->getParameter('images_directory'),
                            $newFilename
                        );

                        $image = new Image();
                        $image->setImageUrl($this->getParameter('images_directory') . $newFilename)->setTrick($trick);
                        $imageRepository->add($image, true);

                        $trick->addImage($image);
                    } catch (FileException $e) {
                        // ... handle exception if something happens during file upload
                    }
                }
            }

            $videoData = $form->get('video')->getData();

            foreach ($videoData as $video) {
                $now = new \DateTimeImmutable();
                $now->format('Y-m-d H:i:s');

                $video->setTrick($trick);
                $video->setCreatedAt($now);
                $video->setUpdatedAt($now);

                $videoRepository->add($video);
                $trick->addVideo($video);
            }

            $slugify = new Slugify();
            $slug = $slugify->slugify($trick->getName());

            $trick->setSlug($slug);

            $user = $this->getUser();
            $trick->setUser($user);

            $trickRepository->add($trick, true);

            return $this->redirectToRoute('app_trick_index', [], Response::HTTP_SEE_OTHER);
        }
        return $this->renderForm('trick/new.html.twig', [
            'trick' => $trick,
            'form' => $form,
        ]);
    }

    #[Route('/{slug}', name: 'app_trick_show', methods: ['GET', 'POST'])]
    public function show(Request $request, CommentRepository $commentRepository, Trick $trick = null): Response
    {
        if (!$trick) {
            throw $this->createNotFoundException('No tricks found');
        }

        $comment = new Comment();
        $form = $this->createForm(CommentType::class, $comment);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $user = $this->getUser();

            $comment->setUser($user);
            $comment->setCreatedAt(new \DateTimeImmutable());
            $comment->setTrick($trick);

            $commentRepository->add($comment, true);

            $this->addFlash('success', 'Comment added!');
        }

        return $this->render('trick/show.html.twig', [
            'trick' => $trick,
            'formComment' => $form->createView()
        ]);
    }

    /**
     * @param Request $request
     * @param Trick $trick
     * @param TrickRepository $trickRepository
     * @param ImageRepository $imageRepository
     * @param VideoRepository $videoRepository
     * @return Response
     * @throws \Exception
     */
    #[Route('/{slug}/edit', name: 'app_trick_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Trick $trick, TrickRepository $trickRepository, ImageRepository $imageRepository, VideoRepository $videoRepository): Response
    {
        if(!$this->getUser()){
                throw new AccessDeniedException();
        }
        $form = $this->createForm(TrickType::class, $trick);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            /** @var UploadedFile $imageFile */
            $imageUploaded = $form->get('image')->getData();

            if ($imageUploaded) {
                foreach ($imageUploaded as $imageFile) {
                    $originalFilename = pathinfo($imageFile->getClientOriginalName(), PATHINFO_FILENAME);
                    $newFilename = $originalFilename . '-' . uniqid() . '.' . $imageFile->guessExtension();

                    try {
                        $imageFile->move(
                            $this->getParameter('images_directory'),
                            $newFilename
                        );

                        $image = new Image();
                        $image->setImageUrl($newFilename)->setTrick($trick);
                        $imageRepository->add($image, true);

                        $trick->addImage($image);
                    } catch (FileException $e) {
                        throw new \Exception($e);
                    }
                }
            }

            $videoData = $form->get('video')->getData();

            foreach ($videoData as $video) {
                $now = new \DateTimeImmutable();
                $now->format('Y-m-d H:i:s');

                $video->setTrick($trick);
                $video->setUpdatedAt($now);

                $videoRepository->add($video);
                $trick->addVideo($video);
            }

            $slugify = new Slugify();
            $slug = $slugify->slugify($trick->getName());

            $trick->setSlug($slug);

            $user = $this->getUser();
            $trick->setUser($user);

            $trickRepository->update($trick, true);

            return $this->redirectToRoute('app_trick_index', [], Response::HTTP_SEE_OTHER);
        }
        return $this->renderForm('trick/edit.html.twig', [
            'trick' => $trick,
            'form' => $form,
        ]);
    }

    #[Route('/{slug}', name: 'app_trick_delete', methods: ['POST'])]
    public function delete(Request $request, Trick $trick, TrickRepository $trickRepository): Response
    {
        if ($this->isCsrfTokenValid('delete' . $trick->getId(), $request->request->get('_token'))) {
            $trickRepository->remove($trick, true);
        }

        return $this->redirectToRoute('app_trick_index', [], Response::HTTP_SEE_OTHER);
    }

    #[Route('video/{id}/delete', name: 'app_video_delete')]
    public function deleteVideo(VideoRepository $videoRepository , Video $video = null): Response
    {

        $trick = $video->getTrick();
        $slug = $trick->getSlug();
        $videoRepository->remove($video, true);

        return $this->redirectToRoute('app_trick_edit', ['slug' => $slug]);
    }
}
