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
        $arrayCategory = ['Front board', 'Back-flip', 'Rotating', 'Slide'];
        for ($i = 0; $i < 4; $i++) {
            $category = new Category();
            $category->setName($arrayCategory[$i]);
            $this->setReference('category' . $i, $category);
            $manager->persist($category);
        }

        /*
         * Tricks
         */
        for ($i = 0; $i < 10; $i++) {
            $userForTrick = $this->getReference('user' . random_int(0, 3));
            $categoryTrick = $this->getReference('category' . random_int(0, 3));

            $trick = new Trick();
            $trick->setName($faker->words(random_int(1, 4), true));
            $trick->setUser($userForTrick);
            $trick->setCategory($categoryTrick);
            $trick->setDescription($faker->paragraphs(random_int(1, 3), true));

            $slugify = new Slugify();
            $slug = $slugify->slugify($trick->getName());
            $trick->setSlug($slug);
            $this->setReference('trick' . $i, $trick);
            $manager->persist($trick);
        }

        /*
         * Comments
         */
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
