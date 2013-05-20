<?php

namespace Wowo\NewsletterBundle\Newsletter;

interface SenderInterface
{
    public function send($mailingId, $contactId, $contactClass);
}
