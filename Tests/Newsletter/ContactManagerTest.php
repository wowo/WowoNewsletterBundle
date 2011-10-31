<?php

namespace Wowo\Bundle\NewsletterBundle\Tests\Newsletter;

use Wowo\Bundle\NewsletterBundle\Newsletter\ContactManager;
use Wowo\Bundle\NewsletterBundle\Entity\Contact;
use lapistano\ProxyObject\ProxyObject;

class ContactManagerTest extends AbstractManagerBase
{
    public function testFindContactToChooseForMailing()
    {
        $this->assertEquals(array(null => 'john  ()'),
            $this->getContactManager()->findContactToChooseForMailing());
    }

    public function testFindChoosenContactIdForMailing()
    {
        $formMock = $this->getMock('\Wowo\Bundle\NewsletterBundle\Form\MailingType', array('getData'));
        $formMock->expects($this->any())
            ->method('getData')
            ->will($this->returnValue(array('contacts' => array(2 => 'john'))));

        $this->assertEquals(array(2 => 'john'),
            $this->getContactManager()->findChoosenContactIdForMailing($formMock));
    }

    protected function getContactManager()
    {
        return new ContactManager($this->getEmMock(), $this->getContainerMock(), 'aClass');
    }
}
