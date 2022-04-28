<?php

namespace App\Form;

use App\Data\SearchData;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\NumberType;

class SearchFormType extends AbstractType
{

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('minpiece', NumberType::class, [
                'label' => 'Pièce min',
                'required' => false,
                'attr' => [
                    'placeholder' => 'Piece min'
                ]
                
            ])
            ->add('maxpiece', NumberType::class, [
                'label' => 'Pièce max',
                'required' => false,
                'attr' => [
                    'placeholder' => 'Piece max'
                ]
                
            ])

            ->add('minsurface', NumberType::class, [
                'label' => 'Surface min',
                'required' => false,
                'attr' => [
                    'placeholder' => 'Surface min'
                ]
                
            ])
            ->add('maxsurface', NumberType::class, [
                'label' => 'Surface max',
                'required' => false,
                'attr' => [
                    'placeholder' => 'Surface max'
                ]
                
            ])
            ->add('minprix', NumberType::class, [
                'label' => 'Prix min',
                'required' => false,
                'attr' => [
                    'placeholder' => 'Prix min'
                ]
                
            ])
            ->add('maxprix', NumberType::class, [
                'label' => 'Prix max',
                'required' => false,
                'attr' => [
                    'placeholder' => 'Prix max'
                ]
                
                ]);
            
            
    }
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => SearchData::class,
            'method' => 'GET',
            'csrf_protection' => false
        ]);
        
    }

    public function getBlockPreFix(){
        return '';
    }

   
}
