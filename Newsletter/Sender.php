<?php

namespace Wowo\Bundle\NewsletterBundle\Newsletter;

use Wowo\Bundle\NewsletterBundle\Newsletter\BuilderInterface;

class Sender implements SenderInterface
{
    protected $mailer;
    protected $builder;

    /**
     * Constructs object with mailer and builder dependencies
     *
     * @param \Swift_Mailer $mailer 
     * @param BuilderInterface $builder 
     * @access public
     * @return Sender
     */
    public function __construct(\Swift_Mailer $mailer, BuilderInterface $builder)
    {
        $this->mailer  = $mailer;
        $this->builder = $builder;
    }

    /**
     * Sends mailing
     * 
     * @param mixed $mailingId 
     * @param mixed $contactId 
     * @param mixed $contactClass 
     * @access public
     * @return void
     */
    public function send($mailingId, $contactId, $contactClass)
    {
        $message = $this->builder->buildMessage($mailingId, $contactId, $contactClass);
        $this->mailer->send($message);
        return $message;
    }
}
