<?php

namespace App\Form;

use App\Entity\User;
use App\Entity\Option;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;

class BienType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('titre', TextType::class, [
                'label' => "Titre"
                ])
            ->add('nbrepieces' , TextType::class, [
                'label' => "Nombre des piéces"
                ])
            ->add('surface',  TextType::class, [
                'label' => "Surface"
                ])
            ->add('prix',  TextType::class, [
                'label' => "Prix"
                ])
            ->add('localisation',  TextType::class, [
                'label' => "localisation"
                ])
            ->add('type',  TextType::class, [
                'label' => "Type"
                ])
            ->add('etage',  TextType::class, [
                'label' => "L'étage"
                ])
            ->add('transactiontype',  TextType::class, [
                'label' => "Transaction_type"
                ])
            ->add('description', TextareaType::class, [
                'label' => "Description"
            ])
            ->add('dateconstruction', DateType::class,[
                'label' => "Date de construction"
            ])
            ->add('photos', FileType::class,[
                'label' => false,
                'multiple' => true,
                'mapped' => false,
                'required' => false
            ])
            ->add('options', EntityType::class, array(
                 'class' => Option::class,
                 'choice_label' => 'designation',
                 'expanded' => true,
                 'multiple' => true,
                 'mapped' => false
             ))
            ->add('employeur', EntityType::class, array(
                'class' => User::class,
                'choice_label' => 'id'
            ));
            
        
    }


    

}
