<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Gedmo\Timestampable\Traits\TimestampableEntity;
use Ramsey\Uuid\Uuid;
use Symfony\Component\Serializer\Annotation\Groups;

/**
 * @ORM\Entity(repositoryClass="App\Repository\ExamRepository")
 */
class Exam
{
    use TimestampableEntity;

    /**
     * @ORM\Id()
     * @ORM\Column(type="string")
     * @Groups({"exam:read"})
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255)
     * @Groups({"exam:read"})
     */
    private $status = ExamStatus::CREATED;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\User", inversedBy="exams")
     * @ORM\JoinColumn(nullable=false)
     */
    private $user;

    public function __construct()
    {
        $this->id = Uuid::uuid4()->toString();
    }

    public function getId(): string
    {
        return $this->id;
    }

    public function getStatus(): string
    {
        return $this->status;
    }

    public function getUser(): ?User
    {
        return $this->user;
    }

    public function setUser(User $user): self
    {
        $this->user = $user;

        return $this;
    }

    /**
     * @Groups({"exam:read"})
     */
    public function getCreatedAt(): \DateTime
    {
        return $this->createdAt;
    }
}
