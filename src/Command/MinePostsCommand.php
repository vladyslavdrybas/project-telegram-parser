<?php

declare(strict_types=1);

namespace App\Command;

use App\Services\MinePosts;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class MinePostsCommand extends Command
{
    protected const COMMAND_NAME = 'app:telegram:miner:posts:mine';
    protected const COMMAND_DESCRIPTION = self::class;

    protected MinePosts $service;

    public function __construct(MinePosts $service)
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
        $this ->addArgument(
            'minerTitle',
            InputArgument::REQUIRED,
            'run miner by title'
        );
    }

    /**
     * @param \Symfony\Component\Console\Input\InputInterface $input
     * @param \Symfony\Component\Console\Output\OutputInterface $output
     * @return int
     */
    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        return (int) $this->service->execute($input->getArgument('minerTitle'));
    }
}