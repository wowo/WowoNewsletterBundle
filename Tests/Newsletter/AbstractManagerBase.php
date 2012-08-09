<?php

namespace Wowo\NewsletterBundle\Tests\Newsletter;

use Wowo\NewsletterBundle\Entity\Contact;

class AbstractManagerBase extends \PHPUnit_Framework_TestCase
{
    protected function getEmMock()
    {
        $emMock = \Mockery::mock('\Doctrine\ORM\EntityManager',
            array(
                'getRepository' => new FakeRepository(),
                'getClassMetadata' => (object)array('name' => 'aClass'),
                'persist' => null,
                'flush' => null,
            ));
        return $emMock;
    }

    protected function getContainerMock()
    {
        $containerMock = \Mockery::mock('\Symfony\Component\DependencyInjection\Container',
            array('get' => null));
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
