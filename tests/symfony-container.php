<?php

$appKernel = new \App\Kernel('tests', false);
$appKernel->boot();

return $appKernel->getContainer();
