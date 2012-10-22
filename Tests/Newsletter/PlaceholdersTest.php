<?php

namespace Wowo\NewsletterBundle\Tests\Newsletter;

use Wowo\NewsletterBundle\Entity\Contact;
use Wowo\NewsletterBundle\Newsletter\NewsletterManager;
use Doctrine\ORM\EntityManager;
use Wowo\NewsletterBundle\Newsletter\Placeholders\Exception\InvalidPlaceholderMappingException;
use Wowo\NewsletterBundle\Newsletter\Placeholders\PlaceholderProcessor;
use lapistano\ProxyObject\ProxyBuilder;

class PlaceholdersTest extends \PHPUnit_Framework_TestCase
{
    public function testFillPlaceholders()
    {
        $contact = $this->getContact();
        $body = "Samuel L. Ipsum, read it {{ name }} {{ surname }}

            The path of the righteous man is beset on all sides by the iniquities of the selfish and the tyranny of evil men. Blessed is he who, in the name of charity and good will, shepherds the weak through the valley of darkness, for he is truly his brother's keeper and the finder of lost children. And I will strike down upon thee with great vengeance and furious anger those who would attempt to poison and destroy My brothers. And you will know My name is the Lord when I lay My vengeance upon thee.  Email: {{ email }}";
        $resultBody = "Samuel L. Ipsum, read it John Smith

            The path of the righteous man is beset on all sides by the iniquities of the selfish and the tyranny of evil men. Blessed is he who, in the name of charity and good will, shepherds the weak through the valley of darkness, for he is truly his brother's keeper and the finder of lost children. And I will strike down upon thee with great vengeance and furious anger those who would attempt to poison and destroy My brothers. And you will know My name is the Lord when I lay My vengeance upon thee.  Email: john@example.org";

        $mapping = array(
            "email" => "getEmail",
            "name"  => "getName",
            "surname" => "getSurname",
        );
        $manager = new PlaceholderProcessor();
        $manager->setMapping($mapping);
        $manager->setReferenceClass(get_class($contact));
        $this->assertEquals($resultBody, $manager->process($contact, $body));
    }

    protected function getContact()
    {
        $contact = new Contact();
        $contact->setEmail("john@example.org");
        $contact->setName("John");
        $contact->setSurname("Smith");
        return $contact;
    }
    
    /**
     *  @expectedException \BadMethodCallException 
     */
    public function testFillPlaceholdersWithoutConfiguration()
    {
        $manager = new PlaceholderProcessor();
        $manager->process(new \StdClass(), "");
    }
    
    /**
     *  @expectedException \InvalidArgumentException
     */
    public function testFillPlaceholdersWithBadContactClass()
    {
        $manager = new PlaceholderProcessor();
        $manager->setReferenceClass("\BadClass");
        $manager->process(new \StdClass(), "");
    }

    /**
     *  @expectedException Wowo\NewsletterBundle\Newsletter\Placeholders\Exception\InvalidPlaceholderMappingException
     *  @expectedExceptionCode 1
     */
    public function testFillPlaceholdersWithNonPublicPropertyPlaceholder()
    {
        $contact = $this->getContact();
        $mapping = array(
            "email" => "email",
        );
        $manager = new PlaceholderProcessor();
        $manager->setMapping($mapping);
        $manager->setReferenceClass(get_class($contact));
        $manager->process($contact, "");
    }

    /**
     *  @expectedException Wowo\NewsletterBundle\Newsletter\Placeholders\Exception\InvalidPlaceholderMappingException
     *  @expectedExceptionCode 2
     */
    public function testFillPlaceholdersWithNonPublicMethodPlaceholder()
    {
        $contact = new MockContact();;
        $mapping = array(
            "email" => "getEmailX",
        );
        $manager = new PlaceholderProcessor();
        $manager->setMapping($mapping);
        $manager->setReferenceClass(get_class($contact));
        $manager->process($contact, "");
    }


    /**
     *  @expectedException Wowo\NewsletterBundle\Newsletter\Placeholders\Exception\InvalidPlaceholderMappingException
     *  @expectedExceptionCode 3
     */
    public function testFillPlaceholdersWithNonExistingSourcePlaceholder()
    {
        $contact = $this->getContact();
        $mapping = array(
            "email" => "unkonown",
        );
        $manager = new PlaceholderProcessor();
        $manager->setMapping($mapping);
        $manager->setReferenceClass(get_class($contact));
        $manager->process($contact, "");
    }

    /**
     * @expectedException InvalidArgumentException
     */
    public function testProcessWithWrongReferenceClass()
    {
        $manager = new PlaceholderProcessor();
        $manager->setReferenceClass(get_class($this));
        $manager->setMapping(array('asd' => 'asd'));
        $manager->process(new \Exception('asd'), 'asd');
    }

    public function testGetPlaceholderValue()
    {
        $proxy = new ProxyBuilder('\Wowo\NewsletterBundle\Newsletter\Placeholders\PlaceholderProcessor');
        $managerProxy= $proxy
            ->setMethods(array('getPlaceholderValue'))
            ->getProxy();
        $this->assertEquals('lol', $managerProxy->getPlaceholderValue(new FakeObject(), 'source'));
    }
}

class FakeObject {
    public $source = 'lol';
}

class MockContact extends Contact {
    protected function getEmailX() {}
}
