<?php

namespace Wowo\NewsletterBundle\Newsletter\Placeholders;

interface PlaceholderProcessorInterface
{
    public function process($object, $body);
}
