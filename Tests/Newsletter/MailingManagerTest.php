<?php

namespace Wowo\Bundle\NewsletterBundle\Tests\Newsletter;

use Wowo\Bundle\NewsletterBundle\Newsletter\Media\MediaManager;
use lapistano\ProxyObject\ProxyObject;

class MediaManagerTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @dataProvider bodyProvider
     */
    public function testEmbedMedia($body)
    {
        $mailingMedia = new MediaManager();

        $this->assertNotNull($body);
        $bodyWithMedia = $mailingMedia->embed($body, \Swift_Message::newInstance('testing subject'));

        $this->assertNotEquals($body, $bodyWithMedia);
        $this->assertRegexp('#cid:.*?@swift.generated#', $bodyWithMedia);
        $this->assertNotRegexp('#cid:.*?@swift.generated#', $body);
    }

    public function testGetRegex()
    {
        $mailingMedia = new MediaManager();
        $mailingMediaProxy = new ProxyObject();
        $mailingMediaProxy->getProxyBuilder('\\' . get_class($mailingMedia))
            ->setProperties(array('regex'))
            ->getProxy();
        $this->assertNotNull($mailingMedia->getRegex('src'));
        $this->assertNotNull($mailingMedia->getRegex('background'));
        $this->assertNotNull($mailingMedia->getRegex('background_attribute'));
    }

    /**
     * @expectedException \InvalidArgumentException
     */
    public function testGetRegexNotExists()
    {
        $mailingMedia = new MediaManager();
        $mailingMedia->getRegex('foobar');
    }

    public function bodyProvider()
    {
        $image =<<<EOT
<html>
	<head>
		<meta content="text/html; charset=utf-8" http-equiv="Content-Type">
		<title>Mailing</title>
	</head>
	<body>
        <a href="#"><img src="images/bg.jpg" alt="logo" /></a>
	</body>
</html>
EOT;
        $cssBackground =<<<EOT
<html>
	<head>
		<meta content="text/html; charset=utf-8" http-equiv="Content-Type">
		<title>Mailing</title>
		<style type="text/css">
			.bg1 {background:url(images/bg.jpg) top center repeat-y, #ebebed;}
		</style>
	</head>
	<body>
        nothing
	</body>
</html>
EOT;
        $background =<<<EOT
<html>
	<head>
		<meta content="text/html; charset=utf-8" http-equiv="Content-Type">
		<title>Mailing</title>
	</head>
	<body>
        <a href="#" background="images/bg.jpg" /></a>
	</body>
</html>
EOT;
        return array(
            array($image), array($cssBackground), array($background)
        );
    }
}

