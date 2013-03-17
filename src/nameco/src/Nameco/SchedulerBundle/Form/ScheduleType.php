<?php

namespace Nameco\SchedulerBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

/**
 * スケジュールフォーム
 * @author yu-ito
 */
class ScheduleType extends AbstractType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('startDateTime', 'datetime', array('label' => '開始', 'invalid_message' => '日付の形式が違います', 'date_format' => 'yyyy/M/d', 'date_widget' => 'single_text', 'time_widget' => 'choice', 'minutes' => array(0, 15, 30, 45)))
            ->add('endDateTime', 'datetime', array('label' => '終了', 'invalid_message' => '日付の形式が違います', 'date_format' => 'yyyy/M/d', 'date_widget' => 'single_text', 'time_widget' => 'choice', 'minutes' => array(0, 15, 30, 45)))
            ->add('title', 'text', array('label' => 'タイトル'))
            ->add('detail', 'textarea', array('label' => '説明'))
            ->add('out', null, array('required' => false, 'label' => '外出'))
            ->add('user')
            ->add('establishment', null, array('required' => false))
        ;
    }

    public function setDefualtOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'Nameco\SchedulerBundle\Entity\Schedule'
        ));
    }

    public function getName()
    {
        return 'schedule';
    }
}
