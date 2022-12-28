<?php

declare(strict_types=1);

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Ramsey\Uuid\Uuid;
use Symfony\Component\Security\Core\User\UserInterface;

class Account implements UserInterface
{
    private string $id;
    private string $username;
    private string $email;

    /**
     * @var string[]
     */
    private array $roles = ['ROLE_USER'];
    private \DateTimeImmutable $createdAt;
    private ?string $googleId = null;
    private ?string $facebookId = null;
    private ?string $avatar = null;
    private ?string $hostedDomain = null;

    /**
     * @var Collection<int, Rate>
     */
    private Collection $rates;

    /**
     * @var Collection<int, Comment>
     */
    private Collection $comments;

    public function __construct(string $username, string $email)
    {
        $this->id = Uuid::uuid4()->toString();
        $this->setUsername($username);
        $this->setEmail($email);
        $this->createdAt = new \DateTimeImmutable();
        $this->rates = new ArrayCollection();
        $this->comments = new ArrayCollection();
    }

    public function getId(): string
    {
        return $this->id;
    }

    public function getUsername(): string
    {
        return $this->username;
    }

    public function setUsername(string $username): void
    {
        $this->username = $username;
    }

    public function getEmail(): string
    {
        return $this->email;
    }

    public function setEmail(string $email): void
    {
        $this->email = strtolower($email);
    }

    /**
     * @return string[]
     */
    public function getRoles(): array
    {
        return $this->roles;
    }

    /**
     * @param string[] $roles
     */
    public function setRoles(array $roles): void
    {
        $this->roles = $roles;
    }

    public function getCreatedAt(): \DateTimeInterface
    {
        return $this->createdAt;
    }

    public function getGoogleId(): ?string
    {
        return $this->googleId;
    }

    public function setGoogleId(?string $googleId): void
    {
        $this->googleId = $googleId;
    }

    public function getFacebookId(): ?string
    {
        return $this->facebookId;
    }

    public function setFacebookId(?string $facebookId): void
    {
        $this->facebookId = $facebookId;
    }

    public function getAvatar(): ?string
    {
        return $this->avatar;
    }

    public function setAvatar(?string $avatar): void
    {
        $this->avatar = $avatar;
    }

    public function getHostedDomain(): ?string
    {
        return $this->hostedDomain;
    }

    public function setHostedDomain(?string $hostedDomain): void
    {
        $this->hostedDomain = $hostedDomain;
    }

    /**
     * @return iterable<Rate>
     */
    public function getRates(): iterable
    {
        return $this->rates;
    }

    public function eraseCredentials(): void
    {
    }

    public function getUserIdentifier(): string
    {
        return $this->email;
    }

    /**
     * @return iterable<Comment>
     */
    public function getComments(): iterable
    {
        return $this->comments;
    }
}
