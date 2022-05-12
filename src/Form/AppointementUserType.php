<?php

namespace App\Form;


use App\Entity\Bien;
use App\Entity\Appointement;
use Doctrine\ORM\EntityRepository;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Security\Core\Security;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Validator\Constraints\Email;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TelType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;

class AppointementUserType extends AbstractType
{
    private $security;

    public function __construct(Security $security)
    {
        $this->security = $security;
    }

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
            // ->add('titre', EntityType::class,[

            //     'class'=> Bien::class,
            //     'multiple'=>false,
            //     'choice_label'=>'titre'
            // ])
            ;
            
            $user = $this->security->getUser();
        //     if (!$user) {
        //             throw new \LogicException(
        //                 'On ne peut pas recuperer les bien si on n\'a pas defeni l\employer connecter'
        //     );
        // } 
        $builder->addEventListener(FormEvents::PRE_SET_DATA, function (FormEvent $event) use ($user) {
            // if (null !== $event->getData()) {
            //     // we don't need to add the friend field because
            //     // the message will be addressed to a fixed friend
            //     return;
            // }

            $formm = $event->getForm();
            
            $formm->add('titre', EntityType::class,[

                'class'=> Bien::class,
                'multiple'=>false,
                'choice_label'=>'titre',
                'query_builder' =>function(EntityRepository $er) use ($user){
                    return $er->createQueryBuilder('a')
                    ->andWhere('a.employeur = :val')
                    ->setParameter('val', $user);
                 
                }

            ]);
        });
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Appointement::class,
        ]);
    }
}
