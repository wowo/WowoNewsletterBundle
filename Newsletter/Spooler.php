<?php

namespace Wowo\Bundle\NewsletterBundle\Newsletter;

use Wowo\Bundle\NewsletterBundle\Entity\Mailing;
use Wowo\QueueBundle\QueueInterface;

/**
 * Spooler implementation
 * 
 * @uses SpoolerInterface
 * @package default
 * @version $id$
 * @copyright 
 * @author Wojciech Sznapka <wojciech@sznapka.pl> 
 * @license 
 */
class Spooler implements SpoolerInterface
{
    protected $queue;
    protected $sender;
    protected $logger;

    /**
     * Constructs object with queue and sender dependencies
     * 
     * @param QueueInterface $queue 
     * @param SenderInterface $sender 
     * @access public
     * @return Spooler
     */
    public function __construct(QueueInterface $queue, SenderInterface $sender)
    {
        $this->queue  = $queue;
        $this->sender = $sender;
    }

    /**
     * setLogger 
     * 
     * @param \Closure $logger 
     * @access public
     * @return void
     */
    public function setLogger(\Closure $logger)
    {
        $this->logger = $logger;
    }

    /**
     * Processes spooled items 
     * 
     * @access public
     * @return void
     */
    public function process()
    {
        $rawJob = $this->queue->get();
        if ($rawJob) {
            $job = json_decode($rawJob->getData(), false);
            $time = new \DateTime("now");
            $logger = $this->logger;
            if (is_callable($logger)) {
                $logger(sprintf("<info>[%s]</info> Processing job with contact id <info>%d</info> "
                    . " and mailing id <info>%d</info>", $time->format("Y-m-d h:i:s"), $job->contactId,
                      $job->mailingId));
            }
            
            $message = $this->sender->send($job->mailingId, $job->contactId, $job->contactClass);
            if (is_callable($logger)) {
                $logger(sprintf("<info>[%s]</info> Recipient: <info>%s</info> Subject: <info>%s</info>",
                    $time->format("Y-m-d h:i:s"), key($message->getTo()), $message->getSubject()));
            }
            $this->queue->delete($rawJob);
        }
    }

    /**
     * Clears queues
     * 
     * @access public
     * @return void
     */
    public function clear()
    {
        $rawJob = $this->queue->get();
        if ($rawJob) {
            $this->queue->delete($rawJob);
            return true;
        } else {
            return false;
        }
    }

    /**
     * Spools mailing
     * 
     * @param Mailing $mailing 
     * @param mixed $contactId 
     * @access public
     * @return void
     */
    public function spool(Mailing $mailing, $contactId, $contactClass)
    {
        $job = new \StdClass();
        $job->contactId = $contactId;
        $job->mailingId = $mailing->getId();
        $job->contactClass = $contactClass;
        $this->queue->put(json_encode($job), null, $this->getInterval($mailing));
    }

    /**
     * Spool with many contacts
     * 
     * @param Mailing $mailing 
     * @param array $contactIds 
     * @access public
     * @return void
     */
    public function spoolManyContacts(Mailing $mailing, array $contactIds, $contactClass)
    {
        if (count($contactIds) == 0) {
            throw new \InvalidArgumentException('No contacs selected, it need to be at least '
                . 'one contact to send mailing');
        }
        $count = 0;
        foreach(array_unique($contactIds) as $contactId) {
            $this->spool($mailing, $contactId, $contactClass);
            $count++;
        }
        return $count;
    }

    /**
     * Gets intaval for mailing
     * 
     * @param Mailing $mailing 
     * @access protected
     * @return seconds
     */
    protected function getInterval(Mailing $mailing)
    {
        if ($mailing->isDelayedMailing()) {
            $interval = $mailing->getSendDate()->diff(new \DateTime("now"));
            $intervalSeconds = $interval->format("%days") * 24 * 60 * 60 
                + $interval->format("%h") * 60 * 60 
                + $interval->format("%i") * 60 
                + $interval->format("%s");
            return $intervalSeconds;
        } else {
            return null;
        }
    }
}
