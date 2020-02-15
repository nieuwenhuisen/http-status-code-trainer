<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Gedmo\Timestampable\Traits\TimestampableEntity;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Serializer\Annotation\SerializedName;

/**
 * @ORM\Entity(repositoryClass="App\Repository\StatusCodeRepository")
 */
class StatusCode
{
    use TimestampableEntity;

    /**
     * @ORM\Id()
     * @ORM\Column(type="integer")
     * @SerializedName("code")
     * @Groups({"statuscode:get", "statuscode:post"})
     */
    private int $id = 0;

    /**
     * @ORM\Column(type="string", length=255)
     * @Groups({"statuscode:get", "statuscode:post"})
     */
    private string $title = '';

    public function __construct(int $id, string $title)
    {
        $this->id = $id;
        $this->title = $title;
    }

    public function getId(): int
    {
        return $this->id;
    }

    public function getCode(): int
    {
        return $this->id;
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
        return \in_array($this->id, [200, 203, 300, 301, 302, 404, 410], true);
    }

    /**
     * @Groups({"statuscode:read"})
     */
    public function isRedirect(): bool
    {
        return \in_array($this->id, [201, 301, 302, 303, 307, 308], true);
    }

    /**
     * @Groups({"statuscode:read"})
     */
    public function isEmpty(): bool
    {
        return \in_array($this->id, [204, 304], true);
    }
}
