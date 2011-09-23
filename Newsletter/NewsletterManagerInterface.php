<?php

namespace Wowo\Bundle\NewsletterBundle\Newsletter;

interface NewsletterManagerInterface
{
  public function putMailingInPreparationQueue($mailingId, array $contactIds);
}
