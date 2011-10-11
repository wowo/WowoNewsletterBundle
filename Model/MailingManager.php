<?php

namespace Wowo\Bundle\NewsletterBundle\Model;

use Doctrine\ORM\EntityManager;
use Wowo\Bundle\NewsletterBundle\Entity\Mailing;
use Symfony\Component\HttpFoundation\Request;

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
        // TODO podmiana obrazków!
        return $template;
    }
}
