<?php

namespace Wowo\Bundle\NewsletterBundle\Newsletter;

use Wowo\Bundle\NewsletterBundle\Newsletter\BuilderInterface;

interface SenderInterface
{
    public function send($mailingId, $contactId, $contactClass);
}
