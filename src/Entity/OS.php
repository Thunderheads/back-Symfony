<?php

namespace App\Entity;

use App\Repository\OSRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;

#[ORM\Entity(repositoryClass: OSRepository::class)]
class OS
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private $id;

    #[Groups(['donnes', 'application', 'source', 'os'])]

    #[ORM\Column(type: 'string', length: 255)]
    private $nom;

    #[ORM\OneToMany(mappedBy: 'os', targetEntity: Source::class)]
    private $sources;

    #[Groups([ 'os'])]
    #[ORM\OneToMany(mappedBy: 'os', targetEntity: Donnes::class)]
    private $donnes;

    public function __construct()
    {
        $this->sources = new ArrayCollection();
        $this->donnes = new ArrayCollection();
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
            $source->setOs($this);
        }

        return $this;
    }

    public function removeSource(Source $source): self
    {
        if ($this->sources->removeElement($source)) {
            // set the owning side to null (unless already changed)
            if ($source->getOs() === $this) {
                $source->setOs(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, Donnes>
     */
    public function getDonnes(): Collection
    {
        return $this->donnes;
    }

    public function addDonne(Donnes $donne): self
    {
        if (!$this->donnes->contains($donne)) {
            $this->donnes[] = $donne;
            $donne->setOs($this);
        }

        return $this;
    }

    public function removeDonne(Donnes $donne): self
    {
        if ($this->donnes->removeElement($donne)) {
            // set the owning side to null (unless already changed)
            if ($donne->getOs() === $this) {
                $donne->setOs(null);
            }
        }

        return $this;
    }



}
