<?php

declare(strict_types=1);

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Ramsey\Uuid\Uuid;

class Comment
{
    private string $id;
    private \DateTimeImmutable $createdAt;
    private string $groupKey;
    private string $content;
    private Account $author;
    private ?float $rate = null;
    private ?Comment $parent = null;

    /**
     * @var Collection<int, Comment>
     */
    private Collection $children;

    /**
     * @var Collection<int, Rate>
     */
    private Collection $rates;

    public function __construct(?string $id, string $groupKey, string $content, Account $author, ?self $parent = null)
    {
        $this->id = $id ?? Uuid::uuid4()->toString();
        $this->createdAt = new \DateTimeImmutable();
        $this->groupKey = $groupKey;
        $this->content = $content;
        $this->author = $author;
        $this->parent = $parent;
        $this->children = new ArrayCollection();
        $this->rates = new ArrayCollection();
    }

    public function getAuthor(): Account
    {
        return $this->author;
    }

    public function getParent(): ?self
    {
        return $this->parent;
    }

    public function getAuthorAvatar(): ?string
    {
        return $this->author->getAvatar();
    }

    public function getAuthorUsername(): string
    {
        return $this->author->getUsername();
    }

    public function getId(): string
    {
        return $this->id;
    }

    public function getCreatedAt(): \DateTimeInterface
    {
        return $this->createdAt;
    }

    public function getGroupKey(): string
    {
        return $this->groupKey;
    }

    public function getContent(): string
    {
        return $this->content;
    }

    public function setContent(string $content): void
    {
        $this->content = $content;
    }

    /**
     * @return iterable<Comment>
     */
    public function getChildren(): iterable
    {
        return $this->children;
    }

    public function addChild(self $comment): void
    {
        $this->children->add($comment);
    }

    public function removeChild(self $comment): void
    {
        $this->children->removeElement($comment);
    }

    public function getRate(): ?float
    {
        return $this->rate;
    }

    public function addRate(Account $account, float $value): void
    {
        $this->rates[] = new Rate($account, $this, $value);
        if (null === $this->rate) {
            $this->rate = $value;
        } else {
            $nbRate = \count($this->rates) - 1;
            $this->rate = (($nbRate * $this->rate) + $value) / ($nbRate + 1);
        }
    }

    public function rateUpdated(float $newValue, float $oldValue): void
    {
        $nbRate = \count($this->rates);
        $this->rate = (($nbRate * (float) $this->rate) + $newValue - $oldValue) / $nbRate;
    }
}
