<?php

namespace App\Entity;

use DateTime;
use Doctrine\DBAL\Types\Types;
use App\Repository\ActorRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Vich\UploaderBundle\Mapping\Annotation as Vich;
//Ici on importe le package Vich, que l’on utilisera sous l’alias “Vich”
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\HttpFoundation\File\File;


#[ORM\Entity(repositoryClass: ActorRepository::class)]
#[Vich\Uploadable]
class Actor
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $name = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $poster = null;

    #[Vich\UploadableField(mapping: 'poster_file', fileNameProperty: 'poster')]
    #[Assert\File(
        maxSize: '1M',
        mimeTypes: ['image/jpeg', 'image/png', 'image/webp'],
    )]
    private ?File $posterFile = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE, nullable: true)]
    private ?Datetime $updatedAt = null;

    #[ORM\ManyToMany(targetEntity: Program::class, inversedBy: 'actors')]
    private Collection $programs;

    public function __construct()
    {
        $this->programs = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): self
    {
        $this->name = $name;

        return $this;
    }

    /**
     * @return Collection<int, Program>
     */
    public function getPrograms(): Collection
    {
        return $this->programs;
    }

    public function addProgram(Program $program): self
    {
        if (!$this->programs->contains($program)) {
            $this->programs[] = $program;
            if (!$program->getActors()->contains($this)) {
                $program->addActor($this);
            }
        }

        return $this;
    }

    public function removeProgram(Program $program): self
    {
        if ($this->programs->removeElement($program)) {
            if ($program->getActors()->contains($this)) {
                $program->removeActor($this);
            }
        }

        return $this;
    }

    public function setPosterFile(File $image = null): Actor
    {
        $this->posterFile = $image;
        if ($image) {
            $this->updatedAt = new DateTime('now');
        }
        return $this;
    }

    public function getPosterFile(): ?File
    {
        return $this->posterFile;
    }


    public function getUpdatedAt()
    {
        return $this->updatedAt;
    }

    public function setUpdatedAt($updatedAt)
    {
        $this->updatedAt = $updatedAt;

        return $this;
    }

    public function getPoster()
    {
        return $this->poster;
    }

    public function setPoster($poster)
    {
        $this->poster = $poster;

        return $this;
    }
}
