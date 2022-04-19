<?php

namespace App\Entity;

use App\Repository\DonnesRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: DonnesRepository::class)]
class Donnes
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private $id;

    #[ORM\Column(type: 'datetime')]
    private $dateCollect;

    #[ORM\Column(type: 'float')]
    private $rating;

    #[ORM\Column(type: 'integer')]
    private $vote;

    #[ORM\ManyToOne(targetEntity: Application::class, inversedBy: 'datas')]
    #[ORM\JoinColumn(nullable: false)]
    private $application;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getDateCollect(): ?\DateTimeInterface
    {
        return $this->dateCollect;
    }

    public function setDateCollect(\DateTimeInterface $dateCollect): self
    {
        $this->dateCollect = $dateCollect;

        return $this;
    }

    public function getRating(): ?float
    {
        return $this->rating;
    }

    public function setRating(float $rating): self
    {
        $this->rating = $rating;

        return $this;
    }

    public function getVote(): ?int
    {
        return $this->vote;
    }

    public function setVote(int $vote): self
    {
        $this->vote = $vote;

        return $this;
    }

    public function getApplication(): ?Application
    {
        return $this->application;
    }

    public function setApplication(?Application $application): self
    {
        $this->application = $application;

        return $this;
    }
}
