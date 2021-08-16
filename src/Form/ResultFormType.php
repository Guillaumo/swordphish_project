<?php

namespace App\Form;

use App\Entity\ResultCampaignUser;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TelType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ResultFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('lastname', TextType::class, ['label' => 'Nom'])
            ->add('firstname',TextType::class, ['label' => 'Prénom'])
            ->add('telephone', TelType::class, ['label' => 'Téléphone'])
            ->add('email', EmailType::class)
            ->add('tickets_number', NumberType::class, ['label' =>'Nombre de tickets', 'mapped' => false])
            ->add('submit', SubmitType::class,['label' => 'Participer !', 'attr' => ['class' => 'btn btn-success']])
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => ResultCampaignUser::class,
        ]);
    }
}
