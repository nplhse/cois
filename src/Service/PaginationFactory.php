<?php

namespace App\Service;

use App\DataTransferObjects\PaginationDto;

class PaginationFactory
{
    public static function create(int $page, int $count, int $perPage): PaginationDto
    {
        $last = self::getLast($count, $perPage);
        $page = self::getPage($page);

        if ($page > 1) {
            $previous = $page - 1;
        } else {
            $previous = null;
        }

        if ($page < $last) {
            $next = $page + 1;
        } else {
            $next = null;
        }

        return new PaginationDto($page, $perPage, $previous, $next, $last);
    }

    private static function getPage(int $page): int
    {
        if ($page > 0) {
            return $page;
        }

        return 1;
    }

    private static function getLast(int $count, int $perPage): int
    {
        $last = (int) ceil($count / $perPage);

        if (0 === $count % 10) {
            return --$last;
        }

        if (0 === $last) {
            return 1;
        }

        return $last;
    }
}
