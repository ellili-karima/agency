<?php

namespace App\Entity;

use App\Repository\OptionRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: OptionRepository::class)]
#[ORM\Table(name: '`option`')]
class Option
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private $id;

    #[ORM\Column(type: 'string', length: 15)]
    private $designation;

    #[ORM\OneToMany(mappedBy: 'idoption', targetEntity: Optionbien::class)]
    private $optionbiens;

    public function __construct()
    {
        $this->optionbiens = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getDesignation(): ?string
    {
        return $this->designation;
    }

    public function setDesignation(string $designation): self
    {
        $this->designation = $designation;

        return $this;
    }

    /**
     * @return Collection<int, Optionbien>
     */
    public function getOptionbiens(): Collection
    {
        return $this->optionbiens;
    }

    public function addOptionbien(Optionbien $optionbien): self
    {
        if (!$this->optionbiens->contains($optionbien)) {
            $this->optionbiens[] = $optionbien;
            $optionbien->setIdoption($this);
        }

        return $this;
    }

    public function removeOptionbien(Optionbien $optionbien): self
    {
        if ($this->optionbiens->removeElement($optionbien)) {
            // set the owning side to null (unless already changed)
            if ($optionbien->getIdoption() === $this) {
                $optionbien->setIdoption(null);
            }
        }

        return $this;
    }
}
