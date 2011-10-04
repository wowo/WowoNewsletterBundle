<?php

namespace Wowo\Bundle\NewsletterBundle\Exception;

use Wowo\Bundle\NewsletterBundle\Exception\NewsletterException;

class InvalidPlaceholderMappingException extends NewsletterException
{
    const NON_PUBLIC_PROPERTY = 1;
    const NON_PUBLIC_METHOD = 2;
    const UNABLE_TO_MAP = 3;
}
