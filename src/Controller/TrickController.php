<?php

namespace App\Controller;

use App\Entity\Image;
use App\Entity\Trick;
use App\Entity\Video;
use App\Form\TrickType;
use App\Repository\ImageRepository;
use App\Repository\TrickRepository;
use App\Repository\VideoRepository;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/trick')]
class TrickController extends AbstractController
{

    private $em;

    public function __construct(EntityManagerInterface $em) {
        $this->em = $em;

        // In a Command, you *must* call the parent constructor
    }

    #[Route('/', name: 'app_trick_index', methods: ['GET'])]
    public function index(TrickRepository $trickRepository): Response
    {
        return $this->render('trick/index.html.twig', [
            'tricks' => $trickRepository->findAll(),
        ]);
    }

    #[Route('/new', name: 'app_trick_new', methods: ['GET', 'POST'])]
    public function new(Request $request, TrickRepository $trickRepository, ImageRepository $imageRepository, VideoRepository $videoRepository): Response
    {
        $trick = new Trick();
        $form = $this->createForm(TrickType::class, $trick);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            /** @var UploadedFile $imageFile */
            $imageUploaded = $form->get('image')->getData();

            if ($imageUploaded) {
                foreach($imageUploaded as $imageFile) {
                    $originalFilename = pathinfo($imageFile->getClientOriginalName(), PATHINFO_FILENAME);
                    $newFilename = $originalFilename . '-' . uniqid() . '.' . $imageFile->guessExtension();

                    // Move the file to the directory where brochures are stored
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

            foreach ($videoData as $video){
                $now = new \DateTimeImmutable();
                $now->format('Y-m-d H:i:s');

                $video->setTrick($trick);
                $video->setCreatedAt($now);
                $video->setUpdatedAt($now);

                $videoRepository->add($video);
                $trick->addVideo($video);
            }

            $trickRepository->add($trick, true);

            return $this->redirectToRoute('app_trick_index', [], Response::HTTP_SEE_OTHER);
        }
        return $this->renderForm('trick/new.html.twig', [
            'trick' => $trick,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_trick_show', methods: ['GET'])]
    public function show(Trick $trick): Response
    {
        return $this->render('trick/show.html.twig', [
            'trick' => $trick,
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
    #[Route('/{id}/edit', name: 'app_trick_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Trick $trick, TrickRepository $trickRepository, ImageRepository $imageRepository, VideoRepository $videoRepository): Response
    {
            $form = $this->createForm(TrickType::class, $trick);
            $form->handleRequest($request);

            if ($form->isSubmitted() && $form->isValid()) {
                /** @var UploadedFile $imageFile */
                $imageUploaded = $form->get('image')->getData();

                if ($imageUploaded) {
                    foreach ($imageUploaded as $imageFile) {
                        $originalFilename = pathinfo($imageFile->getClientOriginalName(), PATHINFO_FILENAME);
                        $newFilename = $originalFilename . '-' . uniqid() . '.' . $imageFile->guessExtension();

                        // Move the file to the directory where brochures are stored
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
                $trickRepository->update($trick, true);

                return $this->redirectToRoute('app_trick_index', [], Response::HTTP_SEE_OTHER);
            }
        return $this->renderForm('trick/edit.html.twig', [
            'trick' => $trick,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_trick_delete', methods: ['POST'])]
    public function delete(Request $request, Trick $trick, TrickRepository $trickRepository): Response
    {
        if ($this->isCsrfTokenValid('delete' . $trick->getId(), $request->request->get('_token'))) {
            $trickRepository->remove($trick, true);
        }

        return $this->redirectToRoute('app_trick_index', [], Response::HTTP_SEE_OTHER);
    }
}
