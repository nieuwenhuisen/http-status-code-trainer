<?php

namespace App\Entity;

use ApiPlatform\Core\Annotation\ApiResource;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;

/**
 * @ORM\Entity(repositoryClass="App\Repository\StatusCodeRepository")
 */
class StatusCode
{
    /**
     * @ORM\Id()
     * @ORM\Column(type="integer")
     * @Groups({"statuscode:read"})
     */
    private $code;

    /**
     * @ORM\Column(type="string", length=255)
     * @Groups({"statuscode:read", "statuscode:write"})
     */
    private $title;

    public function __construct(int $code, string $title)
    {
        $this->code = $code;
        $this->title = $title;
    }

    public function getCode(): int
    {
        return $this->code;
    }

    public function getTitle(): string
    {
        return $this->title;
    }

    public function setTitle(string $title): self
    {
        $this->title = $title;

        return $this;
    }

    /**
     * @Groups({"statuscode:read"})
     */
    public function isCacheable(): bool
    {
        return \in_array($this->code, [200, 203, 300, 301, 302, 404, 410], true);
    }

    /**
     * @Groups({"statuscode:read"})
     */
    public function isRedirect(): bool
    {
        return \in_array($this->code, [201, 301, 302, 303, 307, 308], true);
    }

    /**
     * @Groups({"statuscode:read"})
     */
    public function isEmpty(): bool
    {
        return \in_array($this->code, [204, 304], true);
    }
}
