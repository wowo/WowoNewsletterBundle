<?php

namespace Wowo\Bundle\NewsletterBundle\Tests\Newsletter;

use lapistano\ProxyObject\ProxyObject;
use \Wowo\Bundle\NewsletterBundle\Newsletter\Builder;

class BuilderTest extends \PHPUnit_Framework_TestCase
{
    public function testConstructor()
    {
        $MailingManager = \Mockery::mock('\Wowo\Bundle\NewsletterBundle\Newsletter\Model\MailingManager');
        $ContactManager = \Mockery::mock('\Wowo\Bundle\NewsletterBundle\Newsletter\Model\ContactManager');
        $PlaceholderProcessor = \Mockery::mock('\Wowo\Bundle\NewsletterBundle\Newsletter\Placeholders\PlaceholderProcessor');
        $MediaManager = \Mockery::mock('\Wowo\Bundle\NewsletterBundle\Newsletter\Media\MediaManager');
        $builder = new Builder($MailingManager, $ContactManager, $PlaceholderProcessor, $MediaManager);
    }
}
