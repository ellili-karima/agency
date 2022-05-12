<?php

namespace App\Form;


use App\Entity\Bien;
use App\Entity\Appointement;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Validator\Constraints\Email;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TelType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;

class AppointementType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('nom', TextType::class, [
                'label' => "Nom",
                'attr' => ['placeholder' => 'Nom']
            ])
            ->add('prenom', TextType::class, [
                'label' => "Prenom",
                'attr' => ['placeholder' => 'Prenom']
            ])
            
            ->add('email', EmailType::class, [
                'attr' => ['placeholder' => 'Email'],
                'constraints' => [
                    new NotBlank(['message' => 'Merci de bien saisir un email valide']),
                    new Email(['message' => "Votre email n'est pas valide"])
                ]
            ] )
            ->add('tel', TelType::class,[
                'label' => "Tel",
                'attr' => ['placeholder' => 'Téléphone']
            ])
            ->add('date', DateTimeType::class, [
                'label' => "Date de rendez-vous"
            ])
            ->add('titre', EntityType::class, array(
                'class' => Bien::class,
                'choice_label' => 'Titre',
            ))
            
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Appointement::class,
        ]);
    }
}
