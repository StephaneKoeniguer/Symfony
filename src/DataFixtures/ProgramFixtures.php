<?php

namespace App\DataFixtures;

use App\Entity\Program;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;

class ProgramFixtures extends Fixture implements DependentFixtureInterface
{
    public const PROGRAMS =[
        [
            'title' => 'Friends',
            'synopsis' => 'Salut les amis',
            'reference' => 'category_Comedie',
        ],

        [
            'title' => 'La guerre des mondes',
            'synopsis' => 'Coucou nous voila',
            'reference' => 'category_Action',
        ],

        [
            'title' => 'One piece',
            'synopsis' => 'gum gum',
            'reference' => 'category_Animation',
        ],

        [
            'title' => 'Start trek',
            'synopsis' => 'La tête dans les étoiles',
            'reference' => 'category_Aventure',
        ],

        [
            'title' => 'Game of thrones',
            'synopsis' => 'Il faut couper la tête',
            'reference' => 'category_Horreur',
        ]

    ];

    public function load(ObjectManager $manager)
    {
        foreach (self::PROGRAMS as $key => $programValue) {
            $program = new Program();
            $program->setTitle($programValue['title']);
            $program->setSynopsis($programValue['synopsis']);
            $program->setCategory($this->getReference($programValue['reference']));
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
