<?php

namespace Wowo\Bundle\NewsletterBundle\Tests\Newsletter;

use Wowo\Bundle\NewsletterBundle\Entity\Contact;
use Wowo\Bundle\NewsletterBundle\Newsletter\NewsletterManager;
use Doctrine\ORM\EntityManager;
use Wowo\Bundle\NewsletterBundle\Exception\InvalidPlaceholderMappingException;
use Wowo\Bundle\NewsletterBundle\Newsletter\PlaceholderProcessor;

class NewsletterManagerTest extends \PHPUnit_Framework_TestCase
{
    public function testBuildBody()
    {
    }
}
