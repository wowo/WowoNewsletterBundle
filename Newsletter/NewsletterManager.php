<?php

namespace Wowo\Bundle\NewsletterBundle\Newsletter;

use Doctrine\ORM\EntityManager;
use Wowo\Bundle\NewsletterBundle\Entity\Mailing;
use Wowo\Bundle\NewsletterBundle\Exception\InvalidPlaceholderMappingException;
use Wowo\Bundle\NewsletterBundle\Exception\MailingNotFoundException;
use Wowo\Bundle\NewsletterBundle\Exception\ContactNotFoundException;
use Wowo\Bundle\NewsletterBundle\Newsletter\Placeholders\PlaceholderProcessorInterface;
use Wowo\Bundle\NewsletterBundle\Newsletter\Media\MediaManagerInterface;
use Wowo\Bundle\QueueBundle\QueueManager;

class NewsletterManager implements NewsletterManagerInterface
{
    protected $em;
    protected $contactClass;
    protected $mailer;
    protected $tube;
    protected $sendingTube;
    protected $placeholderProcessor;
    protected $mediaManager;
    protected $queue;

    public function __construct(QueueManager $queue)
    {
        $this->queue = $queue;
    }

    public function setEntityManager(EntityManager $em)
    {
        $this->em = $em;
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

    public function setTube($tube)
    {
        $this->tube = $tube;
    }

    public function setPlaceholderProcessor(PlaceholderProcessorInterface $processor)
    {
        $this->placeholderProcessor = $processor;
    }

    public function setMailingMedia(MediaManagerInterface $manager)
    {
        $this->mediaManager= $manager;
    }

    public function validateDependencies()
    {
        $dependencies = array(
            'em' => $this->em,
            'mailer' => $this->mailer,
            'contactClass' => $this->contactClass,
            'tube' => $this->tube,
            'placeholderProcessor' => $this->placeholderProcessor,
            'mediaManager' => $this->mediaManager,
        );
        foreach ($dependencies as $name => $dependency) {
            if (null == $dependency) {
                throw new \RuntimeException(sprintf('Dependency "%s" is not set or set to null', $name));
            }
        }
    }

    public function putMailingInQueue(Mailing $mailing, array $contactIds)
    {
        if (null == $this->tube) {
            throw new \InvalidArgumentException("Preparation tube unkonwn!");
        }
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

    protected function buildMessage($mailingId, $contactId, $contactClass)
    {
        $contact = $this->em
            ->getRepository($contactClass)
            ->find($contactId);
        if (!$contact) {
            throw new ContactNotFoundException(sprintf('Contact %s with id %d not found', $contactClass, $contactId));
        }
        $mailing = $this->em
            ->getRepository('WowoNewsletterBundle:Mailing')
            ->find($mailingId);
        if (null == $mailing) {
            throw new MailingNotFoundException(sprintf('Mailing with id %d not found', $mailingId));
        }
        $fullName = method_exists($contact, "getFullName") ? $contact->getFullName() : $contact->getEmail();
        $message = \Swift_Message::newInstance()
            ->setFrom(array($mailing->getSenderEmail() => $mailing->getSenderName() ?: $mailing->getSenderEmail()))
            ->setTo(array($contact->getEmail() => $fullName))
            ->setSubject($this->buildMessageSubject($contact, $mailing))
            ->setMaxLineLength(1000);

        $body = $this->buildMessageBody($contact, $mailing, $message);
        $message->setBody($body, 'text/html');
        return $message;
    }

    protected function buildMessageBody($contact, Mailing $mailing, \Swift_Message $message)
    {
        $body = $this->placeholderProcessor->process($contact, $mailing->getBody());
        $body = $this->mediaManager->embed($body, $message);
        return $body;
    }

    protected function buildMessageSubject($contact, Mailing $mailing)
    {
        $title = $this->placeholderProcessor->process($contact, $mailing->getTitle());
        return $title;
    }


    public function sendMailing($mailingId, $contactId, $contactClass)
    {
        $message = $this->buildMessage($mailingId, $contactId, $contactClass);
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

    public function fillPlaceholders($contact, $body)
    {
        return $this->placeholderProcessor->process($contact, $body);
    }
}
