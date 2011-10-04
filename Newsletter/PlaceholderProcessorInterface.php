<?php

namespace Wowo\Bundle\NewsletterBundle\Newsletter;

interface PlaceholderProcessorInterface
{
    public function process($object, $body);
}
