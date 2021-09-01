<?php

namespace App\Entity;

use App\Repository\ResultCampaignUserRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity(repositoryClass=ResultCampaignUserRepository::class)
 */
class ResultCampaignUser
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $userip;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $hostname;

    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    private $screenwidth;

    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    private $screenheight;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $navigator;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     * @Assert\NotBlank
     */
    private $lastname;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     * @Assert\NotBlank
     */
    private $firstname;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     * @Assert\NotBlank
     */
    private $telephone;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     * @Assert\NotBlank
     * @Assert\Email(message = "L'email '{{ value }} n'est pas un email valide")
     */
    private $email;

    /**
     * @ORM\Column(type="datetime")
     * @Assert\NotBlank
     */
    private $created_at;

    /**
     * @ORM\ManyToOne(targetEntity=Campagne::class, inversedBy="results")
     * @ORM\JoinColumn(nullable=false)
     */
    private $campagne;

    /**
     * @ORM\ManyToOne(targetEntity=Destinataire::class, inversedBy="results")
     * @ORM\JoinColumn(nullable=false)
     */
    private $destinataire;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getUserip(): ?string
    {
        return $this->userip;
    }

    public function setUserip(?string $userip): self
    {
        $this->userip = $userip;

        return $this;
    }

    public function getUsername(): ?string
    {
        return $this->username;
    }

    public function setUsername(?string $username): self
    {
        $this->username = $username;

        return $this;
    }

    public function getHostname(): ?string
    {
        return $this->hostname;
    }

    public function setHostname(?string $hostname): self
    {
        $this->hostname = $hostname;

        return $this;
    }

    public function getScreenwidth(): ?int
    {
        return $this->screenwidth;
    }

    public function setScreenwidth(?int $screenwidth): self
    {
        $this->screenwidth = $screenwidth;

        return $this;
    }

    public function getScreenheight(): ?int
    {
        return $this->screenheight;
    }

    public function setScreenheight(?int $screenheight): self
    {
        $this->screenheight = $screenheight;

        return $this;
    }

    public function getNavigator(): ?string
    {
        return $this->navigator;
    }

    public function setNavigator(?string $navigator): self
    {
        $this->navigator = $navigator;

        return $this;
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

    public function getTelephone(): ?string
    {
        return $this->telephone;
    }

    public function setTelephone(string $telephone): self
    {
        $this->telephone = $telephone;

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

    public function getCreatedAt(): ?\DateTimeInterface
    {
        return $this->created_at;
    }

    public function setCreatedAt(\DateTimeInterface $created_at): self
    {
        $this->created_at = $created_at;

        return $this;
    }

    public function getCampagne(): ?Campagne
    {
        return $this->campagne;
    }

    public function setCampagne(?Campagne $campagne): self
    {
        $this->campagne = $campagne;

        return $this;
    }

    public function getDestinataire(): ?Destinataire
    {
        return $this->destinataire;
    }

    public function setDestinataire(?Destinataire $destinataire): self
    {
        $this->destinataire = $destinataire;

        return $this;
    }
}
