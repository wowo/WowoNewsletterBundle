<?php

namespace Wowo\Bundle\NewsletterBundle\Newsletter;

use Symfony\Component\HttpFoundation\Request;

interface MailingManagerInterface
{
    /**
     * Creates mailing object based on form sent by user (basically title and e-mail body
     */
    public function createMailingBasedOnForm($form, $contactCount);
}
