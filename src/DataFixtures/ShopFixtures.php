<?php

namespace App\DataFixtures;

use App\Entity\Shop;
use App\Entity\Smartphone;
use App\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;
use Faker\Provider\Color;
use Faker\Factory;
use Faker\Generator;
use Faker\Provider\Internet;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class ShopFixtures extends Fixture
{
    public const SHOP_USER_REFERENCE = 'shop-user';
    
    public function __construct(UserPasswordEncoderInterface $encoder)
    {
        $this->encoder = $encoder;
    }

    public function load(ObjectManager $manager): void
    {
        // $product = new Product();
        // $manager->persist($product);

        $faker = Factory::create('fr_FR');

        // Créer 3 shops
        for ($i=0; $i<3; $i++)
        {
            $shop = new Shop;

            $shop->setName($faker->name)
                ->setUrl($faker->url);

            $manager->persist($shop);

            $this->addReference(self::SHOP_USER_REFERENCE . '_' . $i, $shop);
        }
        

        $manager->flush();
    }
}
