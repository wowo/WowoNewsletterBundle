<?php

namespace Wowo\Bundle\NewsletterBundle\Tests\Newsletter;

use lapistano\ProxyObject\ProxyObject;
use Wowo\Bundle\NewsletterBundle\Newsletter\MailingMedia;

class MailingMediaTest extends \PHPUnit_Framework_TestCase
{
    private $testTemplatePath;

    public function setUp()
    {
        $this->testTemplatePath = __DIR__.'/../Data/mailing.html';
    }

    public function testEmbedMedia()
    {
        $body = file_get_contents($this->testTemplatePath);
        $msg  = \Swift_Message::newInstance('testing subject');
        $mailingMedia = new MailingMedia($msg);

        $this->assertNotNull($body);
        $bodyWithMedia = $mailingMedia->embedMedia($body, $this->testTemplatePath, $msg);

        var_dump($bodyWithMedia);
        $this->assertNotEquals($body, $bodyWithMedia);
    }
}

