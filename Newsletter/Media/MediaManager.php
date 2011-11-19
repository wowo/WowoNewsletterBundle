<?php

namespace Wowo\Bundle\NewsletterBundle\Newsletter\Media;

/**
 * MediaManager 
 * 
 * @uses MediaManagerInterface
 * @package default
 * @version $id$
 * @copyright 
 * @author Wojciech Sznapka <wojciech@sznapka.pl> 
 * @license 
 */
class MediaManager implements MediaManagerInterface
{
    protected $regex = array(
        'src' => '#<img.*?src="(.*?)"#im',
        'background' => '#background:url\((.*?)\)#im',
        'background_attribute' => '#background="(.*?)"#im',
    );

    /**
     * embedMedia 
     * 
     * @param mixed $body 
     * @param \Swift_Message $message 
     * @access public
     * @return void
     */
    public function embed($body, \Swift_Message $message)
    {
        $body = $this->embedInlineContent($body, $message, $this->getRegex('src'));
        $body = $this->embedInlineContent($body, $message, $this->getRegex('background'));
        $body = $this->embedInlineContent($body, $message, $this->getRegex('background_attribute'));
        return $body;
    }

    public function getRegex($name)
    {
        if (!isset($this->regex[$name])) {
            throw new \InvalidArgumentException($name . ' regex does not exists');
        }
        return $this->regex[$name];
    }

    /**
     * We assume that urls in template are absolute!
     * 
     * @param mixed $body 
     * @param mixed $message 
     * @param mixed $regex 
     * @access protected
     * @return void
     */
    protected function embedInlineContent($body, $message, $regex)
    {
        return preg_replace_callback($regex, 
            function ($matches) use ($message) {
                $cid = $message->embed(\Swift_Image::fromPath($matches[1]));
                return str_replace($matches[1], $cid, $matches[0]);
            }, $body);
    }
}
