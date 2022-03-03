<?php

namespace App\DataFixtures;

use App\DataFixtures\ShopFixtures;
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

class UserFixtures extends Fixture
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
      
        // Créer 30 utilisateurs
        for ($j=0; $j<30; $j++)
        {
            $user = new User;

            $user->setFirstname($faker->firstName)
                ->setLastname($faker->lastName)
                ->setUsername($faker->userName)
                ->setEmail($faker->email)
                ->setPassword($this->encoder->encodePassword($user, 'password'))
                ->setRoles($user->getRoles())
                ->addShop($this->getReference(ShopFixtures::SHOP_USER_REFERENCE . '_'. mt_rand(0,2)));

            $manager->persist($user);
        }
        
        

        $manager->flush();
    }
}
