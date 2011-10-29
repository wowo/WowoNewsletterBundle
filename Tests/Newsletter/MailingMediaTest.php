<?php

namespace Wowo\Bundle\NewsletterBundle\Tests\Newsletter;

use lapistano\ProxyObject\ProxyObject;
use Wowo\Bundle\NewsletterBundle\Newsletter\MailingMedia;

class MailingMediaTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @dataProvider bodyProvider
     */
    public function testEmbedMedia($body)
    {
        $msg  = \Swift_Message::newInstance('testing subject');
        $mailingMedia = new MailingMedia($msg);

        $this->assertNotNull($body);
        $bodyWithMedia = $mailingMedia->embedMedia($body, $msg);

        $this->assertNotEquals($body, $bodyWithMedia);
        $this->assertRegexp('#cid:.*?@swift.generated#', $bodyWithMedia);
        $this->assertNotRegexp('#cid:.*?@swift.generated#', $body);
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

