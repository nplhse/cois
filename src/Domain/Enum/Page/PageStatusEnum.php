<?php

declare(strict_types=1);

namespace Domain\Enum\Page;

use Domain\Enum\AbstractBasicEnum;

abstract class PageStatusEnum extends AbstractBasicEnum
{
    public const Draft = 'draft';

    public const Published = 'published';

    public const Deleted = 'deleted';
}
