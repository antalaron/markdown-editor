<?php

/*
 * This file is part of MarkdownEditor.
 *
 * (c) Antal Áron <antalaron@antalaron.hu>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace App\Command;

use App\ImageManager;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

/**
 * @author Antal Áron <antalaron@antalaron.hu>
 */
class AppGarbageCollectCommand extends Command
{
    /**
     * @var string
     */
    protected static $defaultName = 'app:garbage-collect';

    /**
     * @var ImageManager
     */
    private $imageManager;

    public function __construct(ImageManager $imageManager)
    {
        $this->imageManager = $imageManager;

        parent::__construct();
    }

    protected function configure()
    {
        $this
            ->setDescription('Removes expired images')
            ->addOption('dry-run', null, InputOption::VALUE_NONE, 'Perform dry-run')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $io = new SymfonyStyle($input, $output);

        $dryRun = $input->getOption('dry-run');

        $removedImageCount = $this->imageManager->removeExpiredImages($dryRun);

        if ($dryRun) {
            $io->text('It is just a dry-run! No removal has been executed!');
        }

        $io->success(sprintf('Expired images removed (%s)!', $removedImageCount));
    }
}
