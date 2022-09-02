<?php

namespace App\Controller;

use App\Entity\Image;
use App\Entity\User;
use App\Form\UserType;
use App\Repository\UserRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;

#[Route('/user')]
class UserController extends AbstractController
{
    #[Route('/', name: 'app_user')]
    public function index(): Response
    {
        return $this->render('user/user.html.twig', [
            'controller_name' => 'UserController',
        ]);
    }

    #[Route('/{id}/edit', name: 'app_user_edit')]
    public function editUser(Request $request, UserRepository $userRepository, User $user = null): Response
    {
        if (!$this->getUser()) {
            throw new AccessDeniedException();
        }

        $form = $this->createForm(UserType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {


            /** @var UploadedFile $imageUploaded */
            $imageUploaded = $form->get('imageUrl')->getData();

            if ($imageUploaded) {
                $originalFilename = pathinfo($imageUploaded->getClientOriginalName(), PATHINFO_FILENAME);
                $newFilename = $originalFilename . '-' . uniqid() . '.' . $imageUploaded->guessExtension();

                try {
                    $file = $this->getParameter('avatars_directory') . $user->getImageUrl();

                    if (is_file($file)) {
                        unlink($file);
                    }

                    $imageUploaded->move(
                        $this->getParameter('avatars_directory'),
                        $newFilename
                    );

                    $user->setImageUrl($newFilename);
                } catch (FileException $e) {
                    throw new \Exception($e);
                }
            }

            $now = new \DateTimeImmutable();
            $now->format('Y-m-d H:i:s');

            $user->setUpdatedAt($now);
            $userRepository->add($user, true);
        }

        return $this->renderForm('user/user.html.twig', [
            'id' => $user->getId(),
            'user' => $user,
            'form' => $form,
        ]);
    }
}
