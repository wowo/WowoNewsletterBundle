<?php

namespace Wowo\Bundle\NewsletterBundle\Newsletter;

interface BuilderInterface
{
    public function buildMessage($mailingId, $contactId, $contactClass);
}
