<?php

namespace Wowo\Bundle\NewsletterBundle\Newsletter\Model;

use Wowo\Bundle\NewsletterBundle\Newsletter\Templates\TemplateManagerInterface;

/**
 * Mailing Manager is used to create mailing depends on form submited by suer 
 * 
 * @uses AbstractManager
 * @uses MailingManagerInterface
 * @package default
 * @version $id$
 * @copyright 
 * @author Wojciech Sznapka <wojciech@sznapka.pl> 
 * @license 
 */
class MailingManager extends AbstractManager implements MailingManagerInterface
{
    /**
     * template manager 
     */
    protected $templateManager;

    /**
     * Template Manager setter
     * 
     * @param TemplateManagerInterface $templateManager 
     * @return void
     */
    public function setTemplateManager(TemplateManagerInterface $templateManager)
    {
        $this->templateManager = $templateManager;
    }

    /**
     * Creates mailing database entry depeding on form and values choosen by user
     * 
     * @param mixed $form 
     * @param int $contactCount 
     * @return Mailing
     */
    public function createMailingBasedOnForm($form, $contactCount)
    {
        $data = $form->getData();
        $mailing = $data['mailing'];
        $body = $this->templateManager->applyTemplate($mailing->getBody(), $mailing->getTitle());

        $mailing->setBody($body);
        $mailing->setTotalCount($contactCount);
        $mailing->setSentCount(0);
        $mailing->setErrorsCount(0);
        $this->em->persist($mailing);
        $this->em->flush();
        return $mailing;
    }
}
