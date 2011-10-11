<?php

namespace Wowo\Bundle\NewsletterBundle\Model;

use Symfony\Component\HttpFoundation\Request;

interface MailingManagerInterface
{
    /**
     * Creates mailing object based on form sent by user (basically title and e-mail body
     */
    public function createMailingBasedOnForm($form, $contactCount);
    public function getAvailableTemplates();
}
