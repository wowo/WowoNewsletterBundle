<?php

namespace Wowo\Bundle\NewsletterBundle\Newsletter;

interface NewsletterManagerInterface
{
  public function putMailingInPreparationQueue($mailingId, array $contactIds);
  public function sendMailing($mailingId, $contactIds, $contactClass);
}
