<?php

namespace App\Entity;

use App\Repository\DonnesRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;

#[ORM\Entity(repositoryClass: DonnesRepository::class)]
class Donnes
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private $id;

    #[Groups(['donnes', 'application', 'source','os'])]

    #[ORM\Column(type: 'datetime')]
    private $dateCollect;

    #[Groups(['donnes', 'application', 'source', 'os'])]

    #[ORM\Column(type: 'float')]
    private $rating;

    #[Groups(['donnes', 'application','source', 'os'])]

    #[ORM\Column(type: 'integer')]
    private $vote;

    #[Groups(['donnes', 'os'])]
    #[ORM\ManyToOne(targetEntity: Application::class, inversedBy: 'datas')]
    #[ORM\JoinColumn(nullable: false)]
    private $application;

    #[Groups(['donnes', 'application', 'source'])]
    #[ORM\ManyToOne(targetEntity: OS::class, inversedBy: 'donnes')]
    #[ORM\JoinColumn(nullable: false)]
    private $os;

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

    public function getOs(): ?OS
    {
        return $this->os;
    }

    public function setOs(?OS $os): self
    {
        $this->os = $os;

        return $this;
    }
}
