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
    public const CUSTOMER_SMARTPHONE_REFERENCE = 'customer-smartphone';

    
    public function __construct(UserPasswordEncoderInterface $encoder)
    {
        $this->encoder = $encoder;
    }

    public function load(ObjectManager $manager): void
    {
        // $product = new Product();
        // $manager->persist($product);

        $faker = Factory::create('fr_FR');

        // Créer 20 customers
        for ($i=0; $i<20; $i++)
        {
            $customer = new Customer;

            $customer->setName($faker->name)
                ->setUrl($faker->url)
                ->setUsername($faker->userName)
                ->setPassword($this->encoder->encodePassword($customer, 'password'))
                ->setRoles($customer->getRoles());
                
            $manager->persist($customer);

            $this->addReference(self::CUSTOMER_USER_REFERENCE . '_' . $i, $customer);
            $this->addReference(self::CUSTOMER_SMARTPHONE_REFERENCE . '_' . $i, $customer);
        }
        

        $manager->flush();
    }
}
