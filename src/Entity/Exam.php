<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
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
    private string $id;

    /**
     * @ORM\Column(type="string", length=255)
     * @Groups({"exam:read"})
     */
    private string $status = ExamStatus::CREATED;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\User", inversedBy="exams")
     * @ORM\JoinColumn(nullable=false)
     * @Groups({"exam:read"})
     */
    private User $user;

    /**
     * @var Collection<Question>
     * @ORM\OneToMany(targetEntity="App\Entity\Question", mappedBy="exam", orphanRemoval=true, cascade={"persist"})
     * @ORM\OrderBy({"position": "ASC"})
     * @Groups({"exam:read"})
     */
    private Collection $questions;

    public function __construct(User $user)
    {
        $this->id = Uuid::uuid4()->toString();
        $this->questions = new ArrayCollection();
        $this->user = $user;
    }

    public function getId(): string
    {
        return $this->id;
    }

    public function getStatus(): string
    {
        return $this->status;
    }

    public function getUser(): User
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

    /**
     * @return Collection|Question[]
     */
    public function getQuestions(): Collection
    {
        return $this->questions;
    }

    public function addQuestion(Question $question): self
    {
        if (!$this->questions->contains($question)) {
            $this->questions[] = $question;
        }

        return $this;
    }

    public function isFinished(): bool
    {
        return ExamStatus::FINISHED === $this->status;
    }

    public function updateStatus(): void
    {
        $done = true;

        /** @var Question $question */
        foreach ($this->questions as $question) {
            if ($question->isCorrect()) {
                continue;
            }

            $done = false;
        }

        $this->status = $done ? ExamStatus::FINISHED : ExamStatus::STARTED;
    }
}
