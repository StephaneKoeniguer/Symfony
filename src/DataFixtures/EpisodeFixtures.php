<?php

namespace App\DataFixtures;

use Faker\Factory;
use App\Entity\Episode;
use Doctrine\Persistence\ObjectManager;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Symfony\Component\String\Slugger\SluggerInterface;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;

class EpisodeFixtures extends Fixture implements DependentFixtureInterface
{

    public function __construct(SluggerInterface $slugger)
    {
        $this->slugger = $slugger;
    }

    public function load(ObjectManager $manager): void
    {
        $faker = Factory::create();

        for($i = 0; $i < 50; $i++) {
            $episode = new Episode();
            //Ce Faker va nous permettre d'alimenter l'instance de Season que l'on souhaite ajouter en base

            $episode->setTitle($faker->realText($maxNbChars = 10, $indexSize = 2));
            $episode->setSynopsis($faker->paragraphs(3, true));
            $episode->setNumber($faker->numberBetween(1, 10));
            $episode->setDuration($faker->randomNumber(2));
            $episode->setSlug($this->slugger->slug($episode->getTitle()));

            //$seasonName = $faker->numberBetween(1, 10);
            $episode->setSeason($this->getReference('season_' . $faker->numberBetween(1, 5)));

            $manager->persist($episode);
        }
       
        $manager->flush();
    }


    public function getDependencies()
    {
        // Tu retournes ici toutes les classes de fixtures dont ProgramFixtures dépend
        return [
          SeasonFixtures::class,
        ];
    }
}
