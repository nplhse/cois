<?php

namespace App\Domain\Enum\Page;

use App\Domain\Enum\AbstractBasicEnum;

abstract class PageStatusEnum extends AbstractBasicEnum
{
    public const Draft = 'draft';

    public const Published = 'published';

    public const Deleted = 'deleted';
}
