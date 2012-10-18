<?php

namespace Wowo\NewsletterBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;

class MailingType extends AbstractType
{
    protected $hasDelayedSending = true;

    public function setHasDelayedSending($value)
    {
        $this->hasDelayedSending = $value;
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('senderName', null, array('label' => 'Sender name'))
            ->add('senderEmail', null, array('label' => 'Sender e-mail'))
            ->add('title')
            ->add('body', 'textarea', array('required' => false));
        if ($this->hasDelayedSending) {
            $builder
                ->add('delayedMailing', null, array('label' => 'Is delayed?', 'required' => false))
                ->add('sendDate', 'datetime', array('label' => 'Send date', 'required' => false));
        }
    }

    public function getName()
    {
        return 'wowo_bundle_newsletterbundle_mailingtype';
    }

    public function getDefaultOptions(array $options)
    {
        return array(
            'data_class' => 'Wowo\NewsletterBundle\Entity\Mailing',
        );
    }
}
