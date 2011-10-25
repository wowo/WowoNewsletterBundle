<?php

namespace Wowo\Bundle\NewsletterBundle\Newsletter;

/**
 * MailingMedia 
 * 
 * @package default
 * @version $id$
 * @copyright 
 * @author Wojciech Sznapka <wojciech@sznapka.pl> 
 * @license 
 */
class MailingMedia
{
    const REGEX_SRC = '#<img.*?src="(.*?)"#im';
    const REGEX_BACKGROUND = '#background:url\((.*?)\)#im';

    /**
     * embedMedia 
     * 
     * @param mixed $body 
     * @param \Swift_Message $message 
     * @access public
     * @return void
     */
    public function embedMedia($body, \Swift_Message $message)
    {
        $body = $this->embedInlineContent($body, $message, self::REGEX_SRC);
        $body = $this->embedInlineContent($body, $message, self::REGEX_BACKGROUND);
        return $body;
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
