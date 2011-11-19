<?php

namespace Wowo\Bundle\NewsletterBundle\Tests\Newsletter;

use Wowo\Bundle\NewsletterBundle\Newsletter\TemplateManager;
use lapistano\ProxyObject\ProxyObject;

class TemplateManagerTest extends \PHPUnit_Framework_TestCase
{
    protected $class = '\Wowo\Bundle\NewsletterBundle\Newsletter\TemplateManager';

    public function testGetters()
    {
        $manager = new TemplateManager(\Mockery::mock('\Wowo\Bundle\NewsletterBundle\Newsletter\Media\MediaManager'));
        $tpls = array("main" => "/tmp/main.html");
        $manager->setAvailableTemplates($tpls);
        $this->assertEquals($tpls, $manager->getAvailableTemplates());
    }

    public function testGetActiveTemplatePathAndBody()
    {
        $tpls = array("main" => tempnam(sys_get_temp_dir(), 'NewsletterBundle'));
        file_put_contents($tpls['main'], 'NewsletterBundle');

        $proxy = new ProxyObject();
        $managerProxy= $proxy
            ->getProxyBuilder($this->class)
            ->setMethods(array('getActiveTemplatePath', 'getActiveTemplateBody'))
            ->disableOriginalConstructor()
            ->getProxy();
        $managerProxy->setAvailableTemplates($tpls);
        $this->assertEquals($tpls['main'], $managerProxy->getActiveTemplatePath());
        $this->assertEquals('NewsletterBundle', $managerProxy->getActiveTemplateBody());
    }

    public function testApplyTemplate()
    {
        $body =<<<EOT
<html>
<body>
    <h1>{{ title }}</h1>
    <div>{{ content }} <img src="images/lol.gif" /></div>
</body>
</html>
EOT;
        $result =<<<EOT
<html>
<body>
    <h1>NewsletterBundle</h1>
    <div>Lorem Ipsum <img src="/tmp/images/lol.gif" /></div>
</body>
</html>
EOT;
        $mediaManager = new \Wowo\Bundle\NewsletterBundle\Newsletter\Media\MediaManager();
        $managerMock = $this->getMock($this->class, array('getActiveTemplateBody', 'getActiveTemplatePath'),
            array($mediaManager));
        $managerMock->expects($this->any())
            ->method('getActiveTemplatePath')
            ->will($this->returnValue('/tmp/main.html'));
        $managerMock->expects($this->any())
            ->method('getActiveTemplateBody')
            ->will($this->returnValue($body));
        $managerMock->setTemplateRegex(array('src'));

        $this->assertEquals($result, $managerMock->applyTemplate('Lorem Ipsum', 'NewsletterBundle'));
    }

    /**
     * @expectedException \Wowo\Bundle\NewsletterBundle\Exception\NonExistingTemplateException
     */
    public function testGetActiveTemplateBodyNonExistingPath()
    {
        $proxy = new ProxyObject();
        $managerProxy= $proxy
            ->getProxyBuilder($this->class)
            ->setMethods(array('getActiveTemplateBody'))
            ->disableOriginalConstructor()
            ->getProxy();
        $managerProxy->getActiveTemplateBody();
    }
}
