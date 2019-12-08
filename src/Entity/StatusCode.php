<?php

namespace App\Entity;

use ApiPlatform\Core\Annotation\ApiResource;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ApiResource()
 * @ORM\Entity(repositoryClass="App\Repository\StatusCodeRepository")
 */
class StatusCode
{
    /**
     * @ORM\Id()
     * @ORM\Column(type="integer")
     */
    private $code;

    /**
     * @ORM\Column(type="string", length=255)
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

    public function isCacheable(): bool
    {
        return \in_array($this->code, [200, 203, 300, 301, 302, 404, 410], true);
    }

    public function isRedirect(): bool
    {
        return \in_array($this->code, [201, 301, 302, 303, 307, 308], true);
    }

    public function isEmpty(): bool
    {
        return \in_array($this->code, [204, 304], true);
    }
}
