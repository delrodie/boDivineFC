<?php

namespace App\Form;

use App\Entity\Metier;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\File;

class MetierType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('titre', TextType::class,[
                'attr'=>['class'=>"form-control", 'placeholder'=>"Le titre du metier", 'autocomplete'=>"off"],
                'label' => "Le titre"
            ])
            ->add('contenu', TextareaType::class,[
                'attr'=>['class'=>"form-control", 'rows'=>7],
                'label' => "La description du metier"
            ])
            ->add('resume', TextareaType::class,[
                'attr'=>['class'=>"form-control", 'rows'=>3],
                'label' => "Le resumé de la description",
                'required' => false
            ])
            ->add('media', FileType::class,[
                'attr'=>['class'=>"custom-file-input", 'data-preview' => ".preview"],
                'label' => "Télécharger la photo",
                'mapped' => false,
                'multiple' => false,
                'constraints' => [
                    new File([
                        'maxSize' => "1000000k",
                        'mimeTypes' =>[
                            'image/png',
                            'image/jpeg',
                            'image/jpg',
                            'image/gif',
                            'image/webp',
                        ],
                        'mimeTypesMessage' => "Votre fichier doit être de type image"
                    ])
                ],
                'required' => false
            ])
            ->add('statut', CheckboxType::class,['attr'=>['class'=>"custom-control-input"],'required'=>false])
            //->add('slug')
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Metier::class,
        ]);
    }
}
