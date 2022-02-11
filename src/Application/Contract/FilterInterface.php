<?php

namespace App\Application\Contract;

use Doctrine\ORM\QueryBuilder;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\Request;

interface FilterInterface
{
    public static function getParam(): string;

    public function get(Request $request): mixed;

    public function getValue(Request $request): mixed;

    public function supportsForm(): bool;

    public function buildForm(array $arguments): ?FormInterface;

    public function processQuery(QueryBuilder $qb, array $arguments, Request $request): QueryBuilder;
}