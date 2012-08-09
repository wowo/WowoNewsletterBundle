<?php

namespace Wowo\NewsletterBundle\Newsletter;

use Symfony\Component\Validator\Constraints as Assert;
use Wowo\NewsletterBundle\Entity\Mailing;

class Newsletter
{
    public $mailing;
    public $contacts;

    public function __construct()
    {
        $this->mailing = new Mailing();
    }

    public function setSenderNameProxy($value)
    {
        $this->mailing->setSenderName($value);
    }

    public function setSenderEmailProxy($value)
    {
        $this->mailing->setSenderEmail($value);
    }
}
