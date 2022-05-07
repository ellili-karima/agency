<?php

namespace App\Entity;

use App\Entity\User;
use App\Entity\Photo;
use App\Entity\Appointement;
use Doctrine\ORM\Mapping as ORM;
use App\Repository\BienRepository;
use Doctrine\Common\Collections\Collection;
use Doctrine\Common\Collections\ArrayCollection;

#[ORM\Entity(repositoryClass: BienRepository::class)]
class Bien
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private $id;

    #[ORM\Column(type: 'string', length: 50)]
    private $titre;

    #[ORM\Column(type: 'integer')]
    private $nbrepieces;

    #[ORM\Column(type: 'integer')]
    private $surface;

    #[ORM\Column(type: 'integer')]
    private $prix;

    #[ORM\Column(type: 'string', length: 100)]
    private $localisation;

    #[ORM\Column(type: 'string', length: 15)]
    private $type;

    #[ORM\Column(type: 'integer')]
    private $etage;

    #[ORM\Column(type: 'string', length: 10)]
    private $transactiontype;

    #[ORM\Column(type: 'string', length: 100)]
    private $description;

    #[ORM\Column(type: 'datetime')]
    private $dateconstruction;

    #[ORM\OneToMany(mappedBy: 'titre', targetEntity: Appointement::class)]
    private $appointements;

    
    #[ORM\OneToMany(mappedBy: 'bien', targetEntity: Photo::class,  orphanRemoval: true, cascade:['persist'])]
    private $photos;

    #[ORM\OneToMany(mappedBy: 'idbien', targetEntity: Optionbien::class)]
    private $optionbiens;

    #[ORM\ManyToOne(targetEntity: User::class, inversedBy: 'biens')]
    private $employeur;

    
   
    public function __construct()
    {
        $this->optionbiens = new ArrayCollection();
        $this->appointements = new ArrayCollection();
        $this->photos = new ArrayCollection();
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

    public function getNbrepieces(): ?int
    {
        return $this->nbrepieces;
    }

    public function setNbrepieces(int $nbrepieces): self
    {
        $this->nbrepieces = $nbrepieces;

        return $this;
    }

    public function getSurface(): ?int
    {
        return $this->surface;
    }

    public function setSurface(int $surface): self
    {
        $this->surface = $surface;

        return $this;
    }

    public function getPrix(): ?int
    {
        return $this->prix;
    }

    public function setPrix(int $prix): self
    {
        $this->prix = $prix;

        return $this;
    }

    public function getLocalisation(): ?string
    {
        return $this->localisation;
    }

    public function setLocalisation(string $localisation): self
    {
        $this->localisation = $localisation;

        return $this;
    }

    public function getType(): ?string
    {
        return $this->type;
    }

    public function setType(string $type): self
    {
        $this->type = $type;

        return $this;
    }

    public function getEtage(): ?int
    {
        return $this->etage;
    }

    public function setEtage(int $etage): self
    {
        $this->etage = $etage;

        return $this;
    }

    public function getTransactiontype(): ?string
    {
        return $this->transactiontype;
    }

    public function setTransactiontype(string $transactiontype): self
    {
        $this->transactiontype = $transactiontype;

        return $this;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(string $description): self
    {
        $this->description = $description;

        return $this;
    }

    public function getDateconstruction(): ?\DateTimeInterface
    {
        return $this->dateconstruction;
    }

    public function setDateconstruction(\DateTimeInterface $dateconstruction): self
    {
        $this->dateconstruction = $dateconstruction;

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
            $optionbien->setIdbien($this);
        }

        return $this;
    }

    public function removeOptionbien(Optionbien $optionbien): self
    {
        if ($this->optionbiens->removeElement($optionbien)) {
            // set the owning side to null (unless already changed)
            if ($optionbien->getIdbien() === $this) {
                $optionbien->setIdbien(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, Appointement>
     */
    public function getAppointements(): Collection
    {
        return $this->appointements;
    }

    public function addAppointement(Appointement $appointement): self
    {
        if (!$this->appointements->contains($appointement)) {
            $this->appointements[] = $appointement;
            $appointement->setTitre($this);
        }

        return $this;
    }

    public function removeAppointement(Appointement $appointement): self
    {
        if ($this->appointements->removeElement($appointement)) {
            // set the owning side to null (unless already changed)
            if ($appointement->getTitre() === $this) {
                $appointement->setTitre(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, Photo>
     */
    public function getPhotos(): Collection
    {
        return $this->photos;
    }

    public function addPhoto(Photo $photo): self
    {
        if (!$this->photos->contains($photo)) {
            $this->photos[] = $photo;
            $photo->setBien($this);
        }

        return $this;
    }

    public function removePhoto(Photo $photo): self
    {
        if ($this->photos->removeElement($photo)) {
            // set the owning side to null (unless already changed)
            if ($photo->getBien() === $this) {
                $photo->setBien(null);
            }
        }

        return $this;
    }


    #[ORM\PostRemove]
    public function deletePhoto(): void
    {
        // 1. On vérifie que le fichier existe
        if (file_exists(__DIR__.'/../../public/img/biens/'. $this->photo)) {

            // 2. On supprime le fichier
            unlink(__DIR__.'/../../public/img/biens/'. $this->photo);
        }
        // 3. On indique quand utiliser cette méthode grâce aux évènements:
        // #[ORM\HasLifecycleCallbacks]à ajouter sur la class
        // #[ORM\PostRemove] à ajouter sur la méthode qui prend l'évènement

    }
    

    public function getEmployeur(): ?user
    {
        return $this->employeur;
    }

    public function setEmployeur(?user $employeur): self
    {
        $this->employeur = $employeur;

        return $this;
    }
    
   
}
