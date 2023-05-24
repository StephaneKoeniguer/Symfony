<?php

namespace App\DataFixtures;

use App\Entity\Program;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;

use Faker\Factory;

class ProgramFixtures extends Fixture implements DependentFixtureInterface
{
    
    public function load(ObjectManager $manager)
    {
        //Puis ici nous demandons à la Factory de nous fournir un Faker
        $faker = Factory::create();

        for($i = 0; $i < 50; $i++) {
            $program = new Program();
            //Ce Faker va nous permettre d'alimenter l'instance de Season que l'on souhaite ajouter en base

            $program->setTitle($faker->sentence());
            $program->setSynopsis($faker->realText($maxNbChars = 200, $indexSize = 2));
            $program->setPoster($faker->image(null, 640, 480));
            $program->setCountry($faker->country());
            $program->setYear($faker->year());
            $randomCategoryKey = array_rand(CategoryFixtures::CATEGORIES);
            $categoryName = CategoryFixtures::CATEGORIES[$randomCategoryKey];

            $program->setCategory($this->getReference('category_' . $categoryName));
            $this->addReference('program_' . $i, $program);

            $manager->persist($program);
        }
       
        $manager->flush();
    }

    public function getDependencies()
    {
        // Tu retournes ici toutes les classes de fixtures dont ProgramFixtures dépend
        return [
          CategoryFixtures::class,
        ];
    }


}
