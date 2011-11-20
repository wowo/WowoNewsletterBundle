<?php

namespace Wowo\Bundle\NewsletterBundle\Tests\Newsletter;

use Wowo\Bundle\NewsletterBundle\Newsletter\NewsletterManager;

class NewsletterManagerTest extends \PHPUnit_Framework_TestCase
{
    public function testSetters()
    {
        $manager = new NewsletterManager();
        $manager->setEntityManager($this->getMock('\Doctrine\ORM\EntityManager', null, array(), '', false));
        $manager->setMailer($this->getMock('\Swift_Mailer', null, array(), '', false));
        $manager->setContactClass('Wowo\Bundle\NewsletterBundle\Entity\Contact');
        $manager->setTube('tube');
        $manager->setMailingMedia($this->getMock('Wowo\Bundle\NewsletterBundle\Newsletter\Media\MediaManager'));
        $manager->setPlaceholderProcessor($this->getMock('Wowo\Bundle\NewsletterBundle\Newsletter\Placeholders\PlaceholderProcessorInterface'));
        $manager->validateDependencies();
    }

    /**
     * @expectedException RuntimeException
     */
    public function testValidateDependenciesWithNoneSet()
    {
        $manager = new NewsletterManager();
        $manager->validateDependencies();
    }

    /**
     * @expectedException InvalidArgumentException
     */
    public function testSetcontactClassForNonExistantClass()
    {
        $manager = new NewsletterManager();
        $manager->setContactClass('ThisDoesNotExist');
    }
}
