<?php

namespace App\Entity;

use App\Repository\ChallengeRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Symfony\Component\Serializer\Annotation\Groups;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: ChallengeRepository::class)]
class Challenge
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    /**
     * @Groups({"challenge"})
     */
    private ?int $id = null;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    /**
     * @Groups({"challenge"})
     */
    private ?string $description = null;

    #[ORM\Column(nullable: true)]
    /**
     * @Groups({"challenge"})
     */
    private ?int $level = null;

    #[ORM\Column(length: 4)]
    /**
     * @Groups({"challenge"})
     */
    private ?string $status = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE)]
    /**
     * @Groups({"challenge"})
     */
    private ?\DateTimeInterface $createdAt = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE)]
    /**
     * @Groups({"challenge"})
     */
    private ?\DateTimeInterface $updatedAt = null;

    #[ORM\OneToMany(mappedBy: 'challenge', targetEntity: ChallengeComplete::class)]
    /**
     * @Groups({"challenge"})
     */
    private Collection $challengeCompletes;

    public function __construct()
    {
        $this->challengeCompletes = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(?string $description): static
    {
        $this->description = $description;

        return $this;
    }

    public function getLevel(): ?int
    {
        return $this->level;
    }

    public function setLevel(?int $level): static
    {
        $this->level = $level;

        return $this;
    }

    public function getStatus(): ?string
    {
        return $this->status;
    }

    public function setStatus(string $status): static
    {
        $this->status = $status;

        return $this;
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
            $challengeComplete->setChallenge($this);
        }

        return $this;
    }

    public function removeChallengeComplete(ChallengeComplete $challengeComplete): static
    {
        if ($this->challengeCompletes->removeElement($challengeComplete)) {
            // set the owning side to null (unless already changed)
            if ($challengeComplete->getChallenge() === $this) {
                $challengeComplete->setChallenge(null);
            }
        }

        return $this;
    }
}
