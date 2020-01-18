<?php declare(strict_types=1);

namespace App\Entity;

use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Validator\Constraints as Assert;

final class MultiFactorAuthenticationVerify
{
    /**
     * @var string
     * @Assert\NotBlank()
     * @Groups({"write"})
     */
    public $code;
}
