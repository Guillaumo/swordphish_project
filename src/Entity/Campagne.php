<?php

namespace App\Entity;

use App\Repository\CampagneRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use phpDocumentor\Reflection\Types\Boolean;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity(repositoryClass=CampagneRepository::class)
 * @ORM\HasLifecycleCallbacks()
 */
class Campagne
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255, unique=true)
     * @Assert\NotBlank
     */
    private $name;

    /**
     * @ORM\Column(type="date")
     * 
     */
    private $date;

    /**
     * @ORM\ManyToMany(targetEntity=Destinataire::class, inversedBy="campagnes")
     */
    private $destinataires;

    /**
     * @ORM\OneToMany(targetEntity=ResultCampaignUser::class, mappedBy="campagne", orphanRemoval=true)
     */
    private $results;

    /**
     * @ORM\Column(type="boolean")
     */
    private $isSent;

    /**
     * @ORM\Column(type="boolean")
     */
    private $isEnable;

    /**
     * @ORM\Column(type="boolean")
     */
    private $isInfoSent;

    /**
     * @ORM\Column(type="integer")
     */
    private $number_recipients_per_group;

    /**
     * @ORM\Column(type="integer")
     */
    private $tempo_minutes;

    public function __construct()
    {
        $this->destinataires = new ArrayCollection();
        $this->results = new ArrayCollection();
        $this->isSent = false;
        $this->isEnable = true;
        $this->isInfoSent = false;
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): self
    {
        $this->name = $name;

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

    /**
     * @ORM\PrePersist
     */
    public function setDateValue()
    {
        $this->date = new \DateTime();
    }

    /**
     * @return Collection|Destinataire[]
     */
    public function getDestinataires(): Collection
    {
        return $this->destinataires;
    }

    public function addDestinataire(Destinataire $destinataire): self
    {
        if (!$this->destinataires->contains($destinataire)) {
            $this->destinataires[] = $destinataire;
        }

        return $this;
    }

    public function removeDestinataire(Destinataire $destinataire): self
    {
        if ($this->destinataires->contains($destinataire)) {
            $this->destinataires->removeElement($destinataire);
        }

        return $this;
    }

    /**
     * @return Collection|ResultCampaignUser[]
     */
    public function getResults(): Collection
    {
        return $this->results;
    }

    public function addResult(ResultCampaignUser $result): self
    {
        if (!$this->results->contains($result)) {
            $this->results[] = $result;
            $result->setCampagne($this);
        }

        return $this;
    }

    public function removeResult(ResultCampaignUser $result): self
    {
        if ($this->results->removeElement($result)) {
            // set the owning side to null (unless already changed)
            if ($result->getCampagne() === $this) {
                $result->setCampagne(null);
            }
        }

        return $this;
    }

    public function __toString()
    {
        return 'Campagne '.$this->name;
    }

    public function getIsSent(): ?bool
    {
        return $this->isSent;
    }

    public function setIsSent(bool $isSent): self
    {
        $this->isSent = $isSent;

        return $this;
    }

    public function getIsEnable(): ?bool
    {
        return $this->isEnable;
    }

    public function setIsEnable(bool $isEnable): self
    {
        $this->isEnable = $isEnable;

        return $this;
    }

    public function getIsInfoSent(): ?bool
    {
        return $this->isInfoSent;
    }

    public function setIsInfoSent(bool $isInfoSent): self
    {
        $this->isInfoSent = $isInfoSent;

        return $this;
    }

    public function getNumberRecipientsPerGroup(): ?int
    {
        return $this->number_recipients_per_group;
    }

    public function setNumberRecipientsPerGroup(?int $number_recipients_per_group): self
    {
        $this->number_recipients_per_group = $number_recipients_per_group;

        return $this;
    }

    public function getTempoMinutes(): ?int
    {
        return $this->tempo_minutes;
    }

    public function setTempoMinutes(?int $tempo_minutes): self
    {
        $this->tempo_minutes = $tempo_minutes;

        return $this;
    }

    /**
     * Méthode pour calculer la durée d'envoi des emails de la campagne
     *
     * @return integer|null
     */
    public function getDurationSending(): ?int
    {
        $number_groups = intdiv(count($this->destinataires),$this->number_recipients_per_group);
        if((count($this->destinataires) % $this->number_recipients_per_group) >0) {
            return ($number_groups) * $this->tempo_minutes;
        }
        return ($number_groups - 1) * $this->tempo_minutes;
    }

    // public function getOfficesDestinataires()
    // {
    //     $offices =[];
    //     foreach($this->destinataires as $destinataire)
    //     {
    //         $offices [] = $destinataire->getOffice();
    //     }
    //     return $offices;
    // }
}
