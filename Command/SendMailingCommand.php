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
        $pheanstalk = $this->getContainer()->get('wowo_newsletter.pheanstalk');
        $tube = $this->getContainer()->getParameter('wowo_newsletter.queue.preparation');
        while (1) {
            $rawJob = $pheanstalk->watch($tube)->ignore('default')->reserve();
            $job = json_decode($rawJob->getData(), false);
            $time = new \DateTime("now");
            $output->writeLn(sprintf("<info>[%s]</info> Processing job with contact id <info>%d</info> "
                . " and mailing id <info>%d</info>", $time->format("Y-m-d h:i:s"), $job->contactId, $job->mailingId));
            
            $message = $this->getContainer()->get('wowo_newsletter.newsletter_manager')->sendMailing($job->mailingId,
                $job->contactId, $job->contactClass);
            if ($input->getOption('verbose')) {
                $output->writeLn(sprintf("Sent message:\n%s", $message->toString()));
            }
            $pheanstalk->delete($rawJob);
            usleep(10);
        }
    }
}
