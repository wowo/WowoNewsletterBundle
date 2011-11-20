<?php

namespace Wowo\Bundle\NewsletterBundle\Tests\Newsletter;

use Wowo\Bundle\NewsletterBundle\Newsletter\NewsletterManager;

class NewsletterManagerTest extends \PHPUnit_Framework_TestCase
{
    public function testSetters()
    {
        $manager = new NewsletterManager();
        $manager->setMailer($this->getMock('\Swift_Mailer', null, array(), '', false));
        $manager->setContactClass('Wowo\Bundle\NewsletterBundle\Entity\Contact');
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
