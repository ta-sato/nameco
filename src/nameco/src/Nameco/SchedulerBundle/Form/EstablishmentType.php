<?php

namespace Nameco\SchedulerBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;

class EstablishmentType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('name')
            ->add('area', 'entity', array(
				'class' => 'NamecoSchedulerBundle:Area',
                'property' => 'name',
				'required' => false
                ))
			;
    }

    public function getName()
    {
        return 'nameco_schedulerbundle_establishmenttype';
    }
}
