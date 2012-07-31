<?php

namespace Wowo\NewsletterBundle\Newsletter;

use Wowo\NewsletterBundle\Newsletter\Model\MailingManagerInterface;
use Wowo\NewsletterBundle\Newsletter\Model\ContactManagerInterface;
use Wowo\NewsletterBundle\Newsletter\Placeholders\PlaceholderProcessorInterface;
use Wowo\NewsletterBundle\Newsletter\Media\MediaManagerInterface;
use Wowo\NewsletterBundle\Entity\Mailing;

/**
 * Builds Swift Mailer message based on mailin
 * 
 * @package default
 * @version $id$
 * @copyright 
 * @author Wojciech Sznapka <wojciech@sznapka.pl> 
 * @license 
 */
class Builder implements BuilderInterface
{
    protected $mailingManager;
    protected $contactManager;
    protected $placeholderProcessor;
    protected $mediaManager;

    /**
     * Constructor, passing obligatory dependencies
     * 
     * @param MailingManagerInterface $mailingManager 
     * @param ContactManagerInterface $contactManager 
     * @access public
     * @return void
     */
    public function __construct(
        MailingManagerInterface $mailingManager,
        ContactManagerInterface $contactManager,
        PlaceholderProcessorInterface $placeholderProcessor,
        MediaManagerInterface $mediaManager
    ) {
        $this->mailingManager = $mailingManager;
        $this->contactManager = $contactManager;
        $this->placeholderProcessor = $placeholderProcessor;
        $this->mediaManager = $mediaManager;
    }


    public function buildMessage($mailingId, $contactId, $contactClass)
    {
        $contact = $this->contactManager->findContact($contactId, $contactClass);
        $mailing = $this->mailingManager->findMailing($mailingId);

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

}
