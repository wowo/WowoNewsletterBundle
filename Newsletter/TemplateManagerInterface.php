<?php

namespace Wowo\Bundle\NewsletterBundle\Newsletter;

use Doctrine\ORM\EntityManager;
use Wowo\Bundle\NewsletterBundle\Entity\Mailing;
use Symfony\Component\HttpFoundation\Request;
use Wowo\Bundle\NewsletterBundle\Newsletter\MailingMedia;

interface TemplateManagerInterface
{
    public function setAvailableTemplates(array $templates);
    public function getAvailableTemplates();
    public function applyTemplate($body, $title);
}
