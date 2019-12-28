<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Gedmo\Timestampable\Traits\TimestampableEntity;
use Ramsey\Uuid\Uuid;
use Symfony\Component\Serializer\Annotation\Groups;

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
     * @ORM\Column(type="integer", nullable=true)
     */
    private $answer;

    /**
     * @ORM\Column(type="integer", nullable=false)
     */
    private $position;

    private function __construct(Exam $exam, StatusCode $statusCode, int $position = 0)
    {
        $this->id = Uuid::uuid4()->toString();
        $this->exam = $exam;
        $this->statusCode = $statusCode;
        $this->position = $position;
    }

    public static function fromExamAndStatusCodeAndPosition(Exam $exam, StatusCode $statusCode, int $position): self
    {
        return new self($exam, $statusCode, $position);
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

    /**
     * @Groups({"exam:read"})
     */
    public function getQuestion(): string
    {
        return $this->statusCode->getTitle();
    }

    public function getAnswer(): ?int
    {
        return $this->answer;
    }

    public function setAnswer(?int $answer): self
    {
        $this->answer = $answer;

        return $this;
    }

    public function getPosition(): int
    {
        return $this->position;
    }
}
