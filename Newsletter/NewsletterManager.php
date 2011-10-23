<?php

namespace Wowo\Bundle\NewsletterBundle\Newsletter;

use Doctrine\ORM\EntityManager;
use Wowo\Bundle\NewsletterBundle\Entity\Mailing;
use Wowo\Bundle\NewsletterBundle\Exception\InvalidPlaceholderMappingException;
use Wowo\Bundle\NewsletterBundle\Exception\MailingNotFoundException;
use Wowo\Bundle\NewsletterBundle\Newsletter\PlaceholderProcessorInterface;
use Wowo\Bundle\NewsletterBundle\Newsletter\MailingMedia;

class NewsletterManager implements NewsletterManagerInterface
{
    protected $em;
    protected $contactClass;
    protected $mailer;
    protected $pheanstalk;
    protected $tube;
    protected $sendingTube;
    protected $placeholderProcessor;
    protected $mailingMedia;

    public function __construct()
    {
    }

    public function setEntityManager(EntityManager $em)
    {
        $this->em = $em;
    }

    public function setMailer(\Swift_Mailer $mailer)
    {
        $this->mailer = $mailer;
    }

    public function setPheanstalk(\Pheanstalk $pheanstalk)
    {
        $this->pheanstalk = $pheanstalk;
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

    public function setMailingMedia(MailingMedia $mailingMedia)
    {
        $this->mailingMedia = $mailingMedia;
    }

    public function validateDependencies()
    {
        $dependencies = array(
            'em' => $this->em,
            'mailer' => $this->mailer,
            'pheanstalk' => $this->pheanstalk,
            'contactClass' => $this->contactClass,
            'tube' => $this->tube,
            'placeholderProcessor' => $this->placeholderProcessor,
            'mailingMedia' => $this->mailingMedia,
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
              $this->pheanstalk->useTube($this->tube)->put(json_encode($job),
                  \Pheanstalk::DEFAULT_PRIORITY, $interval);
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
            ->setBody($this->buildMessageBody($contact, $mailing), 'text/html');
        return $message;
    }

    protected function buildMessageBody($contact, Mailing $mailing)
    {
        $body = $this->placeholderProcessor->process($contact, $mailing->getBody());
        $body = $this->mailingMedia->embedMedia($body);
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
        $rawJob = $this->pheanstalk->watch($this->tube)->ignore('default')->reserve();
        if ($rawJob) {
            $job = json_decode($rawJob->getData(), false);
            $time = new \DateTime("now");
            if (is_callable($logger)) {
                $logger(sprintf("<info>[%s]</info> Processing job with contact id <info>%d</info> "
                    . " and mailing id <info>%d</info>", $time->format("Y-m-d h:i:s"), $job->contactId, $job->mailingId));
            }
            
            $message = $this->sendMailing($job->mailingId, $job->contactId, $job->contactClass);
            if (is_callable($logger)) {
                $logger(sprintf("<info>Sent message:</info>\n%s", $message->toString()));
            }
            $this->pheanstalk->delete($rawJob);
        }
    }

    public function fillPlaceholders($contact, $body)
    {
        return $this->placeholderProcessor->process($contact, $body);
    }
}
