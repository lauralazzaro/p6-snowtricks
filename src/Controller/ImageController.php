<?php

namespace App\Controller;

use App\Entity\Image;
use App\Entity\Trick;
use App\Form\ImageType;
use App\Repository\ImageRepository;
use App\Repository\TrickRepository;
use App\Services\ImageHelper;
use Cocur\Slugify\Slugify;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class ImageController extends AbstractController
{
    #[Route('/image', name: 'app_image')]
    public function index(): Response
    {
        return $this->render(
            'image/image.html.twig',
            [
                'controller_name' => 'ImageController',
            ]
        );
    }


    #[Route('/image/{id}/delete', name: 'app_image_delete')]
    public function deleteImage(ImageRepository $imageRepository, Image $image = null): Response
    {
        $file = $this->getParameter('images_directory') . $image->getImageUrl();

        if (file_exists($file)) {
            unlink($file);
        }

        $trick = $image->getTrick();
        $slug = $trick->getSlug();
        $imageRepository->remove($image, true);

        return $this->redirectToRoute('app_trick_edit', ['slug' => $slug]);
    }

    #[Route('/image/{id}/edit', name: 'app_image_edit', methods: ['GET', 'POST'])]
    public function editImage(
        Request $req,
        ImageHelper $imgHelper,
        TrickRepository $trickRepo,
        Image $img = null
    ): Response {
        $trick = $img->getTrick();

        $form = $this->createForm(ImageType::class, $img);
        $form->handleRequest($req);

        if ($form->isSubmitted() && $form->isValid()) {
            $imgHelper->uploadImage(
                $form->get('image')->getData(),
                $trick,
                $this->getParameter('images_directory')
            );

            $slug = $trick->getSlug();

            $trickRepo->update($trick, true);

            return $this->redirectToRoute('app_trick_edit', ['slug' => $slug]);
        }

        return $this->renderForm(
            'image/image.html.twig',
            [
                'image' => $img,
                'form' => $form
            ]
        );
    }
}
