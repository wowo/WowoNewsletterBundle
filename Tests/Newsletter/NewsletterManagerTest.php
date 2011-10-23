<?php

namespace Wowo\Bundle\NewsletterBundle\Tests\Newsletter;

use lapistano\ProxyObject\ProxyObject;

class NewsletterManagerTest extends \PHPUnit_Framework_TestCase
{
    public function testBuildBody()
    {
        $proxy = new ProxyObject();
        $newsletterManagerProxy = $proxy->getProxyBuilder('\Wowo\Bundle\NewsletterBundle\Newsletter\NewsletterManager')
            ->setMethods(array('buildMessageBody'))
            ->getProxy();
        $newsletterManagerProxy->buildMessageBody();
    }
}
