<?php

namespace Wowo\NewsletterBundle\Tests\Newsletter;

use Wowo\NewsletterBundle\Newsletter\Newsletter;

class NewsletterTest extends \PHPUnit_Framework_TestCase
{
    public function testSetters()
    {
        $newsletter = new Newsletter();
        $newsletter->setSenderNameProxy('name');
        $newsletter->setSenderEmailProxy('name@example.org');
        $this->assertEquals('name', $newsletter->mailing->getSenderName());
        $this->assertEquals('name@example.org', $newsletter->mailing->getSenderEmail());
    }
}
