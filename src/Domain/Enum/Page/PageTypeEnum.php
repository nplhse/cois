<?php

namespace App\Domain\Enum\Page;

use App\Domain\Enum\AbstractBasicEnum;

abstract class PageTypeEnum extends AbstractBasicEnum
{
    public const Public = 'public';

    public const Private = 'private';

    public const ImprintPage = 'imprint';

    public const PrivacyPage = 'privacy';
}
