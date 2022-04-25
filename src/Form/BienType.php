<?php

namespace App\Form;

use App\Entity\Bien;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class BienType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('titre', TextType::class, [
                'label' => "Nom de la catégorie"
                ])
            ->add('nbrepieces' , integerType::class, [
                'label' => "Nombre des piéces"
                ])
            ->add('surface',  FloatType::class, [
                'label' => "Nom de la catégorie"
                ])
            ->add('prix',  FloatType::class, [
                'label' => "Nom de la catégorie"
                ])
            ->add('localisation',  TextType::class, [
                'label' => "localisation du bien"
                ])
            ->add('type',  TextType::class, [
                'label' => "Type du bien"
                ])
            ->add('etage',  integerType::class, [
                'label' => "L'étage"
                ])
            ->add('transactiontype',  TextType::class, [
                'label' => "Nom de la catégorie"
                ])
            ->add('description', TextareaType::class, [
                'label' => "Description du bien"
            ])
            ->add('dateconstruction', DateType::class,[
                'label' => "Date de construction"
            ])
            ->add('photo', FileEntity::class, [
                'label' => "Photo du bien",
                'mapped' => false,
                'required' => false
            ])
            ->add('submit', SubmitType::class, [
                'label' => 'Ajouter'
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Bien::class,
        ]);
    }
}
