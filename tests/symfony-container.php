<?php

declare(strict_types=1);

$appKernel = new \App\Kernel('tests', false);
$appKernel->boot();

return $appKernel->getContainer();
