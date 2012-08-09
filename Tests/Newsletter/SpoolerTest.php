<?php

namespace Wowo\NewsletterBundle\Tests\Newsletter;

use lapistano\ProxyObject\ProxyObject;
use \Wowo\NewsletterBundle\Newsletter\Spooler;
use \Wowo\NewsletterBundle\Entity\Mailing;

class SpoolerTest extends \PHPUnit_Framework_TestCase
{
    public function testConstructor()
    {
        $queue = \Mockery::mock('\Wowo\QueueBundle\QueueManager');
        $sender = \Mockery::mock('\Wowo\NewsletterBundle\Newsletter\Sender');
        $spooler = new Spooler($queue, $sender);
        $spooler->setLogger(function() {});
    }

    public function testSpoolManyContacts()
    {
        $data = array('contactId' => 1, 'mailingId' => null, 'contactClass' => 'Foo');
        $queue = \Mockery::mock('\Wowo\QueueBundle\QueueManager');
        $queue->shouldReceive('put')->with(json_encode((object)$data), null, null)->once()->ordered();
        $data['contactId'] = 2;
        $queue->shouldReceive('put')->with(json_encode((object)$data), null, null)->once()->ordered();
        $sender = \Mockery::mock('\Wowo\NewsletterBundle\Newsletter\Sender');
        $spooler = new Spooler($queue, $sender);

        $mailing= new Mailing();
        $mailing->setDelayedMailing(false);

        $count = $spooler->spoolManyContacts($mailing, array(1,2), 'Foo');
        $this->assertEquals(2, $count);
    }

    public function testSpoolManyContactsNonUnique()
    {
        $data = array('contactId' => 1, 'mailingId' => null, 'contactClass' => 'Foo');
        $queue = \Mockery::mock('\Wowo\QueueBundle\QueueManager');
        $queue->shouldReceive('put')->with(json_encode((object)$data), null, null)->once()->ordered();
        $sender = \Mockery::mock('\Wowo\NewsletterBundle\Newsletter\Sender');
        $spooler = new Spooler($queue, $sender);

        $mailing= new Mailing();
        $mailing->setDelayedMailing(false);

        $count = $spooler->spoolManyContacts($mailing, array(1,1), 'Foo');
        $this->assertEquals(1, $count);
    }

    /**
     * @expectedException \InvalidArgumentException
     */
    public function testSpoolManyContactsWithEmptyContacts()
    {
        $queue = \Mockery::mock('\Wowo\QueueBundle\QueueManager');
        $sender = \Mockery::mock('\Wowo\NewsletterBundle\Newsletter\Sender');
        $spooler = new Spooler($queue, $sender);

        $mailing= new Mailing();
        $mailing->setDelayedMailing(false);

        $count = $spooler->spoolManyContacts($mailing, array(), 'Foo');
    }

    public function testClearSuccessful()
    {
        $sender = \Mockery::mock('\Wowo\NewsletterBundle\Newsletter\Sender');
        $queue = \Mockery::mock('\Wowo\QueueBundle\QueueManager');
        $job = new \StdClass();
        $job->id = 666;
        $queue->shouldReceive('get')->andReturn($job)->once()->ordered();
        $queue->shouldReceive('delete')->with($job)->once()->ordered();
        $spooler = new Spooler($queue, $sender);

        $this->assertTrue($spooler->clear());
    }

    public function testClearNone()
    {
        $sender = \Mockery::mock('\Wowo\NewsletterBundle\Newsletter\Sender');
        $queue = \Mockery::mock('\Wowo\QueueBundle\QueueManager');
        $queue->shouldReceive('get')->andReturn(null)->once()->ordered();
        $spooler = new Spooler($queue, $sender);

        $this->assertTrue(!$spooler->clear());
    }

    public function testGetInterval()
    {
        $spooler = new ProxyObject();
        $spooler = $spooler->getProxyBuilder('\Wowo\NewsletterBundle\Newsletter\Spooler')
            ->setMethods(array('getInterval'))
            ->disableOriginalConstructor()
            ->getProxy();

        $mailing = new Mailing();

        $mailing->setDelayedMailing(true);
        $mailing->setSendDate(new \DateTime('+1 second'));
        $this->assertEquals(1, $spooler->getInterval($mailing));
        $mailing->setSendDate(new \DateTime('+60 second'));
        $this->assertEquals(60, $spooler->getInterval($mailing));
        $mailing->setSendDate(new \DateTime('+1 minute'));
        $this->assertEquals(60, $spooler->getInterval($mailing));
        $mailing->setSendDate(new \DateTime('+1 hour'));
        $this->assertEquals(60*60, $spooler->getInterval($mailing));

        $mailing->setDelayedMailing(false);
        $this->assertNull($spooler->getInterval($mailing));
    }

    public function testProcess()
    {
        $mockJob = new MockJob();

        $queue = \Mockery::mock('\Wowo\QueueBundle\QueueManager');
        $queue->shouldReceive('get')->andReturn($mockJob)->once()->ordered();
        $queue->shouldReceive('delete')->with($mockJob)->once()->ordered();

        $sender = \Mockery::mock('\Wowo\NewsletterBundle\Newsletter\Sender');
        $sender
            ->shouldReceive('send')
            ->with($mockJob->mailingId, $mockJob->contactId, $mockJob->contactClass)
            ->andReturn(new MockMessage())
            ->once()
            ->ordered();

        $spooler = new Spooler($queue, $sender);
        $spooler->setLogger(function() {});

        $spooler->process();
        $this->assertTrue(true, 'Everything should pass since there');

    }
}

class MockJob
{
    public $contactId = 1;
    public $mailingId = 1;
    public $contactClass = 'Foo';

    public function getData()
    {
        return json_encode($this);
    }
}

class MockMessage
{
    public function getTo()
    {
        return array('john@example.com' => 'john');
    }

    public function getSubject()
    {
        return 'foo bar';
    }
}
