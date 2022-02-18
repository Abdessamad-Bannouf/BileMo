<?php

namespace App\DataFixtures;

use App\Entity\Smartphone;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Faker\Factory;
use Faker\Provider\Color;

class AppFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        // $product = new Product();
        // $manager->persist($product);

        $faker = Factory::create('fr_FR');
        $description = join($faker->paragraphs(1));

        // Ajoute 10 smartphone
        for ($i=0; $i<10; $i++)
        {
            $smartphone = new Smartphone;

            $smartphone->setDesignation($faker->word)
            ->setDescription($description)
            ->setColor($faker->colorName)
            ->setPrice($faker->randomNumber(4))
            ->setYear($faker->dateTimeBetween('2022-01-01', 'now'));

            $manager->persist($smartphone);
        }

        $manager->flush();
    }
}
