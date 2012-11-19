<?php

namespace Wowo\NewsletterBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilder;
use Wowo\NewsletterBundle\Entity\Mailing;

class NewsletterType extends AbstractType
{
    protected $mailingType;
    protected $canChooseContactsViaForm = true;

    public function setMailingType($type)
    {
        $this->mailingType = $type;
    }

    public function setCanChooseContactsViaForm($value)
    {
        $this->canChooseContactsViaForm = $value;
    }

    public function buildForm(FormBuilder $builder, array $options)
    {
        $builder
            ->add('mailing', $this->mailingType, array('label' => ' ', 'data' => @$options['data']['mailing']));
        if ($this->canChooseContactsViaForm) {
            $builder
                ->add('contacts', 'choice', array(
                    'label'    => 'Recipients',
                    'multiple' => true,
                    'expanded' => false,
                    'required' => true,
                    'choices'  => $options['data']['availableContacts']));
        }
    }

    public function getName()
    {
        return 'wowo_bundle_newsletterbundle_newslettertype';
    }
}

