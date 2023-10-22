<?php

namespace App\DataFixtures;

use App\Entity\Book;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Faker;

class AppFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        $faker = Faker\Factory::create();
        for ($i = 0; $i < 20; $i++){
            $book = new Book();
            $book->setTitle($this->generateTitle($faker));
            $book->setAuthor($faker->name());
            $book->setPublishedAt($faker->datetime());
            $manager->persist($book);
        }

        $manager->flush();
    }


    private function generateTitle($faker){
        $sentence = $faker->sentence(rand(1,5));
        return substr($sentence, 0, strlen($sentence) - 1);
    }
    
}
