<?php

namespace Wowo\Bundle\NewsletterBundle\Newsletter\Templates;

interface TemplateManagerInterface
{
    public function setAvailableTemplates(array $templates);
    public function getAvailableTemplates();
    public function applyTemplate($body, $title);
}
