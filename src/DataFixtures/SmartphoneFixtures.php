<?php

namespace App\DataFixtures;

use App\Entity\Shop;
use App\Entity\Smartphone;
use App\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Faker\Provider\Color;
use Faker\Factory;
use Faker\Generator;
use Faker\Provider\Internet;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class SmartphoneFixtures extends Fixture
{
    private const SHOP_USER_REFERENCE = 'shop-user';
    
    public function __construct(UserPasswordEncoderInterface $encoder)
    {
        $this->encoder = $encoder;
    }

    public function load(ObjectManager $manager): void
    {
        // $product = new Product();
        // $manager->persist($product);

        $faker = Factory::create('fr_FR');
        $description = join($faker->paragraphs(1));

        // Ajoute 10 smartphones
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
