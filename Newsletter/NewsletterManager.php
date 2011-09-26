<?php

namespace Wowo\Bundle\NewsletterBundle\Newsletter;

use Doctrine\ORM\EntityManager;
use Wowo\Bundle\NewsletterBundle\Entity\Mailing;

class NewsletterManager implements NewsletterManagerInterface
{
    protected $em;
    protected $class;
    protected $mailer;
    protected $pheanstalk;
    protected $tube;
    protected $sendingTube;

    public function __construct(EntityManager $em, \Pheanstalk $pheanstalk, \Swift_Mailer $mailer, $class, $tube)
    {
        $this->em = $em;
        $metadata = $this->em->getClassMetadata($class);
        $this->class = $metadata->name;
        $this->mailer = $mailer;

        $this->pheanstalk = $pheanstalk;
        $this->tube = $tube;
    }

    public function putMailingInPreparationQueue(Mailing $mailing, array $contactIds)
    {
        if (null == $this->tube) {
            throw new \InvalidArgumentException("Preparation tube unkonwn!");
        }
        if (count($contactIds) == 0) {
            throw new \InvalidArgumentException('No contacs selected, it need to be at least one contact to send mailing');
        }
        $interval = $mailing->getSendDate()->diff(new \DateTime("now"));
        foreach($contactIds as $contactId) {
              $job = new \StdClass();
              $job->contactId = $contactId;
              $job->mailingId = $mailing->getId();
              $job->contactClass = $this->class;
              $this->pheanstalk->useTube($this->tube)->put(json_encode($job), \Pheanstalk::DEFAULT_PRIORITY,
                  $this->convertDataIntervalToSeconds($interval));
        }
    }

    protected function convertDataIntervalToSeconds(\DateInterval $interval)
    {
        return $interval->format("%days") * 24 * 60 * 60 + $interval->format("%h") * 60 * 60 + $interval->format("%i") * 60 + $interval->format("%s");
    }

    protected function buildMessage($mailingId, $contactId, $contactClass)
    {
        $contact = $this->em
            ->getRepository($contactClass)
            ->find($contactId);
        $mailing = $this->em
            ->getRepository('WowoNewsletterBundle:Mailing')
            ->find($mailingId);
        $body = $this->buildMessageBody($contact, $mailing);
        $message = \Swift_Message::newInstance()
            ->setSubject($mailing->getTitle())
            ->setFrom(array($mailing->getSenderEmail() => $mailing->getSenderName() ?: $mailing->getSenderEmail()))
            ->setTo(array($contact->getEmail() => method_exists($contact, "getFullName") ? $contact->getFullName() : $contact->getEmail()))
            ->setBody($body);
        return $message;
    }

    protected function buildMessageBody($contact, Mailing $mailing)
    {
        return $mailing->getBody();
    }

    public function sendMailing($mailingId, $contactId, $contactClass)
    {
        $message = $this->buildMessage($mailingId, $contactId, $contactClass);
        $this->mailer->send($message);
        return $message;
    }

    public function getJobFromQueueAndSendMailing(\Closure $logger, $verbose)
    {
        $rawJob = $this->pheanstalk->watch($this->tube)->ignore('default')->reserve();
        if ($rawJob) {
            $job = json_decode($rawJob->getData(), false);
            $time = new \DateTime("now");
            $logger(sprintf("<info>[%s]</info> Processing job with contact id <info>%d</info> "
                . " and mailing id <info>%d</info>", $time->format("Y-m-d h:i:s"), $job->contactId, $job->mailingId));
            
            $message = $this->sendMailing($job->mailingId, $job->contactId, $job->contactClass);
            if ($verbose) {
                $logger(sprintf("Sent message:\n%s", $message->toString()));
            }
            $this->pheanstalk->delete($rawJob);
        }
    }
}
