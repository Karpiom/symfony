<?php

namespace AppBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class RegisterForm extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
			->add('firstName', 'text', array(
				'label' => 'Imię'
			))
			->add('lastName', 'text', array(
				'label' => 'Nazwisko'
			))
            ->add('email', 'email')
            ->add('plainPassword', 'repeated', array(
                'type' => 'password',
                'first_options'  => array('label' => 'Hasło'),
                'second_options' => array('label' => 'Powtórz hasło'),
            )
        );
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'AppBundle\Entity\User'
        ));
    }

    public function getName()
    {
        return 'user';
    }
}