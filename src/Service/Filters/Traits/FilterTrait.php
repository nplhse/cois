<?php

namespace App\Service\Filters\Traits;

use App\Application\Exception\FilterMissingArgumentException;
use Symfony\Component\HttpFoundation\Request;

trait FilterTrait
{
    private mixed $cacheValue;

    public static function getParam(): string
    {
        return self::Param;
    }

    public function get(Request $request): mixed
    {
        if (isset($this->cacheValue)) {
            return $this->getCacheValue();
        }

        return $this->getValue($request);
    }

    private function setCacheValue(mixed $value): mixed
    {
        $this->cacheValue = $value;

        return $value;
    }

    private function getCacheValue(): mixed
    {
        return $this->cacheValue;
    }

    private function checkArguments(array $arguments, array $expected): void
    {
        foreach ($expected as $key) {
            if (!array_key_exists($key, $arguments)) {
                throw new FilterMissingArgumentException(sprintf('Filter expected argument %s which is missing', strtoupper($key)));
            }
        }
    }
}
