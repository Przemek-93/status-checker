<?php

declare(strict_types=1);

namespace App\Form;

use App\Entity\Notification;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\UrlType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\OptionsResolver\OptionsResolver;

class NotificationType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('url', UrlType::class, ['attr' => ['placeholder' => 'e.g. https://syngeos.api.pl/']])
            ->add('type', ChoiceType::class, [
                'choices' => ['Email' => Notification::NOTIFICATION_EMAIL_TYPE]
            ])
            ->add('httpMethod', ChoiceType::class, [
                'choices' => Notification::HTTP_FORM_METHODS
            ])
            ->add('sendingFrequency', IntegerType::class, [
                'attr' => ['placeholder' => 'Every how many hours to check? e.g. 10']
            ])
            ->add('receivers', CollectionType::class, [
                    'entry_type' => NotificationReceiverType::class,
                    'allow_add' => true,
                    'allow_delete' => true,
                    'prototype' => true,
                    'required' => true,
                    'label' => false
                ]
            )
            ->add('isActive', ChoiceType::class, [
                'choices' => [
                    'True' => true,
                    'False' => false,
                ]
            ])
            ->addEventListener(FormEvents::SUBMIT, function (FormEvent $event) {
                $notification = $event->getData();
                foreach($notification->getReceivers() as $receiver) {
                    $receiver->setNotification($notification);
                }
            });
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Notification::class,
        ]);
    }
}