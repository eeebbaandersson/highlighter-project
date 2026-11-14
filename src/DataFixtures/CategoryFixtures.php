<?php

namespace App\DataFixtures;

use App\Entity\Category;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class CategoryFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        $categories = [
            ['name' => 'Travel', 'color' => 'plane'],
            ['name' => 'Health', 'color' => 'heart'],
            ['name' => 'Concert', 'color' => 'music'],
            ['name' => 'Personal', 'color' => 'user'],
            ['name' => 'Career', 'color' => 'briefcase'],
            ['name' => 'Education', 'color' => 'graduation-cap'],
        ];

        foreach ($categories as $data) {
            $category = new Category();
            $category->setName($data['name']);
            $category->setColor($data['color']);
            $category->setIsDefault(true);

            $manager->persist($category);
        }

        $manager->flush();
    }
}
