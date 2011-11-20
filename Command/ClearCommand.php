<?php

namespace Wowo\Bundle\NewsletterBundle\Command;

use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Output\Output;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;

class ClearCommand extends ContainerAwareCommand
{
    const DELAY = 100;
    protected function configure()
    {
        $this
            ->setDescription('Clears all queues')
            ->setHelp(<<<EOT
This is only helper for debugging purposes, you shouldn't use it in the production.
It will erase all your queues
EOT
            )
            ->setName('newsletter:clear')
        ;
    }

    /**
     * @see Command
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $output->writeln('<info>Clearing all queues</info>');
        $i = 1;
        while (1) {
            try {
                $this->getContainer()->get('wowo_newsletter.newsletter_manager')->clearQueues();
                $output->writeln(sprintf(' Removed <info>%d</info> job', $i++));
            } catch (\Exception $e) {
                $output->writeln(sprintf('<error>Unknown exception (%s) occured, message: %s</error>',
                    get_class($e), $e->getMessage()));
            }
        }
    }
}

