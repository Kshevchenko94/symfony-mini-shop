<?php

namespace App\DataFixtures;

use App\Entity\Service;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class ServiceFixture extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        $services = [
            ['title' => 'Оценка стоимости автомобиля', 'is_active' => true, 'price' => 500],
            ['title' => 'Оценка стоимости квартиры', 'is_active' => true, 'price' => 1000],
            ['title' => 'Оценка стоимости бизнеса', 'is_active' => true, 'price' => 1500],
        ];
        foreach ($services as $item) {
            $service = new Service();
            $service->setTitle($item['title']);
            $service->setIsActive($item['is_active']);
            $service->setPrice($item['price']);
            $manager->persist($service);
        }

        $manager->flush();
    }
}
