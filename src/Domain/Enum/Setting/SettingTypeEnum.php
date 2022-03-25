<?php

namespace App\Domain\Enum\Setting;

use App\Domain\Enum\AbstractBasicEnum;

abstract class SettingTypeEnum extends AbstractBasicEnum
{
    public const String = 'string';

    public const Integer = 'integer';

    public const Boolean = 'boolean';
}
