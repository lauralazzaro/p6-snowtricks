<?php

namespace App\Services;

use App\Entity\Image;
use App\Entity\Trick;
use App\Repository\ImageRepository;
use Symfony\Component\HttpFoundation\File\Exception\FileException;

class ImageHelper
{
    private ImageRepository $imageRepository;

    public function __construct(ImageRepository $imageRepository)
    {
        $this->imageRepository = $imageRepository;
    }

    public function uploadImage(
        ?array $imageUploaded,
        Trick $trick,
        $directory
    ) {
        if ($imageUploaded) {
            foreach ($imageUploaded as $imageFile) {
                $originalFilename = pathinfo($imageFile->getClientOriginalName(), PATHINFO_FILENAME);
                $newFilename = $originalFilename . '-' . uniqid() . '.' . $imageFile->guessExtension();

                $imageFile->move(
                    $directory,
                    $newFilename
                );

                $image = new Image();
                $image->setImageUrl($newFilename)->setTrick($trick);
                $this->imageRepository->add($image, true);
            }
        }
    }
}
