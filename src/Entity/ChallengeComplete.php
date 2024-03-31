<?php

namespace App\Entity;

use App\Repository\ChallengeCompleteRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: ChallengeCompleteRepository::class)]
class ChallengeComplete
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    #[Groups(["challengeComplete", "userChallenge"])]
    private ?int $id = null;
    
    #[ORM\ManyToOne(inversedBy: 'challengeCompletes')]
    #[ORM\JoinColumn(nullable: false)]
    #[Groups(["userChallenge", "review"])]
    #[Assert\NotBlank]
    private ?Challenge $challenge = null;

    #[ORM\Column(nullable: true)]
    #[Groups(["challengeComplete", "userChallenge"])]
    private ?\DateInterval $time = null;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    #[Groups(["challengeComplete", "userChallenge"])]
    private ?string $response = null;

    #[ORM\ManyToOne]
    #[ORM\JoinColumn(nullable: false)]
    #[Groups(["challengeComplete", "userChallenge"])]
    #[Assert\NotBlank]
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

    public function getTime(): ?\DateInterval
    {
        return $this->time;
    }

    public function setTime(?\DateInterval $time): static
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
