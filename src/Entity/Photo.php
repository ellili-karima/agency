<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use App\Repository\PhotoRepository;
use Symfony\Component\HttpFoundation\File\File;

#[ORM\Entity(repositoryClass: PhotoRepository::class)]
class Photo
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private $id;

    #[ORM\Column(type: 'string', length: 40)]
    private $photo;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getPhoto(): string|File|null
    {
        return $this->photo;
    }

    public function setPhoto(string|File|null $photo): self
    {
        $this->photo = $photo;

        return $this;
    }
}
