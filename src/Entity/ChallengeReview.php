<?php

namespace App\Entity;

use App\Repository\ChallengeReviewRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: ChallengeReviewRepository::class)]
class ChallengeReview
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    #[Groups(["review"])]
    private ?int $id = null;

    #[ORM\Column(length: 255, nullable: true)]
    #[Groups(["review"])]
    #[Assert\NotBlank]
    #[Assert\Type('string')]
    private ?string $comment = null;

    #[ORM\Column]
    #[Groups(["review"])]
    #[Assert\Type('boolean')]
    private ?bool $needRevaluation = null;

    #[ORM\OneToOne(inversedBy: 'challengeReview', cascade: ['persist', 'remove'])]
    #[ORM\JoinColumn(nullable: false)]
    #[Groups(["review"])]
    #[Assert\NotBlank]
    private ?ChallengeComplete $challengeComplete = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE)]
    #[Groups(["review"])]
    private ?\DateTimeInterface $createdAt = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE)]
    #[Groups(["review"])]
    private ?\DateTimeInterface $updatedAt = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getComment(): ?string
    {
        return $this->comment;
    }

    public function setComment(?string $comment): static
    {
        $this->comment = $comment;

        return $this;
    }

    public function isNeedRevaluation(): ?bool
    {
        return $this->needRevaluation;
    }

    public function setNeedRevaluation(bool $needRevaluation): static
    {
        $this->needRevaluation = $needRevaluation;

        return $this;
    }

    public function getChallengeComplete(): ?ChallengeComplete
    {
        return $this->challengeComplete;
    }

    public function setChallengeComplete(ChallengeComplete $challengeComplete): static
    {
        $this->challengeComplete = $challengeComplete;

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
}
