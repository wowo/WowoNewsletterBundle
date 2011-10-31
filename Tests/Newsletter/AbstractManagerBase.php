<?php

namespace Wowo\Bundle\NewsletterBundle\Tests\Newsletter;

use Wowo\Bundle\NewsletterBundle\Entity\Contact;

class AbstractManagerBase extends \PHPUnit_Framework_TestCase
{
    protected function getEmMock()
    {
        $emMock  = $this->getMock('\Doctrine\ORM\EntityManager',
            array('getRepository', 'getClassMetadata', 'persist', 'flush'), array(), '', false);
        $emMock->expects($this->any())
            ->method('getRepository')
            ->will($this->returnValue(new FakeRepository()));
        $emMock->expects($this->any())
            ->method('getClassMetadata')
            ->will($this->returnValue((object)array('name' => 'aClass')));
        $emMock->expects($this->any())
            ->method('persist')
            ->will($this->returnValue(null));
        $emMock->expects($this->any())
            ->method('flush')
            ->will($this->returnValue(null));
        return $emMock;
    }

    protected function getContainerMock()
    {
        $containerMock = $this->getMock('Symfony\Component\DependencyInjection\Container',
            array('get'), array(), '', false);
        $containerMock->expects($this->any())
            ->method('get')
            ->will($this->returnValue(null));
        return $containerMock;
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
