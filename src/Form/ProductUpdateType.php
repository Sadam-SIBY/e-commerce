<?php

namespace App\Form;

use App\Entity\Product;
use App\Entity\SubCategory;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Validator\Constraints\File;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\FileType;

class ProductUpdateType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('name')
            ->add('description')
            ->add('price')
            ->add('image', FileType::class,[
                'label' => 'image de produit',
                'mapped'=>false,
                'required' => false,
                'constraints' => [
                    new File([
                        "maxSize"=> "1024k",
                        "mimeTypes"=>[
                            'image/jpg',
                            'image/png',
                            'image/jpeg',
                        ],
                        "maxSizeMessage"=> "Votre image ne doit pas deppasez les 1024",
                        "mimeTypesMessage"=> "Votre image doit être de format valide (Jpg, png, jepg)"
                    ])
                ]
                    
                
            ])
            // ->add('Stock')
            ->add('subCategories', EntityType::class, [
                'class' => SubCategory::class,
                'choice_label' => 'name',
                'multiple' => true,
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Product::class,
        ]);
    }
}
