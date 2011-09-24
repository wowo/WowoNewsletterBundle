<?php

namespace Wowo\Bundle\NewsletterBundle\Model;

interface MailingManagerInterface
{
  /**
   * Creates mailing object based on form sent by user (basically title and e-mail body
   */
  public function createMailingBasedOnForm($form, $contactCount);
}

