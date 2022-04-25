<?php

namespace App\Entity;

use App\Repository\OptionbienRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: OptionbienRepository::class)]
class Optionbien
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private $id;

    #[ORM\ManyToOne(targetEntity: bien::class, inversedBy: 'optionbiens')]
    private $idbien;

    #[ORM\ManyToOne(targetEntity: option::class, inversedBy: 'optionbiens')]
    private $idoption;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getIdbien(): ?bien
    {
        return $this->idbien;
    }

    public function setIdbien(?bien $idbien): self
    {
        $this->idbien = $idbien;

        return $this;
    }

    public function getIdoption(): ?option
    {
        return $this->idoption;
    }

    public function setIdoption(?option $idoption): self
    {
        $this->idoption = $idoption;

        return $this;
    }
}
