<?php

declare(strict_types=1);

namespace Domain\Enum\Page;

use Domain\Enum\AbstractBasicEnum;

abstract class PageTypeEnum extends AbstractBasicEnum
{
    public const Public = 'public';

    public const Private = 'private';

    public const ImprintPage = 'imprint';

    public const PrivacyPage = 'privacy';

    public const TermsPage = 'terms';
}
