<?php

declare(strict_types=1);

namespace App\Command;

use App\Services\ChannelGrabber;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class ChannelGrabberCommand extends Command
{
    protected const COMMAND_NAME = 'app:telegram:channel:grabber:run';
    protected const COMMAND_DESCRIPTION = self::class;

    protected ChannelGrabber $service;

    public function __construct(ChannelGrabber $service)
    {
        parent::__construct(static::COMMAND_NAME);
        $this->service = $service;
    }

    /**
     * @return void
     */
    protected function configure(): void
    {
        $this->setDescription(static::COMMAND_DESCRIPTION);
    }

    /**
     * @param \Symfony\Component\Console\Input\InputInterface $input
     * @param \Symfony\Component\Console\Output\OutputInterface $output
     * @return int
     */
    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        return (int) $this->service->execute();
    }
}
