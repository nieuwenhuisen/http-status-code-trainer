<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Gedmo\Timestampable\Traits\TimestampableEntity;
use Ramsey\Uuid\Uuid;
use RuntimeException;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Validator\Context\ExecutionContextInterface;

/**
 * @ORM\Entity(repositoryClass="App\Repository\QuestionRepository")
 */
class Question
{
    use TimestampableEntity;

    /**
     * @ORM\Id()
     * @ORM\Column(type="string")
     * @Groups({"exam:read"})
     */
    private $id;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Exam", inversedBy="questions")
     * @ORM\JoinColumn(nullable=false)
     */
    private $exam;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\StatusCode")
     * @ORM\JoinColumn(nullable=false)
     */
    private $statusCode;

    /**
     * @ORM\Column(type="simple_array", nullable=false)
     * @Groups({"exam:read"})
     */
    private $choices;

    /**
     * @ORM\Column(type="integer", nullable=true)
     * @Groups({"question:write"})
     */
    private $answer;

    /**
     * @Groups({"question:read"})
     * @ORM\Column(type="integer", nullable=false)
     */
    private $attempts = 0;

    /**
     * @ORM\Column(type="integer", nullable=false)
     */
    private $position;

    public function __construct(Exam $exam, StatusCode $statusCode, array $choices, int $position = 0)
    {
        $this->id = Uuid::uuid4()->toString();
        $this->exam = $exam;
        $this->statusCode = $statusCode;
        $this->position = $position;
        $this->choices = $choices;
    }

    public function getId(): string
    {
        return $this->id;
    }

    public function getExam(): Exam
    {
        return $this->exam;
    }

    public function getStatusCode(): StatusCode
    {
        return $this->statusCode;
    }

    public function getChoices(): array
    {
        return $this->choices;
    }

    /**
     * @Groups({"exam:read"})
     */
    public function getQuestion(): string
    {
        return $this->statusCode->getTitle();
    }

    public function getAttempts(): int
    {
        return $this->attempts;
    }

    /**
     * @Groups({"exam:read"})
     */
    public function isAnswered(): bool
    {
        return null !== $this->answer;
    }

    /**
     * @Groups({"question:read"})
     */
    public function isCorrect(): bool
    {
        if (!$this->isAnswered()) {
            return false;
        }

        return $this->statusCode->getCode() === $this->answer;
    }

    public function getAnswer(): ?int
    {
        return $this->answer;
    }

    public function setAnswer(int $answer): self
    {
        if ($this->isCorrect()) {
            throw new RuntimeException('Answer already given.');
        }

        ++$this->attempts;
        $this->answer = $answer;

        return $this;
    }

    public function getPosition(): int
    {
        return $this->position;
    }

    /**
     * @Assert\Callback()
     */
    public function validate(ExecutionContextInterface $context): void
    {
        if (in_array((string)$this->answer, $this->choices, true)) {
            return;
        }

        $context->buildViolation(sprintf('Invalid answer %d', $this->answer))
            ->atPath('answer')
            ->addViolation();
    }
}
