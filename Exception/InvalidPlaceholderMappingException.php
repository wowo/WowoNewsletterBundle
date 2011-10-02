<?php

namespace Wowo\Bundle\NewsletterBundle\Exception;

class InvalidPlaceholderMappingException extends \Exception
{
    const NON_PUBLIC_PROPERTY = 1;
    const NON_PUBLIC_METHOD = 2;
    const UNABLE_TO_MAP = 3;
}
