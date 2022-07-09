<?php

namespace App\DataFixtures;

use App\Entity\Customer;
use App\Entity\Product;
use App\Entity\Cart;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class AppFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        $product = new Product();
        $product->setName('Laptop');
        $product->setPrice(2500);
        $manager->persist($product);
        $manager->flush();

        $cart = new Cart();
        $customer = new Customer();
        $customer->setName('Tom Tester');
        $customer->setEmail('test@tester.com');
        $customer->setPhone('800-555-1212');
        $manager->persist($customer);
        $manager->flush();

        $cart->setCustomer($customer);
        $cart->addProduct($product);
        $product = new Product();
        $product->setName('Docking Station');
        $product->setPrice(50);
        $manager->persist($product);
        $manager->flush();
        $cart->addProduct($product);
        $datetime = new \DateTime();
        $cart->setDatetime($datetime);
        $manager->persist($cart);
        $manager->flush();
        $customer = new Customer();
        $customer->setName('Test NoCart');
        $customer->setEmail('No Email');
        $customer->setPhone('800-111-2222');
        $manager->persist($customer);
        $manager->flush();
    }
}
