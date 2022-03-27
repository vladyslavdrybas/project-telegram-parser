<?php

declare(strict_types=1);

namespace App\Services\EvilLinks;


interface EvilLinksGrabberInterface
{
    public function execute(): array;
}
