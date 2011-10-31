<?php

namespace Wowo\Bundle\NewsletterBundle\Tests\Newsletter;

use Wowo\Bundle\NewsletterBundle\Newsletter\ContactManager;
use Wowo\Bundle\NewsletterBundle\Entity\Contact;
use lapistano\ProxyObject\ProxyObject;

class ContactManagerTest extends \PHPUnit_Framework_TestCase
{
    public function testFintContactToChooseForMailing()
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
        $emMock  = $this->getMock('\Doctrine\ORM\EntityManager',
            array('getRepository', 'getClassMetadata'), array(), '', false);
        $emMock->expects($this->any())
            ->method('getRepository')
            ->will($this->returnValue(new FakeRepository()));
        $emMock->expects($this->any())
            ->method('getClassMetadata')
            ->will($this->returnValue((object)array('name' => 'aClass')));
        $containerMock = $this->getMock('Symfony\Component\DependencyInjection\Container',
            array('get'), array(), '', false);
        $containerMock->expects($this->any())
            ->method('get')
            ->will($this->returnValue(null));

        return new ContactManager($emMock, $containerMock, 'aClass');
    }
}

class FakeRepository
{
    public function findAll()
    {
        $c1 = new Contact();
        $c1->setName('john');
        return array($c1);
    }
}
