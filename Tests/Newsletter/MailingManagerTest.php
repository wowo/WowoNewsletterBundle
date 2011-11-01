<?php

namespace Wowo\Bundle\NewsletterBundle\Tests\Newsletter;

use Wowo\Bundle\NewsletterBundle\Newsletter\MailingManager;
use Wowo\Bundle\NewsletterBundle\Newsletter\TemlateManager;
use Wowo\Bundle\NewsletterBundle\Entity\Mailing;

class MailingManagerTest extends AbstractManagerBase
{
    public function testFindChoosenContactIdForMailing()
    {
        $templateManagerMock = $this->getMock('\Wowo\Bundle\NewsletterBundle\Newsletter\TemplateManager');
        $manager = new MailingManager($this->getEmMock(), $this->getContainerMock(), 'aClass');
        $manager->setTemplateManager($templateManagerMock);


        $mock = \Mockery::mock('Symfony\Component\Form\AbstractType',
            array('getData' => array('mailing' => new Mailing())));

        $result = new Mailing();
        $result->setTotalCount(0);
        $result->setSentCount(0);
        $result->setErrorsCount(0);
        $this->assertEquals($result, $manager->createMailingBasedOnForm($mock, 0));
        $result->setTotalCount(1);
        $this->assertEquals($result, $manager->createMailingBasedOnForm($mock, 1));
    }

}
