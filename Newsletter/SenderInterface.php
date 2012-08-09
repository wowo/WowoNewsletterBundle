<?php

namespace Wowo\NewsletterBundle\Newsletter;

use Wowo\NewsletterBundle\Newsletter\BuilderInterface;

interface SenderInterface
{
    public function send($mailingId, $contactId, $contactClass);
}
