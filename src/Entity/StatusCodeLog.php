<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\StatusCodeLogRepository")
 */
class StatusCodeLog
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\User")
     * @ORM\JoinColumn(nullable=false)
     */
    private $user;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\StatusCode")
     * @ORM\JoinColumn(nullable=false)
     */
    private $statusCode;

    /**
     * @ORM\Column(type="integer")
     */
    private $correct = 0;

    /**
     * @ORM\Column(type="integer")
     */
    private $fails = 0;

    public function __construct(User $user, StatusCode $statusCode)
    {
        $this->user = $user;
        $this->statusCode = $statusCode;
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getUser(): ?User
    {
        return $this->user;
    }

    public function setUser(?User $user): self
    {
        $this->user = $user;

        return $this;
    }

    public function getStatusCode(): ?StatusCode
    {
        return $this->statusCode;
    }

    public function setStatusCode(?StatusCode $statusCode): self
    {
        $this->statusCode = $statusCode;

        return $this;
    }

    public function getCorrect(): int
    {
        return $this->correct;
    }

    public function getFails(): int
    {
        return $this->fails;
    }

    public function addCorrect(): void
    {
        ++$this->correct;
    }

    public function addFail(): void
    {
        ++$this->fails;
    }
}
