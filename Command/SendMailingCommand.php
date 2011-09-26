<?php

namespace Wowo\Bundle\NewsletterBundle\Command;

use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Output\Output;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;

class SendMailingCommand extends ContainerAwareCommand
{
    protected function configure()
    {
        $this
            ->addOption('delay', null, InputOption::VALUE_REQUIRED, 'Infinite loop delay (ms)', 10)
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
        $logger = function($message) use ($output)
        {
            $output->writeln($message);
        };

        while (1) {
            try {
                $this->getContainer()->get('wowo_newsletter.newsletter_manager')->getJobFromQueueAndSendMailing($logger, $input->getOption('verbose'));
            } catch (\Swift_SwiftException $e) {
                $logger(sprintf('<error>Mailer exception (%s) occured</error> with message: <error>%s</error>', get_class($e), $e->getMessage()));
            } catch (\Exception $e) {
                $logger(sprintf('<error>Unknown exception (%s) occured</error> with message: <error>%s</error>', get_class($e), $e->getMessage()));
            }
            usleep($input->getOption('delay') * 1000);
        }
    }
}
