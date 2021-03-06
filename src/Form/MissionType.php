<?php

namespace App\Form;

use App\Entity\Mission;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class MissionType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('titre', TextType::class,[
                'attr'=>['class'=>"form-control", 'placeholder'=>"Le titre de la mission", 'autocomplete'=>"off"],
                'label' => "Le titre",
            ])
            ->add('contenu',TextareaType::class,[
                'attr'=>['class'=>'form-control', "rows"=>"7"],
                'label' => "La description de la mission"
            ])
            ->add('statut', CheckboxType::class,['attr'=>['class'=>"custom-control-input"],'required'=>false])
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Mission::class,
        ]);
    }
}
