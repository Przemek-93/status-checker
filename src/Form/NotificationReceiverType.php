<?php

declare(strict_types=1);

namespace App\Form;

use App\Entity\NotificationReceiver;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class NotificationReceiverType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder->add('email',EmailType::class, [
            'required' => true,
            'label' => false,
            'attr' => ['placeholder' => 'e.g. test@syngeos.pl']
        ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => NotificationReceiver::class,
        ]);
    }
}
