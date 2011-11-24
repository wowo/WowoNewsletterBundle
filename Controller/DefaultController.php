<?php

namespace Wowo\Bundle\NewsletterBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Wowo\Bundle\NewsletterBundle\Form\NewsletterType;
use Wowo\Bundle\NewsletterBundle\Newsletter\Newsletter;
use Wowo\Bundle\NewsletterBundle\Newsletter\Model\ContactManagerInterface;

class DefaultController extends Controller
{
    /**
     * @Route("/mailing")
     * @Template()
     */
    public function createMailingAction($submitCssClass = '')
    {
        $contactManager = $this->get('wowo_newsletter.contact_manager');
        $mailingManager = $this->get('wowo_newsletter.mailing_manager');
        $form = $this->getForm($contactManager);

        if ('POST' == $this->get('request')->getMethod()) {
            $form->bindRequest($this->getRequest());
            if ($form->isValid()) {
                try {
                    $contactIds = $contactManager->findChoosenContactIdForMailing($form);
                    $mailing    = $mailingManager->createMailingBasedOnForm($form, count((array)$contactIds));
                    $contactClass = $this->container->getParameter('wowo_newsletter.model.contact.class');
                    $this->get('wowo_newsletter.spooler')->spoolManyContacts($mailing, $contactIds, $contactClass);

                    $msg= $this
                        ->get('translator')
                        ->trans(
                            'Mailing to %count% recipients has been enqueued for sending',
                            array('%count%' => count($contactIds))
                        );
                    $this->get('session')->setFlash('notice', $msg);
                } catch (\Exception $e) {
                    $this->get('logger')->err(get_class($e) . ' ' . $e->getMessage());
                    $msg = $this
                        ->get('translator')
                        ->trans($e->getMessage());
                    $this->get('session')->setFlash('error', $msg);
                }
                return $this->redirect($this->generateUrl('wowo_newsletter_default_createmailing'));
            }
        }
        return array(
            'form' => $form->createView(),
            'templates' => $this->get('wowo_newsletter.template_manager')->getAvailableTemplates(),
            'submitCssClass' => $submitCssClass,
        );
    }

    /**
     * Builds form for this controller
     * 
     * @param ContactManagerInterface $contactManager 
     * @access protected
     * @return void
     */
    protected function getForm(ContactManagerInterface $contactManager)
    {
        $newsletter = $this->get('wowo_newsletter.empty_newsletter');
        return $this->createForm($this->get('wowo_newsletter.form.newsletter'), $newsletter,
            array('data' => array(
                'availableContacts' => $contactManager->findContactToChooseForMailing(),
                'mailing' => $newsletter->mailing,
            ))
        );
    }
}
