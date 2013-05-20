<?php

namespace Wowo\NewsletterBundle\Tests\Newsletter;

use \Wowo\NewsletterBundle\Newsletter\Builder;

class BuilderTest extends \PHPUnit_Framework_TestCase
{
    public function testConstructor()
    {
        $MailingManager = \Mockery::mock('\Wowo\NewsletterBundle\Newsletter\Model\MailingManager');
        $ContactManager = \Mockery::mock('\Wowo\NewsletterBundle\Newsletter\Model\ContactManager');
        $PlaceholderProcessor = \Mockery::mock('\Wowo\NewsletterBundle\Newsletter\Placeholders\PlaceholderProcessor');
        $MediaManager = \Mockery::mock('\Wowo\NewsletterBundle\Newsletter\Media\MediaManager');
        $builder = new Builder($MailingManager, $ContactManager, $PlaceholderProcessor, $MediaManager);
    }
}
