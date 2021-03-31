<?php

namespace App\Entity;

use App\Repository\ProduitRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity(repositoryClass=ProduitRepository::class)
 */
class Produit
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255)
     * @Assert\NotBlank
     */
    private $titre;

    /**
     * @ORM\Column(type="date")
     * @Assert\NotNull
     * @Assert\Type("\DateTime")
     */
    private $date;

    /**
     * @ORM\Column(type="text")
     * @Assert\NotBlank
     */
    private $contenu;

    /**
     * @ORM\Column(type="text")
     * @Assert\NotBlank
     */
    private $image;

    /**
     * @ORM\OneToMany(targetEntity=ficheproduit::class, mappedBy="produit", orphanRemoval=true)
     */
    private $ficheproduits;

    public function __construct()
    {
        $this->ficheproduits = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getTitre(): ?string
    {
        return $this->titre;
    }

    public function setTitre(string $titre): self
    {
        $this->titre = $titre;

        return $this;
    }

    public function getDate(): ?\DateTimeInterface
    {
        return $this->date;
    }

    public function setDate(\DateTimeInterface $date): self
    {
        $this->date = $date;

        return $this;
    }

    public function getContenu(): ?string
    {
        return $this->contenu;
    }

    public function setContenu(string $contenu): self
    {
        $this->contenu = $contenu;

        return $this;
    }

    public function getImage(): ?string
    {
        return $this->image;
    }

    public function setImage(string $image): self
    {
        $this->image = $image;

        return $this;
    }

    /**
     * @return Collection|ficheproduit[]
     */
    public function getficheProduit(): Collection
    {
        return $this->ficheproduit;
    }

    public function addficheProduit(ficheProduit $commentaire): self
    {
        if (!$this->ficheproduit->contains($ficheproduit)) {
            $this->ficheproduit[] = $ficheproduit;
            $ficheproduit->setProduit($this);
        }

        return $this;
    }

    public function removeficheProduit(ficheproduit $ficheproduit): self
    {
        if ($this->ficheproduits->removeElement($ficheroduit)) {
            // set the owning side to null (unless already changed)
            if ($ficheproduit->getProduit() === $this) {
                $ficheproduit->setProduit(null);
            }
        }

        return $this;
    }
}
