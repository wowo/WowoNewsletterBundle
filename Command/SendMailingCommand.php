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
            ->addOption('jobs', null, InputOption::VALUE_OPTIONAL, 'Number of jobs to process at one time', 50)
            ->setDescription('Fetches jobs from queue, process them and send as an email')
            ->setHelp(<<<EOT
The <info>newsletter:send</info> fetches jobs from queue, replaces placeholders 
and sends emails to recipients.

<info>php app/console newsletter:send [--jobs]</info>

To determine jobs count use 
<info>--jobs</info> option (by default: 50).

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
        if ((int)$input->getOption('jobs') <= 0) {
            throw new \InvalidArgumentException('<info>Jobs</info> parameter need to be greater than 0');
        }
        $pheanstalk = $this->getContainer()->get('wowo_newsletter.pheanstalk');
        $tube = $this->getContainer()->getParameter('wowo_newsletter.queue.preparation');
        for ($i = 0; $i < $input->getOption('jobs'); $i++) {
            $job = $pheanstalk->watch($tube)->ignore('default')->reserve();
            $job = json_decode($job->getData(), false);
            var_dump($job);
        }
    }
}
