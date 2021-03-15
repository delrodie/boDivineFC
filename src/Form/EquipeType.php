<?php

namespace App\Form;

use App\Entity\Equipe;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\File;

class EquipeType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('nom', TextType::class,[
                'attr'=>['class'=>"form-control", 'autocomplete'=>"off"],
                'label' => "Nom & prenoms"
            ])
            ->add('fonction', TextType::class,[
                'attr'=>['class'=>"form-control", 'autocomplete'=>"off"],
                'label' => "Fonction"
            ])
            ->add('email', EmailType::class,[
                'attr'=>['class'=>"form-control", 'autocomplete'=>"off"],
                'label' => "Adresse email"
            ])
            ->add('biographie', TextareaType::class,[
                'attr'=>['class'=>"form-control", 'rows'=>"7"],
                'label'=> "Biographie"
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
            ->add('facebook', TextType::class,[
                'attr'=>['class'=>"form-control", 'autocomplete'=>"off"],
                'label' => "Compte Facebook",
                'required' => false
            ])
            ->add('twitter', TextType::class,[
                'attr'=>['class'=>"form-control", 'autocomplete'=>"off"],
                'label' => "Compte Twitter",
                'required' => false
            ])
            ->add('linkedin', TextType::class,[
                'attr'=>['class'=>"form-control", 'autocomplete'=>"off"],
                'label' => "Compte LinkedIn",
                'required' => false
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Equipe::class,
        ]);
    }
}
