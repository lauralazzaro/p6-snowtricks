<?php

namespace App\DataFixtures;

use App\Entity\Category;
use App\Entity\Comment;
use App\Entity\Image;
use App\Entity\Trick;
use App\Entity\User;
use App\Entity\Video;
use Cocur\Slugify\Slugify;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Faker\Factory;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class AppFixtures extends Fixture
{
    private UserPasswordHasherInterface $hasher;

    public function __construct(UserPasswordHasherInterface $hasher)
    {
        $this->hasher = $hasher;
    }

    public function load(ObjectManager $manager): void
    {
        $faker = Factory::create();

        // Users
        for ($i = 0; $i < 4; $i++) {
            $user = new User();
            $user->setName($faker->firstName());
            $user->setSurname($faker->lastName());
            $password = $this->hasher->hashPassword($user, 'pass1234');
            $user->setPassword($password);
            $user->setEmail('email' . $i . '@email.com');
            $user->setImageUrl('user' . $i . '.png');
            $this->setReference('user' . $i, $user);
            $manager->persist($user);
        }

        // Categories
        $arrayCategory = ['Grabs', 'Jumps & Spins', 'Slides'];
        for ($i = 0; $i < 3; $i++) {
            $category = new Category();
            $category->setName($arrayCategory[$i]);
            $this->setReference('category' . $i, $category);
            $manager->persist($category);
        }

        // Trick 1
        $userForTrick = $this->getReference('user' . random_int(0, 3));
        $categoryTrick = $this->getReference('category0');

        $trick = new Trick();
        $trick->setName('Stailfish');
        $trick->setUser($userForTrick);
        $trick->setCategory($categoryTrick);
        $trick->setDescription('Saisie de la carre backside de la planche entre les deux pieds avec la main arrière');

        $video = new Video();
        $video->setVideoUrl('https://www.youtube.com/embed/xXCCGYqAWqI');
        $video->setTrick($trick);
        $manager->persist($video);

        for ($i = 0; $i < 5; $i++) {
            $image = new Image();
            $image->setImageUrl('stailfish-' . $i . '.jpg');
            $image->setTrick($trick);
            $manager->persist($image);
        }

        $slugify = new Slugify();
        $slug = $slugify->slugify($trick->getName());
        $trick->setSlug($slug);
        $this->setReference('trick0', $trick);
        $manager->persist($trick);

        // Trick 2
        $userForTrick = $this->getReference('user' . random_int(0, 3));
        $categoryTrick = $this->getReference('category1');

        $trick = new Trick();
        $trick->setName('180');
        $trick->setUser($userForTrick);
        $trick->setCategory($categoryTrick);
        $trick->setDescription('Une rotation horizontale d\'un demi-tour, soit 180 degrés d\'angle');

        $video = new Video();
        $video->setVideoUrl('https://www.youtube.com/embed/XyARvRQhGgk');
        $video->setTrick($trick);
        $manager->persist($video);

        for ($i = 0; $i < 2; $i++) {
            $image = new Image();
            $image->setImageUrl('180-' . $i . '.jpg');
            $image->setTrick($trick);
            $manager->persist($image);
        }

        $slugify = new Slugify();
        $slug = $slugify->slugify($trick->getName());
        $trick->setSlug($slug);
        $this->setReference('trick1', $trick);
        $manager->persist($trick);

        // Trick 3
        $userForTrick = $this->getReference('user' . random_int(0, 3));
        $categoryTrick = $this->getReference('category1');

        $trick = new Trick();
        $trick->setName('Front flip');
        $trick->setUser($userForTrick);
        $trick->setCategory($categoryTrick);
        $trick->setDescription('Une rotation verticale vers l\'avant de 360 degrés');

        $video = new Video();
        $video->setVideoUrl('https://www.youtube.com/embed/gMfmjr-kuOg');
        $video->setTrick($trick);
        $manager->persist($video);

        for ($i = 0; $i < 2; $i++) {
            $image = new Image();
            $image->setImageUrl('frontflip-' . $i . '.jpg');
            $image->setTrick($trick);
            $manager->persist($image);
        }

        $slugify = new Slugify();
        $slug = $slugify->slugify($trick->getName());
        $trick->setSlug($slug);
        $this->setReference('trick2', $trick);
        $manager->persist($trick);

        // Trick 4
        $userForTrick = $this->getReference('user' . random_int(0, 3));
        $categoryTrick = $this->getReference('category2');

        $trick = new Trick();
        $trick->setName('Back slide Nose slide');
        $trick->setUser($userForTrick);
        $trick->setCategory($categoryTrick);
        $trick->setDescription('Ride up to the rail leaning on to your edge and keeping knees slightly bent. Once you are on the kicker do an Ollie and jump almost like you are doing a Backside 50-50. The difference is that once you push off the kicker you need to turn the board perpendicular to the rail while maintaining the position of your shoulders. This is the key to the trick! Now catch your balance by shifting your centre of weight onto your front leg, ride to the end of rail and return the board to its original position.');

        $video = new Video();
        $video->setVideoUrl('https://www.youtube.com/embed/oAK9mK7wWvw');
        $video->setTrick($trick);
        $manager->persist($video);


        for ($i = 0; $i < 3; $i++) {
            $image = new Image();
            $image->setImageUrl('bsns-' . $i . '.jpg');
            $image->setTrick($trick);
            $manager->persist($image);
        }

        $slugify = new Slugify();
        $slug = $slugify->slugify($trick->getName());
        $trick->setSlug($slug);
        $this->setReference('trick3', $trick);
        $manager->persist($trick);

        // Trick 5
        $userForTrick = $this->getReference('user' . random_int(0, 3));
        $categoryTrick = $this->getReference('category0');

        $trick = new Trick();
        $trick->setName('Suitcase');
        $trick->setUser($userForTrick);
        $trick->setCategory($categoryTrick);
        $trick->setDescription('A method grab with the knees bent, so you\'ll be able to grab the toe edge with your front hand and hold the board like a suitcase.');

        $video = new Video();
        $video->setVideoUrl('https://www.youtube.com/embed/JLo63PnCzW4');
        $video->setTrick($trick);
        $manager->persist($video);

        for ($i = 0; $i < 3; $i++) {
            $image = new Image();
            $image->setImageUrl('suitcase-' . $i . '.jpeg');
            $image->setTrick($trick);
            $manager->persist($image);
        }

        $slugify = new Slugify();
        $slug = $slugify->slugify($trick->getName());
        $trick->setSlug($slug);
        $this->setReference('trick4', $trick);
        $manager->persist($trick);

        // Trick 6
        $userForTrick = $this->getReference('user' . random_int(0, 3));
        $categoryTrick = $this->getReference('category0');

        $trick = new Trick();
        $trick->setName('Suitcase');
        $trick->setUser($userForTrick);
        $trick->setCategory($categoryTrick);
        $trick->setDescription('The front hand grabs the toe edge. The knees are bent and the nose of the board is pulled up behind your head.');

        $video = new Video();
        $video->setVideoUrl('https://www.youtube.com/embed/X_WhGuIY9Ak');
        $video->setTrick($trick);
        $manager->persist($video);

        for ($i = 0; $i < 3; $i++) {
            $image = new Image();
            $image->setImageUrl('japan-' . $i . '.jpeg');
            $image->setTrick($trick);
            $manager->persist($image);
        }

        $slugify = new Slugify();
        $slug = $slugify->slugify($trick->getName());
        $trick->setSlug($slug);
        $this->setReference('trick5', $trick);
        $manager->persist($trick);

        //Tricks 7
        $userForTrick = $this->getReference('user' . random_int(0, 3));
        $categoryTrick = $this->getReference('category0');

        $trick = new Trick();
        $trick->setName('Rocket');
        $trick->setUser($userForTrick);
        $trick->setCategory($categoryTrick);
        $trick->setDescription('Both hands grab the nose.');

        $video = new Video();
        $video->setVideoUrl('https://www.youtube.com/embed/nom7QBoGh5w');
        $video->setTrick($trick);
        $manager->persist($video);

        for ($i = 0; $i < 5; $i++) {
            $image = new Image();
            $image->setImageUrl('rocket-' . $i . '.jpeg');
            $image->setTrick($trick);
            $manager->persist($image);
        }

        $slugify = new Slugify();
        $slug = $slugify->slugify($trick->getName());
        $trick->setSlug($slug);
        $this->setReference('trick6', $trick);
        $manager->persist($trick);

        //Tricks 8
        $userForTrick = $this->getReference('user' . random_int(0, 3));
        $categoryTrick = $this->getReference('category1');

        $trick = new Trick();
        $trick->setName('BS Cork 540');
        $trick->setUser($userForTrick);
        $trick->setCategory($categoryTrick);
        $trick->setDescription('A 540 degree rotation off-axis with the backside leading into the spin.');

        $video = new Video();
        $video->setVideoUrl('https://www.youtube.com/embed/P5ZI-d-eHsI');
        $video->setTrick($trick);
        $manager->persist($video);

        for ($i = 0; $i < 3; $i++) {
            $image = new Image();
            $image->setImageUrl('bscork540-' . $i . '.jpeg');
            $image->setTrick($trick);
            $manager->persist($image);
        }

        $slugify = new Slugify();
        $slug = $slugify->slugify($trick->getName());
        $trick->setSlug($slug);
        $this->setReference('trick7', $trick);
        $manager->persist($trick);

        //Tricks 9
        $userForTrick = $this->getReference('user' . random_int(0, 3));
        $categoryTrick = $this->getReference('category2');

        $trick = new Trick();
        $trick->setName('Tailslide');
        $trick->setUser($userForTrick);
        $trick->setCategory($categoryTrick);
        $trick->setDescription('This is a slide performed on the tail of the board, while keeping the nose off the ground. The tail slide is a great way to develop your balance, board control and will make other tricks much more accessible.');

        $video = new Video();
        $video->setVideoUrl('https://www.youtube.com/embed/goGCcSoLegM');
        $video->setTrick($trick);
        $manager->persist($video);

        for ($i = 0; $i < 3; $i++) {
            $image = new Image();
            $image->setImageUrl('tailslide-' . $i . '.jpeg');
            $image->setTrick($trick);
            $manager->persist($image);
        }

        $slugify = new Slugify();
        $slug = $slugify->slugify($trick->getName());
        $trick->setSlug($slug);
        $this->setReference('trick8', $trick);
        $manager->persist($trick);

        //Tricks 10
        $userForTrick = $this->getReference('user' . random_int(0, 3));
        $categoryTrick = $this->getReference('category2');

        $trick = new Trick();
        $trick->setName('Tripod');
        $trick->setUser($userForTrick);
        $trick->setCategory($categoryTrick);
        $trick->setDescription('Tripod is riding down the slope with both hands and front of the board in the snow and the back of the board in the air.');

        $video = new Video();
        $video->setVideoUrl('https://www.youtube.com/embed/msD1jQL63dA');
        $video->setTrick($trick);
        $manager->persist($video);

        for ($i = 0; $i < 3; $i++) {
            $image = new Image();
            $image->setImageUrl('tripod-' . $i . '.jpeg');
            $image->setTrick($trick);
            $manager->persist($image);
        }

        $slugify = new Slugify();
        $slug = $slugify->slugify($trick->getName());
        $trick->setSlug($slug);
        $this->setReference('trick9', $trick);
        $manager->persist($trick);

        // Comments
        for ($i = 0; $i < 70; $i++) {
            $userComment = $this->getReference('user' . random_int(0, 3));
            $trickComment = $this->getReference('trick' . random_int(0, 9));

            $comment = new Comment();
            $comment->setUser($userComment);
            $comment->setTrick($trickComment);
            $comment->setContent($faker->sentence(20, true));

            $manager->persist($comment);
        }

        $manager->flush();
    }
}
