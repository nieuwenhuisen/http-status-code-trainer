<?php declare(strict_types=1);

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Gesdinet\JWTRefreshTokenBundle\Entity\AbstractRefreshToken;

/**
 * This class override Gesdinet\JWTRefreshTokenBundle\Entity\RefreshToken to have another table name.
 * @ORM\Entity
 * @ORM\Table("jwt_refresh_token")
 */
final class JwtRefreshToken extends AbstractRefreshToken
{
    /**
     * @var int
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;

    /**
     * @var bool
     *
     * @ORM\Column(name="mfa_verifed", type="boolean")
     */
    protected $mfaVerifed = false;

    /**
     * {@inheritdoc}
     */
    public function getId()
    {
        return $this->id;
    }

    public function isMfaVerified(): bool
    {
        return $this->mfaVerifed;
    }

    public function setMfaVerified(): self
    {
        $this->mfaVerifed = true;

        return $this;
    }
}
