<?php

declare(strict_types=1);

namespace App\Dto;

use App\Entity\Comment;
use App\Validator\Constraints\EntityExist;
use Symfony\Component\Validator\Constraints as Assert;

class CommentCreate
{
    #[
        Assert\Type('string'),
        Assert\NotBlank,
        Assert\Length(max: 500),
    ]
    public $content;

    #[
        Assert\Type('string'),
        Assert\NotBlank,
        Assert\Length(max: 50),
    ]
    public $groupKey;

    #[
        Assert\Type('string'),
        EntityExist(Comment::class)
    ]
    public $parentId;
}
