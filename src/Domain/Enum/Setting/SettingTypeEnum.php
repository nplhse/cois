<?php

declare(strict_types=1);

namespace Domain\Enum\Setting;

use Domain\Enum\AbstractBasicEnum;

abstract class SettingTypeEnum extends AbstractBasicEnum
{
    public const String = 'string';

    public const Integer = 'integer';

    public const Boolean = 'boolean';
}
