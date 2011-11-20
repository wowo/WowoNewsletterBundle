<?php

namespace Wowo\Bundle\NewsletterBundle\Newsletter;

use Doctrine\ORM\EntityManager;
use Wowo\Bundle\NewsletterBundle\Entity\Mailing;
use Wowo\Bundle\NewsletterBundle\Newsletter\Placeholders\Exception\InvalidPlaceholderMappingException;
use Wowo\Bundle\NewsletterBundle\Newsletter\Model\Exception\MailingNotFoundException;
use Wowo\Bundle\NewsletterBundle\Newsletter\Model\Exception\ContactNotFoundException;
use Wowo\Bundle\NewsletterBundle\Newsletter\Placeholders\PlaceholderProcessorInterface;
use Wowo\Bundle\QueueBundle\QueueManager;
use Wowo\Bundle\NewsletterBundle\Newsletter\BuilderInterface;

class NewsletterManager implements NewsletterManagerInterface
{
    protected $contactClass;
    protected $mailer;
    protected $queue;
    protected $builder;

    public function __construct()
    {
    }

    public function setBuilder(BuilderInterface $builder)
    {
        $this->builder = $builder;
    }

    public function setQueue(QueueManager $queue)
    {
        $this->queue = $queue;
    }

    public function setMailer(\Swift_Mailer $mailer)
    {
        $this->mailer = $mailer;
    }

    public function setContactClass($contactClass)
    {
        if (!class_exists($contactClass)) {
            throw new \InvalidArgumentException(sprintf('Class %s doesn\'t exist!', $contactClass));
        }
        $this->contactClass = $contactClass;
    }

    public function validateDependencies()
    {
        $dependencies = array(
            'mailer' => $this->mailer,
            'contactClass' => $this->contactClass,
        );
        foreach ($dependencies as $name => $dependency) {
            if (null == $dependency) {
                throw new \RuntimeException(sprintf('Dependency "%s" is not set or set to null', $name));
            }
        }
    }

    public function putMailingInQueue(Mailing $mailing, array $contactIds)
    {
        if (count($contactIds) == 0) {
            throw new \InvalidArgumentException('No contacs selected, it need to be at least '
                . 'one contact to send mailing');
        }
        $interval = $mailing->isDelayedMailing()
            ? $this->convertDataIntervalToSeconds($mailing->getSendDate()->diff(new \DateTime("now")))
            : null;
        foreach($contactIds as $contactId) {
              $job = new \StdClass();
              $job->contactId = $contactId;
              $job->mailingId = $mailing->getId();
              $job->contactClass = $this->contactClass;
              $this->queue->put(json_encode($job), null, $interval);
        }
    }

    protected function convertDataIntervalToSeconds(\DateInterval $interval)
    {
        return $interval->format("%days") * 24 * 60 * 60 
            + $interval->format("%h") * 60 * 60 
            + $interval->format("%i") * 60 
            + $interval->format("%s");
    }

    public function sendMailing($mailingId, $contactId, $contactClass)
    {
        $message = $this->builder->buildMessage($mailingId, $contactId, $contactClass);
        $this->mailer->send($message);
        return $message;
    }

    public function processMailing(\Closure $logger)
    {
        $rawJob = $this->queue->get();
        if ($rawJob) {
            $job = json_decode($rawJob->getData(), false);
            $time = new \DateTime("now");
            if (is_callable($logger)) {
                $logger(sprintf("<info>[%s]</info> Processing job with contact id <info>%d</info> "
                    . " and mailing id <info>%d</info>", $time->format("Y-m-d h:i:s"), $job->contactId,
                      $job->mailingId));
            }
            
            $message = $this->sendMailing($job->mailingId, $job->contactId, $job->contactClass);
            if (is_callable($logger)) {
                $logger(sprintf("<info>[%s]</info> Recipient: <info>%s</info> Subject: <info>%s</info>",
                    $time->format("Y-m-d h:i:s"), key($message->getTo()), $message->getSubject()));
            }
            $this->queue->delete($rawJob);
        }
    }

    public function clearQueues()
    {
        $rawJob = $this->queue->get();
        if ($rawJob) {
            $this->queue->delete($rawJob);
        }
    }
}
