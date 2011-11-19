<?php

namespace Wowo\Bundle\NewsletterBundle\Newsletter\Placeholders;

interface PlaceholderProcessorInterface
{
    public function process($object, $body);
}
