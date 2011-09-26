<?php

namespace Wowo\Bundle\NewsletterBundle\Model;

use Symfony\Component\Validator\Constraints as Assert;
use Wowo\Bundle\NewsletterBundle\Entity\Mailing;

class Newsletter
{
    public $mailing;
    /**
     * @Assert\Choice(min=1)
     */
    public $contacts;
}
