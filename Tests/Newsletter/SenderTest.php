<?php

namespace Wowo\Bundle\NewsletterBundle\Tests\Newsletter;

use lapistano\ProxyObject\ProxyObject;
use \Wowo\Bundle\NewsletterBundle\Newsletter\Sender;

class SenderTest extends \PHPUnit_Framework_TestCase
{
    public function testConstructor()
    {
        $mailer = \Mockery::mock('\Swift_Mailer');
        $builder = \Mockery::mock('\Wowo\Bundle\NewsletterBundle\Newsletter\Builder');
        $spooler = new Sender($mailer, $builder);
    }
}
