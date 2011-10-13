<?php

namespace Wowo\Bundle\NewsletterBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilder;
use Wowo\Bundle\NewsletterBundle\Entity\Mailing;

class NewsletterType extends AbstractType
{
    public function buildForm(FormBuilder $builder, array $options)
    {
        $builder
            ->add('mailing', new MailingType(), array('label' => ' ', 'data' => @$options['data']['mailing']))
            ->add('contacts', 'choice', array(
                'label'    => 'Recipients',
                'multiple' => true,
                'expanded' => false,
                'required' => true,
                'choices'  => $options['data']['availableContacts']))
        ;
    }

    public function getName()
    {
        return 'wowo_bundle_newsletterbundle_newslettertype';
    }
}

