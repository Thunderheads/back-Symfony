<?php

namespace App\Entity;

use App\Repository\ApplicationRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: ApplicationRepository::class)]
class Application
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private $id;

    #[ORM\Column(type: 'string', length: 255)]
    private $nom;

    #[ORM\OneToMany(mappedBy: 'application', targetEntity: Source::class)]
    private $sources;

    #[ORM\ManyToOne(targetEntity: Responsable::class, inversedBy: 'applications')]
    private $administrateur;

    #[ORM\OneToMany(mappedBy: 'application', targetEntity: Donnes::class)]
    private $datas;

    public function __construct()
    {
        $this->sources = new ArrayCollection();
        $this->datas = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getNom(): ?string
    {
        return $this->nom;
    }

    public function setNom(string $nom): self
    {
        $this->nom = $nom;

        return $this;
    }

    /**
     * @return Collection<int, Source>
     */
    public function getSources(): Collection
    {
        return $this->sources;
    }

    public function addSource(Source $source): self
    {
        if (!$this->sources->contains($source)) {
            $this->sources[] = $source;
            $source->setApplication($this);
        }

        return $this;
    }

    public function removeSource(Source $source): self
    {
        if ($this->sources->removeElement($source)) {
            // set the owning side to null (unless already changed)
            if ($source->getApplication() === $this) {
                $source->setApplication(null);
            }
        }

        return $this;
    }

    public function getAdministrateur(): ?Responsable
    {
        return $this->administrateur;
    }

    public function setAdministrateur(?Responsable $administrateur): self
    {
        $this->administrateur = $administrateur;

        return $this;
    }

    /**
     * @return Collection<int, Donnes>
     */
    public function getDatas(): Collection
    {
        return $this->datas;
    }

    public function addData(Donnes $data): self
    {
        if (!$this->datas->contains($data)) {
            $this->datas[] = $data;
            $data->setApplication($this);
        }

        return $this;
    }

    public function removeData(Donnes $data): self
    {
        if ($this->datas->removeElement($data)) {
            // set the owning side to null (unless already changed)
            if ($data->getApplication() === $this) {
                $data->setApplication(null);
            }
        }

        return $this;
    }
}
