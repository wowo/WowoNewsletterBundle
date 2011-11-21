<?php

namespace Wowo\Bundle\NewsletterBundle\Tests\Newsletter;

use lapistano\ProxyObject\ProxyObject;
use \Wowo\Bundle\NewsletterBundle\Newsletter\Spooler;

class SpoolerTest extends \PHPUnit_Framework_TestCase
{
    public function testConstructor()
    {
        $queue = \Mockery::mock('\Wowo\Bundle\QueueBundle\QueueManager');
        $sender = \Mockery::mock('\Wowo\Bundle\NewsletterBundle\Newsletter\Sender');
        $spooler = new Spooler($queue, $sender);
    }
}
