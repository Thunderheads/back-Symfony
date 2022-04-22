<?php

namespace App\Entity;

use App\Repository\SourceRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;

#[ORM\Entity(repositoryClass: SourceRepository::class)]
class Source
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private $id;

    #[ORM\Column(type: 'string', length: 255)]
    private $url;

    #[Groups(['source'])]
    #[ORM\ManyToOne(targetEntity: Application::class, inversedBy: 'sources')]
    #[ORM\JoinColumn(nullable: false)]
    private $application;

    #[Groups(['donnes', 'application', 'source'])]

    #[ORM\ManyToOne(targetEntity: OS::class, inversedBy: 'sources')]
    #[ORM\JoinColumn(nullable: false)]
    private $os;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getUrl(): ?string
    {
        return $this->url;
    }

    public function setUrl(string $url): self
    {
        $this->url = $url;

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
