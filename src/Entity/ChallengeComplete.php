<?php

namespace App\Entity;

use App\Repository\ChallengeCompleteRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: ChallengeCompleteRepository::class)]
class ChallengeComplete
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\ManyToOne(inversedBy: 'challengeCompletes')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Challenge $challenge = null;

    #[ORM\Column(type: Types::TIME_MUTABLE, nullable: true)]
    private ?\DateTimeInterface $time = null;

    #[ORM\Column(type: Types::TEXT)]
    private ?string $response = null;

    #[ORM\ManyToOne]
    #[ORM\JoinColumn(nullable: false)]
    private ?Technologie $technologie = null;

    #[ORM\ManyToOne(inversedBy: 'challengeCompletes')]
    #[ORM\JoinColumn(nullable: false)]
    private ?User $user = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE)]
    private ?\DateTimeInterface $createdAt = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE)]
    private ?\DateTimeInterface $updatedAt = null;

    #[ORM\OneToOne(mappedBy: 'challengeComplete', cascade: ['persist', 'remove'])]
    private ?ChallengeReview $challengeReview = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getChallenge(): ?Challenge
    {
        return $this->challenge;
    }

    public function setChallenge(?Challenge $challenge): static
    {
        $this->challenge = $challenge;

        return $this;
    }

    public function getTime(): ?\DateTimeInterface
    {
        return $this->time;
    }

    public function setTime(?\DateTimeInterface $time): static
    {
        $this->time = $time;

        return $this;
    }

    public function getResponse(): ?string
    {
        return $this->response;
    }

    public function setResponse(string $response): static
    {
        $this->response = $response;

        return $this;
    }

    public function getTechnologie(): ?Technologie
    {
        return $this->technologie;
    }

    public function setTechnologie(?Technologie $technologie): static
    {
        $this->technologie = $technologie;

        return $this;
    }

    public function getUser(): ?User
    {
        return $this->user;
    }

    public function setUser(?User $user): static
    {
        $this->user = $user;

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

    public function getChallengeReview(): ?ChallengeReview
    {
        return $this->challengeReview;
    }

    public function setChallengeReview(ChallengeReview $challengeReview): static
    {
        // set the owning side of the relation if necessary
        if ($challengeReview->getChallengeComplete() !== $this) {
            $challengeReview->setChallengeComplete($this);
        }

        $this->challengeReview = $challengeReview;

        return $this;
    }
}
