<?php

namespace App\Entity;

use App\Repository\DestinataireRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity(repositoryClass=DestinataireRepository::class)
 */
class Destinataire
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
    private $lastname;

    /**
     * @ORM\Column(type="string", length=255)
     * @Assert\NotBlank
     */
    private $firstname;

    /**
     * @ORM\Column(type="string", length=255)
     * @Assert\NotBlank
     */
    private $email;

    /**
     * @ORM\Column(type="string", length=255)
     * @Assert\NotBlank
     */
    private $office;

    /**
     * @ORM\ManyToMany(targetEntity="App\Entity\Campagne", mappedBy="destinataires")
     */
    private $campagnes;
   
    /**
     * @ORM\OneToMany(targetEntity=ResultCampaignUser::class, mappedBy="destinataire")
     */
    private $results;

    public function __construct()
    {
        $this->campagnes = new ArrayCollection();
        $this->results = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getLastname(): ?string
    {
        return $this->lastname;
    }

    public function setLastname(string $lastname): self
    {
        $this->lastname = $lastname;

        return $this;
    }

    public function getFirstname(): ?string
    {
        return $this->firstname;
    }

    public function setFirstname(string $firstname): self
    {
        $this->firstname = $firstname;

        return $this;
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(string $email): self
    {
        $this->email = $email;

        return $this;
    }

    public function getOffice(): ?string
    {
        return $this->office;
    }

    public function setOffice(string $office): self
    {
        $this->office = $office;

        return $this;
    }

    /**
     * @return Collection|Campagne[]
     */
    public function getCampagnes(): Collection
    {
        return $this->campagnes;
    }

    public function addCampagne(Campagne $campagne): self
    {
        if (!$this->campagnes->contains($campagne)) {
            $this->campagnes[] = $campagne;
            $campagne->addDestinataire($this);
        }
        return $this;
    }

    public function removeCampagne(Campagne $campagne): self
    {
        if ($this->campagnes->contains($campagne)) {
            $this->campagnes->removeElement($campagne);
            $campagne->removeDestinataire($this);
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
            $result->setDestinataire($this);
        }

        return $this;
    }

    public function removeResult(ResultCampaignUser $result): self
    {
        if ($this->results->removeElement($result)) {
            // set the owning side to null (unless already changed)
            if ($result->getDestinataire() === $this) {
                $result->setDestinataire(null);
            }
        }

        return $this;
    }

    public function __toString()
    {
        return $this->firstname.' '.$this->lastname.' de l\'agence '.$this->office;
    }
}
