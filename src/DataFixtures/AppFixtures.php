<?php

namespace App\DataFixtures;

use App\Entity\Category;
use App\Entity\Comment;
use App\Entity\Trick;
use App\Entity\User;
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

        /*
         * Fake users
         */
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

        /*
         * Category
         */
        $arrayCategory = ['Grabs', 'Flips', 'Rotation', 'Slide'];
        for ($i = 0; $i < 4; $i++) {
            $category = new Category();
            $category->setName($arrayCategory[$i]);
            $this->setReference('category' . $i, $category);
            $manager->persist($category);
        }

        /*
         * Tricks 1
         */
        $userForTrick = $this->getReference('user' . random_int(0, 3));
        $categoryTrick = $this->getReference('category0');

        $trick = new Trick();
        $trick->setName('Stalefish');
        $trick->setUser($userForTrick);
        $trick->setCategory($categoryTrick);
        $trick->setDescription('Saisie de la carre backside de la planche entre les deux pieds avec la main arrière');

        $slugify = new Slugify();
        $slug = $slugify->slugify($trick->getName());
        $trick->setSlug($slug);
        $this->setReference('trick0', $trick);
        $manager->persist($trick);

        /*
        * Tricks 2
        */
        $userForTrick = $this->getReference('user' . random_int(0, 3));
        $categoryTrick = $this->getReference('category2');

        $trick = new Trick();
        $trick->setName('180');
        $trick->setUser($userForTrick);
        $trick->setCategory($categoryTrick);
        $trick->setDescription('Une rotation horizontale d\'un demi-tour, soit 180 degrés d\'angle');

        $slugify = new Slugify();
        $slug = $slugify->slugify($trick->getName());
        $trick->setSlug($slug);
        $this->setReference('trick1', $trick);
        $manager->persist($trick);

        /*
        * Tricks 3
        */
        $userForTrick = $this->getReference('user' . random_int(0, 3));
        $categoryTrick = $this->getReference('category1');

        $trick = new Trick();
        $trick->setName('Front flip');
        $trick->setUser($userForTrick);
        $trick->setCategory($categoryTrick);
        $trick->setDescription('Une rotation verticale vers l\'avant de 360 degrés');

        $slugify = new Slugify();
        $slug = $slugify->slugify($trick->getName());
        $trick->setSlug($slug);
        $this->setReference('trick2', $trick);
        $manager->persist($trick);

        /*
        * Tricks 4
        */
        $userForTrick = $this->getReference('user' . random_int(0, 3));
        $categoryTrick = $this->getReference('category3');

        $trick = new Trick();
        $trick->setName('Nose slide');
        $trick->setUser($userForTrick);
        $trick->setCategory($categoryTrick);
        $trick->setDescription('Le slide se fait l\'avant de la planche sur la barre suivant l\'axe');

        $slugify = new Slugify();
        $slug = $slugify->slugify($trick->getName());
        $trick->setSlug($slug);
        $this->setReference('trick3', $trick);
        $manager->persist($trick);


        /*
         * Comments
         */
        for ($i = 0; $i < 70; $i++) {
            $userComment = $this->getReference('user' . random_int(0, 3));
            $trickComment = $this->getReference('trick' . random_int(0, 3));

            $comment = new Comment();
            $comment->setUser($userComment);
            $comment->setTrick($trickComment);
            $comment->setContent($faker->sentence(20, true));

            $manager->persist($comment);
        }

        $manager->flush();
    }
}
