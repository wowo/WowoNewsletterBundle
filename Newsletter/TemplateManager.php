<?php

namespace Wowo\Bundle\NewsletterBundle\Newsletter;

class TemplateManager implements TemplateManagerInterface
{
    protected $availableTemplates = array();
    protected $templateContentTag = '{{ content }}';
    protected $templateTitleTag   = '{{ title }}';

    /**
     * Sets available templates
     * 
     * @param array $templates 
     * @return void
     */
    public function setAvailableTemplates(array $templates)
    {
        $this->availableTemplates = $templates;
    }

    /**
     * Get templates from database or config file
     *
     * @return array
     */
    public function getAvailableTemplates()
    {
        return $this->availableTemplates;
    }

    /**
     * Applies template, which means that it surrounds body with master template
     * and makes paths to images absolute, that makes they easy to embed into email
     * 
     * @param string $body 
     * @param string $title 
     * @return void
     */
    public function applyTemplate($body, $title)
    {
        $path = current($this->availableTemplates);
        $tpl = file_get_contents($path);
        $tpl = str_replace(
            array($this->templateContentTag, $this->templateTitleTag),
            array($body, $title),
            $tpl);
        $tpl = $this->makeAbsolutePaths($tpl, dirname($path), MailingMedia::REGEX_SRC);
        $tpl = $this->makeAbsolutePaths($tpl, dirname($path), MailingMedia::REGEX_BACKGROUND);
        $tpl = $this->makeAbsolutePaths($tpl, dirname($path), MailingMedia::REGEX_BACKGROUND_ATTRIBUTE);
        return $tpl;
    }

    /**
     * Makes absolute paths in template based on given regex
     * 
     * @param string $template 
     * @param string $path 
     * @param string $regex 
     * @access protected
     * @return string
     */
    protected function makeAbsolutePaths($template, $path, $regex)
    {
        return preg_replace_callback($regex, 
            function ($matches) use ($template, $path) {
                return str_replace($matches[1], $path . '/' . $matches[1], $matches[0]);
            }, $template);
    }
}
