<?php

namespace App\Controller;

use App\Entity\Image;
use App\Entity\Trick;
use App\Form\ImageType;
use App\Repository\ImageRepository;
use App\Repository\TrickRepository;
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
        return $this->render('image/image.html.twig', [
            'controller_name' => 'ImageController',
        ]);
    }


    #[Route('/image/{id}/delete', name: 'app_image_delete')]
    public function deleteImage(ImageRepository $imageRepository, Image $image = null): Response
    {
        $file = $this->getParameter('images_directory') . $image->getImageUrl();

        if(file_exists($file)){
            unlink($file);
        }

        $trick = $image->getTrick();
        $slug = $trick->getSlug();
        $imageRepository->remove($image, true);

        return $this->redirectToRoute('app_trick_edit', ['slug' => $slug]);
    }

    #[Route('/image/{id}/edit', name: 'app_image_edit', methods: ['GET', 'POST'])]
    public function editImage(Request $request, ImageRepository $imageRepository, TrickRepository $trickRepository, Image $image = null, Trick $trick = null): Response
    {
        $trick = $image->getTrick();

        $form = $this->createForm(ImageType::class, $image);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            /** @var UploadedFile $imageFile */
            $imageFile = $form->get('image')->getData();


            if ($imageFile) {
                    $originalFilename = pathinfo($imageFile->getClientOriginalName(), PATHINFO_FILENAME);
                    $newFilename = $originalFilename . '-' . uniqid() . '.' . $imageFile->guessExtension();

                    try {
                        $file = $this->getParameter('images_directory') . $image->getImageUrl();

                        if(file_exists($file)){
                            unlink($file);
                        }

                        $imageFile->move(
                            $this->getParameter('images_directory'),
                            $newFilename
                        );

                        $image->setUpdatedAt(new \DateTimeImmutable());
                        $image->setImageUrl($newFilename)->setTrick($trick);
                        $imageRepository->add($image, true);

                        $trick->addImage($image);
                    } catch (FileException $e) {
                        throw new \Exception($e);
                    }
            }


            $slug = $trick->getSlug();

            $trickRepository->update($trick, true);

            return $this->redirectToRoute('app_trick_edit', ['slug' => $slug]);
        }

        return $this->renderForm('image/image.html.twig', [
            'image' => $image,
            'form' => $form
        ]);


    }
}
