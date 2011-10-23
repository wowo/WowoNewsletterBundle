<?php

namespace Wowo\Bundle\NewsletterBundle\Newsletter;

class MailingMedia
{
    public function embedMedia($body)
    {
        $body = $this->embedImages($body);
        $body = $this->embedFiles($body);
        return $body;
    }

    protected function embedImages($body)
    {
        return $body;
    }

    protected function embedFiles($body)
    {
        return $body;
    }
}
