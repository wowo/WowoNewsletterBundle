<?php

namespace Wowo\NewsletterBundle\Newsletter;

interface BuilderInterface
{
    public function buildMessage($mailingId, $contactId, $contactClass);
}
