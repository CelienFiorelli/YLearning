<?php

namespace App\Entity;

use App\Repository\UserRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Core\User\UserInterface;

#[ORM\Entity(repositoryClass: UserRepository::class)]
class User implements UserInterface, PasswordAuthenticatedUserInterface
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 180, unique: true)]
    private ?string $username = null;

    #[ORM\Column]
    private array $roles = [];

    /**
     * @var string The hashed password
     */
    #[ORM\Column]
    private ?string $password = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE)]
    private ?\DateTimeInterface $createdAt = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE)]
    private ?\DateTimeInterface $updatedAt = null;

    #[ORM\ManyToOne(inversedBy: 'user')]
    private ?Persona $persona = null;

    #[ORM\OneToMany(mappedBy: 'user', targetEntity: Ability::class)]
    private Collection $abilities;

    #[ORM\OneToMany(mappedBy: 'user', targetEntity: ChallengeComplete::class)]
    private Collection $challengeCompletes;

    public function __construct()
    {
        $this->abilities = new ArrayCollection();
        $this->challengeCompletes = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getUsername(): ?string
    {
        return $this->username;
    }

    public function setUsername(string $username): static
    {
        $this->username = $username;

        return $this;
    }

    /**
     * A visual identifier that represents this user.
     *
     * @see UserInterface
     */
    public function getUserIdentifier(): string
    {
        return (string) $this->username;
    }

    /**
     * @see UserInterface
     */
    public function getRoles(): array
    {
        $roles = $this->roles;
        // guarantee every user at least has ROLE_USER
        $roles[] = 'ROLE_USER';

        return array_unique($roles);
    }

    public function setRoles(array $roles): static
    {
        $this->roles = $roles;

        return $this;
    }

    /**
     * @see PasswordAuthenticatedUserInterface
     */
    public function getPassword(): string
    {
        return $this->password;
    }

    public function setPassword(string $password): static
    {
        $this->password = $password;

        return $this;
    }

    /**
     * @see UserInterface
     */
    public function eraseCredentials(): void
    {
        // If you store any temporary, sensitive data on the user, clear it here
        // $this->plainPassword = null;
    }

    public function getCreatedAt(): ?\DateTimeInterface
    {
        return $this->createdAt;
    }

    public function setCreatedAt(\DateTimeInterface $createdAt): static
    {
        $this->createdAt = $createdAt;

        return $this;
    }

    public function getUpdatedAt(): ?\DateTimeInterface
    {
        return $this->updatedAt;
    }

    public function setUpdatedAt(\DateTimeInterface $updatedAt): static
    {
        $this->updatedAt = $updatedAt;

        return $this;
    }

    public function getPersona(): ?Persona
    {
        return $this->persona;
    }

    public function setPersona(?Persona $persona): static
    {
        $this->persona = $persona;

        return $this;
    }

    /**
     * @return Collection<int, Ability>
     */
    public function getAbilities(): Collection
    {
        return $this->abilities;
    }

    public function addAbility(Ability $ability): static
    {
        if (!$this->abilities->contains($ability)) {
            $this->abilities->add($ability);
            $ability->setUser($this);
        }

        return $this;
    }

    public function removeAbility(Ability $ability): static
    {
        if ($this->abilities->removeElement($ability)) {
            // set the owning side to null (unless already changed)
            if ($ability->getUser() === $this) {
                $ability->setUser(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, ChallengeComplete>
     */
    public function getChallengeCompletes(): Collection
    {
        return $this->challengeCompletes;
    }

    public function addChallengeComplete(ChallengeComplete $challengeComplete): static
    {
        if (!$this->challengeCompletes->contains($challengeComplete)) {
            $this->challengeCompletes->add($challengeComplete);
            $challengeComplete->setUser($this);
        }

        return $this;
    }

    public function removeChallengeComplete(ChallengeComplete $challengeComplete): static
    {
        if ($this->challengeCompletes->removeElement($challengeComplete)) {
            // set the owning side to null (unless already changed)
            if ($challengeComplete->getUser() === $this) {
                $challengeComplete->setUser(null);
            }
        }

        return $this;
    }
}
