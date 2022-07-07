<?php

namespace App\DataFixtures;

use App\Entity\Customer;
use App\Entity\Smartphone;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;
use Faker\Provider\Color;
use Faker\Factory;
use Faker\Generator;
use Faker\Provider\Internet;
use Symfony\Component\Security\Core\Encoder\EncoderFactoryInterface;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class CustomerFixtures extends Fixture
{
    public const CUSTOMER_USER_REFERENCE = 'customer-user';
    
    public function __construct(UserPasswordEncoderInterface $encoder)
    {
        $this->encoder = $encoder;
    }

    public function load(ObjectManager $manager): void
    {
        // $product = new Product();
        // $manager->persist($product);

        $faker = Factory::create('fr_FR');

        // Créer 3 customers
        for ($i=0; $i<3; $i++)
        {
            $customer = new Customer;

            $customer->setName($faker->name)
                ->setUrl($faker->url);

            $manager->persist($customer);

            $this->addReference(self::CUSTOMER_USER_REFERENCE . '_' . $i, $customer);
        }
        

        $manager->flush();
    }
}
