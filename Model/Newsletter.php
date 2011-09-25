<?php

namespace Wowo\Bundle\NewsletterBundle\Model;

use Symfony\Component\Validator\Constraints as Assert;

class Newsletter
{
    public $mailing;
    /**
     * @Assert\Choice(min=1)
     */
    public $contacts;
}
