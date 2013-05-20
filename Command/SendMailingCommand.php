<?php

namespace Wowo\NewsletterBundle\Command;

use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Output\Output;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;

class SendMailingCommand extends ContainerAwareCommand
{
    const DELAY = 100;
    protected function configure()
    {
        $this
            ->setDescription('Fetches jobs from queue, process them and send as an email')
            ->setHelp(<<<EOT
The <info>newsletter:send</info> fetches jobs from queue, replaces placeholders
and sends emails to recipients.

<info>php app/console newsletter:send</info>

EOT
            )
            ->setName('newsletter:send')
        ;
    }

    /**
     * @see Command
     *
     * @throws \InvalidArgumentException When the target directory does not exist
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $verbose = $input->getOption('verbose');
        $logger = function($message) use ($output, $verbose) {
            if ($verbose) {
                $output->writeln($message);
            }
        };

        $spooler = $this->getContainer()->get('wowo_newsletter.spooler');
        $spooler->setLogger($logger);
        while (1) {
            try {
                $spooler->process();
            } catch (\Exception $e) {
                $logger(sprintf('Exception <error>(%s)</error> occured, message: %s',
                    get_class($e), $e->getMessage()));
            }
            usleep(self::DELAY);
        }
    }
}
