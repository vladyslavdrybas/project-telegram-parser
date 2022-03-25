<?php

declare(strict_types=1);

namespace App\Services;


interface EvilLinksGrabberInterface
{
    public function execute(): array;
}
