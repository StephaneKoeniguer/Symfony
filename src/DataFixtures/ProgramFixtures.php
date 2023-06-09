<?php

namespace App\DataFixtures;

use Faker\Factory;
use App\Entity\User;
use App\Entity\Program;
use Doctrine\Persistence\ObjectManager;

use Doctrine\Bundle\FixturesBundle\Fixture;
use Symfony\Component\String\Slugger\SluggerInterface;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;

class ProgramFixtures extends Fixture implements DependentFixtureInterface
{
    private SluggerInterface $slugger;

    public function __construct(SluggerInterface $slugger)
    {
        $this->slugger = $slugger;
    }

    public function load(ObjectManager $manager)
    {
        //Puis ici nous demandons à la Factory de nous fournir un Faker
        $faker = Factory::create();

        for($i = 0; $i < 20; $i++) {
            $program = new Program();
            //Ce Faker va nous permettre d'alimenter l'instance de Season que l'on souhaite ajouter en base

            $program->setTitle($faker->realText($maxNbChars = 10, $indexSize = 2));
            $program->setSlug($this->slugger->slug($program->getTitle()));
            $program->setSynopsis($faker->realText($maxNbChars = 200, $indexSize = 2));
            $program->setPoster($faker->realText($maxNbChars = 10, $indexSize = 2));
            $program->setCountry($faker->country());
            $program->setYear($faker->year());

            $randomCategoryKey = array_rand(CategoryFixtures::CATEGORIES);
            $categoryName = CategoryFixtures::CATEGORIES[$randomCategoryKey];

            $user = new User();

            $program->setCategory($this->getReference('category_' . $categoryName));
            $program->setOwner($this->getReference('contributor1'));
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
