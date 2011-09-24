<?php

namespace Wowo\Bundle\NewsletterBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilder;

class MailingType extends AbstractType
{
    public function buildForm(FormBuilder $builder, array $options)
    {
        $builder
            ->add('senderEmail', null, array("label" => "Sender e-mail"))
            ->add('senderName', null, array("label" => "Sender name"))
            ->add('title')
            ->add('body')
            ->add('sendDate', null, array("label" => "Send date"))
        ;
    }

    public function getName()
    {
        return 'wowo_bundle_newsletterbundle_mailingtype';
    }
}
