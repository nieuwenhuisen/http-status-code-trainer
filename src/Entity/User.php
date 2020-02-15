<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Timestampable\Traits\TimestampableEntity;
use Lexik\Bundle\JWTAuthenticationBundle\Security\User\JWTUserInterface;
use Symfony\Component\Security\Core\User\EquatableInterface;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Serializer\Annotation\SerializedName;

/**
 * @ORM\Entity(repositoryClass="App\Repository\UserRepository")
 */
class User implements UserInterface, JWTUserInterface, EquatableInterface
{
    use TimestampableEntity;

    public const MFA_ENABLED_KEY = 'mfa_enabled';
    public const MFA_VERIFIED_KEY = 'mfa_verified';

    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     * @Groups({"user:read"})
     */
    private ?int $id = null;

    /**
     * @ORM\Column(type="string", length=180, unique=true)
     * @Groups({"user:read", "user:write"})
     */
    private string $email;

    /**
     * @var array<string>
     * @ORM\Column(type="json")
     */
    private array $roles;

    /**
     * @ORM\Column(type="string")
     */
    private string $password;

    /**
     * @SerializedName("password")
     * @Groups("user:write")
     */
    private string $plainPassword = '';

    /**
     * @var Collection<Exam>
     * @ORM\OneToMany(targetEntity="App\Entity\Exam", mappedBy="user", orphanRemoval=true)
     */
    private Collection $exams;

    /**
     * @var string|null
     * @ORM\Column(type="string", nullable=true, unique=true)
     */
    private ?string $mfaKey = null;

    /**
     * @param array<string> $roles
     */
    public function __construct(string $email, array $roles = [])
    {
        $this->email = $email;
        $this->roles = $roles;
        $this->exams = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getEmail(): string
    {
        return $this->email;
    }

    public function setEmail(string $email): self
    {
        $this->email = $email;

        return $this;
    }

    public function getUsername(): string
    {
        return $this->email;
    }

    public function getRoles(): array
    {
        $roles = $this->roles;
        $roles[] = 'ROLE_USER';

        return array_unique($roles);
    }

    public function getPassword(): string
    {
        return $this->password;
    }

    public function setPassword(string $password): self
    {
        $this->password = $password;

        return $this;
    }

    public function getPlainPassword(): string
    {
        return $this->plainPassword;
    }

    public function setPlainPassword(string $plainPassword): self
    {
        $this->plainPassword = $plainPassword;

        return $this;
    }

    public function getSalt(): ?string
    {
        return null;
    }

    public function eraseCredentials(): void
    {
        $this->plainPassword = '';
    }

    /**
     * @param string               $username
     * @param array<array<string>> $payload
     */
    public static function createFromPayload($username, array $payload): self
    {
        return new self($username, $payload['roles']);
    }

    /**
     * @return Collection|Exam[]
     */
    public function getExams(): Collection
    {
        return $this->exams;
    }

    public function addExam(Exam $exam): self
    {
        if (!$this->exams->contains($exam)) {
            $this->exams[] = $exam;
            $exam->setUser($this);
        }

        return $this;
    }

    public function removeExam(Exam $exam): self
    {
        if ($this->exams->contains($exam)) {
            $this->exams->removeElement($exam);
        }

        return $this;
    }

    public function isMfaEnabled(): bool
    {
        return null !== $this->mfaKey;
    }

    public function getMfaKey(): ?string
    {
        return $this->mfaKey;
    }

    public function setMfaKey(?string $mfaKey): self
    {
        $this->mfaKey = $mfaKey;

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function isEqualTo(UserInterface $user): bool
    {
        return $user->getUsername() === $this->email;
    }
}
