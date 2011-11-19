<?php

namespace Wowo\Bundle\NewsletterBundle\Newsletter\Templates;

use Wowo\Bundle\NewsletterBundle\Exception\NonExistingTemplateException;
use Wowo\Bundle\NewsletterBundle\Newsletter\Media\MediaManagerInterface;

class TemplateManager implements TemplateManagerInterface
{
    protected $mediaManager;
    protected $availableTemplates = array();
    protected $templateRegex      = array();
    protected $templateContentTag = '{{ content }}';
    protected $templateTitleTag   = '{{ title }}';

    public function __construct(MediaManagerInterface $mediaManager)
    {
        $this->mediaManager = $mediaManager;
    }

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

    public function setTemplateRegex(array $regex)
    {
        $this->templateRegex = $regex;
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
        $path = $this->getActiveTemplatePath();
        $tpl  = $this->getActiveTemplateBody();
        $tpl = str_replace(
            array($this->templateContentTag, $this->templateTitleTag),
            array($body, $title),
            $tpl);
        foreach ($this->templateRegex as $regex) {
            $tpl = $this->makeAbsolutePaths($tpl, dirname($path), $this->mediaManager->getRegex($regex));
        }
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

    /**
     * Get active template path
     * 
     * @return string
     */
    protected function getActiveTemplatePath()
    {
        return current($this->availableTemplates);
    }

    /**
     * Get active template body
     * 
     * @return string
     */
    protected function getActiveTemplateBody()
    {
        $path = $this->getActiveTemplatePath();
        if (!$path || !file_exists($path)) {
            throw new NonExistingTemplateException (sprintf('Template "%s" does not exists or was not set', $path));
        }
        return file_get_contents($path);
    }
}
