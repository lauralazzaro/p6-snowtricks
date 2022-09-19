<?php

namespace App\Controller;

use App\Entity\Comment;
use App\Entity\Image;
use App\Entity\Trick;
use App\Entity\Video;
use App\Form\CommentType;
use App\Form\TrickType;
use App\Form\VideoType;
use App\Repository\CommentRepository;
use App\Repository\ImageRepository;
use App\Repository\TrickRepository;
use App\Repository\VideoRepository;
use Cocur\Slugify\Slugify;
use Doctrine\ORM\EntityManagerInterface;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

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

    /**
     * @throws \Exception
     */
    #[Route('/new', name: 'app_trick_new', methods: ['GET', 'POST'])]
    public function new(
        Request $request,
        TrickRepository $trickRepository,
        ImageRepository $imageRepository,
        VideoRepository $videoRepository
    ): Response {
        if (!$this->getUser()) {
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
                        throw new \Exception($e);
                    }
                }
            }

            $videoData = $form->get('video')->getData();

            foreach ($videoData as $video) {
                $video->setTrick($trick);
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
    public function show(
        Request                $request,
        CommentRepository      $commentRepository,
        PaginatorInterface     $paginator,
        EntityManagerInterface $em,
        Trick                  $trick = null
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
            10 /*limit per page*/
        );

        $pagination->setCustomParameters([
            'align' => 'center',
            'style' => 'bottom'
        ]);

        // parameters to template
        return $this->render('trick/show.html.twig', [
            'trick' => $trick,
            'pagination' => $pagination,
            'formComment' => $form->createView()
        ]);
    }

    /**
     * @param Request $request
     * @param Trick $trick
     * @param TrickRepository $trickRepo
     * @param ImageRepository $imageRepo
     * @param VideoRepository $videoRepo
     * @return Response
     * @throws \Exception
     */
    #[Route('/{slug}/edit', name: 'app_trick_edit', methods: ['GET', 'POST'])]
    public function edit(
        Request         $request,
        Trick           $trick,
        TrickRepository $trickRepo,
        ImageRepository $imageRepo,
        VideoRepository $videoRepo
    ): Response {

        if (!$this->getUser()) {
            throw new AccessDeniedException();
        }
        $form = $this->createForm(TrickType::class, $trick);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            /** @var UploadedFile $imageFile */
            $imageUploaded = $form->get('image')->getData();

            if ($imageUploaded) {
                foreach ($trick->getImage() as $image) {
                    $file = $this->getParameter('images_directory') . $image->getImageUrl();

                    if (file_exists($file)) {
                        unlink($file);
                    }
                }

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
                        $imageRepo->add($image, true);

                        $trick->addImage($image);
                    } catch (FileException $e) {
                        throw new \Exception($e);
                    }
                }
            }

            $videoData = $form->get('video')->getData();

            foreach ($videoData as $video) {
                $videoRepo->add($video);
                $trick->addVideo($video);
            }

            $slugify = new Slugify();
            $slug = $slugify->slugify($trick->getName());

            $trick->setSlug($slug);

            $user = $this->getUser();
            $trick->setUser($user);

            $trick->setUpdatedAt(new \DateTimeImmutable());

            $trickRepo->update($trick, true);

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
}
