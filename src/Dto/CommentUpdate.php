<?php

declare(strict_types=1);

namespace App\Dto;

use Symfony\Component\Validator\Constraints as Assert;

class CommentUpdate
{
    #[
        Assert\Type('numeric'),
        Assert\GreaterThanOrEqual(0),
        Assert\LessThanOrEqual(5),
    ]
    public $rate;
}
