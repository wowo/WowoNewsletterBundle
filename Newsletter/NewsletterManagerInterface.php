<?php

namespace Wowo\Bundle\NewsletterBundle\Newsletter;

use Wowo\Bundle\NewsletterBundle\Entity\Mailing;

interface NewsletterManagerInterface
{
    public function putMailingInQueue(Mailing $mailing, array $contactIds);
    public function sendMailing($mailingId, $contactIds, $contactClass);
    public function processMailing(\Closure $logger, $verbose);
}
