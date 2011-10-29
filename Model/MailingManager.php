<?php

namespace Wowo\Bundle\NewsletterBundle\Model;

use Doctrine\ORM\EntityManager;
use Wowo\Bundle\NewsletterBundle\Entity\Mailing;
use Symfony\Component\HttpFoundation\Request;
use Wowo\Bundle\NewsletterBundle\Newsletter\MailingMedia;

class MailingManager extends AbstractManager implements MailingManagerInterface
{
    protected $availableTemplates = array();
    protected $templateContentTag = '{{ content }}';
    protected $templateTitleTag   = '{{ title }}';

    public function createMailingBasedOnForm($form, $contactCount)
    {
        $data = $form->getData();
        $mailing = $data['mailing'];
        $mailing->setBody($this->wrapBodyIntoTemplate($mailing));
        $mailing->setTotalCount($contactCount);
        $mailing->setSentCount(0);
        $mailing->setErrorsCount(0);
        $this->em->persist($mailing);
        $this->em->flush();
        return $mailing;
    }

    public function setAvailableTemplates(array $templates)
    {
        $this->availableTemplates = $templates;
    }

    /**
     * Get available templates 
     * Get templates from database or config file
     *
     * @return array
     */
    public function getAvailableTemplates()
    {
        return $this->availableTemplates;
    }

    public function wrapBodyIntoTemplate(Mailing $mailing)
    {
        $path = current($this->availableTemplates);
        $template = file_get_contents($path);
        $template = str_replace(
            array($this->templateContentTag, $this->templateTitleTag),
            array($mailing->getBody(), $mailing->getTitle()),
            $template);
        $template = $this->makeAbsolutePaths($template, dirname($path), MailingMedia::REGEX_SRC);
        $template = $this->makeAbsolutePaths($template, dirname($path), MailingMedia::REGEX_BACKGROUND);
        $template = $this->makeAbsolutePaths($template, dirname($path), MailingMedia::REGEX_BACKGROUND_ATTRIBUTE);
        return $template;
    }

    protected function makeAbsolutePaths($template, $path, $regex)
    {
        return preg_replace_callback($regex, 
            function ($matches) use ($template, $path) {
                return str_replace($matches[1], $path . '/' . $matches[1], $matches[0]);
            }, $template);
    }
}
