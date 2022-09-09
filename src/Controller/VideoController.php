<?php

namespace App\Controller;

use App\Entity\Video;
use App\Form\VideoType;
use App\Repository\TrickRepository;
use App\Repository\VideoRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class VideoController extends AbstractController
{
    #[Route('/video', name: 'app_video')]
    public function index(): Response
    {
        return $this->render('video/video.html.twig', [
            'controller_name' => 'VideoController',
        ]);
    }

    #[Route('video/{id}/delete', name: 'app_video_delete')]
    public function deleteVideo(VideoRepository $videoRepository , Video $video = null): Response
    {

        $trick = $video->getTrick();
        $slug = $trick->getSlug();
        $videoRepository->remove($video, true);

        return $this->redirectToRoute('app_trick_edit', ['slug' => $slug]);
    }

    #[Route('video/{id}/edit', name: 'app_video_edit')]
    public function editVideo(Request $request, TrickRepository $trickRepository, VideoRepository $videoRepository , Video $video = null): Response
    {

        $trick = $video->getTrick();

        $form = $this->createForm(VideoType::class, $video);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $slug = $trick->getSlug();

            $video->setUpdatedAt(new \DateTimeImmutable());

            $videoRepository->add($video, true);

            $trick->setUpdatedAt(new \DateTimeImmutable());

            $trickRepository->update($trick, true);

            return $this->redirectToRoute('app_trick_edit', ['slug' => $slug]);
        }

        return $this->renderForm('video/video.html.twig', [
            'video' => $video,
            'form' => $form
        ]);
    }
}
