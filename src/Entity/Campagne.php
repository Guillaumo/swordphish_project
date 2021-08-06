<?php

namespace App\Entity;

use App\Repository\CampagneRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
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

    public function __construct()
    {
        $this->destinataires = new ArrayCollection();
        $this->results = new ArrayCollection();
        $this->isSent = false;
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
}
