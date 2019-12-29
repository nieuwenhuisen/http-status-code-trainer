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

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Question", mappedBy="exam", orphanRemoval=true, cascade={"persist"})
     * @Groups({"exam:read"})
     * @ORM\OrderBy({"position": "ASC"})
     */
    private $questions;

    public function __construct()
    {
        $this->id = Uuid::uuid4()->toString();
        $this->questions = new ArrayCollection();
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

    public function updateStatus(): bool
    {
        if (ExamStatus::FINISHED === $this->status) {
            return false;
        }

        $started = false;
        $done = true;

        /** @var Question $question */
        foreach ($this->questions as $question) {
            if (!$started && $question->isAnswered()) {
                $started = true;
            }

            if ($done && !$question->isCorrect()) {
                $done = false;
            }
        }

        if ($done) {
            $this->status = ExamStatus::FINISHED;

            return true;
        }

        if (ExamStatus::CREATED === $this->status && $started) {
            $this->status = ExamStatus::STARTED;

            return true;
        }

        return false;
    }
}
