<?php

namespace App\Controller;



use App\Entity\Bien;
use App\Entity\User;
use App\Form\EditUserType;
use App\Form\RechercheFormType;
use App\Security\EmailVerifier;
use PhpParser\Node\Stmt\Foreach_;
use App\Form\RegistrationFormType;
use App\Repository\BienRepository;
use App\Repository\UserRepository;
use Symfony\Component\Mime\Address;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Contracts\Translation\TranslatorInterface;
use Symfony\Component\Security\Core\User\UserInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use SymfonyCasts\Bundle\VerifyEmail\Exception\VerifyEmailExceptionInterface;

class RegistrationController extends AbstractController
{
    private EmailVerifier $emailVerifier;

    public function __construct(EmailVerifier $emailVerifier)
    {
        $this->emailVerifier = $emailVerifier;
    }

    
    #[Route('/register', name: 'app_register')]
    #[IsGranted(data:'ROLE_ADMIN', message: "Vous n'avez pas les autorisations nécessaires", statusCode: 403)]
    public function register(ManagerRegistry $manager, Request $request, UserPasswordHasherInterface $userPasswordHasher, EntityManagerInterface $entityManager, UserInterface $userConnecte): Response
    {
        //filtre de recherche
        $filteredBiens = array();
        $formRecherche = $this->createForm(RechercheFormType::class);
        $formRecherche->handleRequest($request);
        if ($formRecherche->isSubmitted() && $formRecherche->isValid()) {
            //ici on recupere l'information du champs
            $searchWord = $formRecherche->get('searchWord')->getData();
            //on recupere les resultats obrenus et ça sera dans un array
            $filteredBiens = $manager->getRepository(Bien::class)->findWithSearchword($searchWord);
        }
        //recuperer le filtre de l'administartion du href
        $administration = $request->query->get("administration");
        if(!$administration){
             $administration = 'Employés';
        }

        // $this->denyAccessUnlessGranted('ROLE_ADMIN');
        $user = new User();
        $form = $this->createForm(RegistrationFormType::class, $user);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            // encode the plain password
            $user->setPassword(
            $userPasswordHasher->hashPassword(
                    $user,
                    $form->get('plainPassword')->getData()
                )
            );

            $entityManager->persist($user);
            $entityManager->flush();

            // generate a signed url and email it to the user
            $this->emailVerifier->sendEmailConfirmation('app_verify_email', $user,
                (new TemplatedEmail())
                    ->from(new Address('agence@agence.com', 'Agency'))
                    ->to($user->getEmail())
                    ->subject('Please Confirm your Email')
                    ->htmlTemplate('registration/confirmation_email.html.twig')
            );
            // do anything else you need here, like send an email
            $this->addFlash("success", "L'utilisateur a été ajouté avec succés");

            return $this->redirectToRoute('administration', ['administration' => 'Employés']);
        }

        return $this->render('registration/register.html.twig', [
            'registrationForm' => $form->createView(),
            'administration' => $administration,
            'user' => $userConnecte,
            //filtre de recherche
            'filteredBiens' => $filteredBiens,
            'formRecherche' => $formRecherche->createView(),
        ]);
    }

    #[Route('/verify/email', name: 'app_verify_email')]
    public function verifyUserEmail(Request $request, TranslatorInterface $translator): Response
    {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');

        // validate email confirmation link, sets User::isVerified=true and persists
        try {
            $this->emailVerifier->handleEmailConfirmation($request, $this->getUser());
        } catch (VerifyEmailExceptionInterface $exception) {
            $this->addFlash('verify_email_error', $translator->trans($exception->getReason(), [], 'VerifyEmailBundle'));

            return $this->redirectToRoute('app_register');
        }

        // @TODO Change the redirect on success and handle or remove the flash message in your templates
        $this->addFlash('success', 'Your email address has been verified.');

        return $this->redirectToRoute('app_register');
    }

    #[Route('/register/{id}/update', name: "user_update",methods: ['GET', 'POST'], requirements: ['id' => "[0-9]+"])]
    #[IsGranted(data:'ROLE_ADMIN', message: "Vous n'avez pas les autorisations nécessaires", statusCode: 403)]
    public function update(ManagerRegistry $manager, User $user,Request $request, UserInterface $userConnecte): Response
    {
        //filtre de recherche
        $filteredBiens = array();
        $formRecherche = $this->createForm(RechercheFormType::class);
        $formRecherche->handleRequest($request);
        if ($formRecherche->isSubmitted() && $formRecherche->isValid()) {
            //ici on recupere l'information du champs
            $searchWord = $formRecherche->get('searchWord')->getData();
            //on recupere les resultats obrenus et ça sera dans un array
            $filteredBiens = $manager->getRepository(Bien::class)->findWithSearchword($searchWord);
        }
        //recuperer le filtre de l'administartion du href
        $administration = $request->query->get("administration");
        if(!$administration){
             $administration = 'Employés';
        }

        $form = $this->createForm(EditUserType::class, $user);
        //on recupere la requete
        $form->handleRequest($request);
        if($form->isSubmitted() && $form->isValid()){
            $em = $manager->getManager();
            $em->persist($user);
            $em->flush();

            $this->addFlash('success', "l'utilisateur a été modifié avec succès");

            return $this->redirectToRoute('administration', ['administration'=>'Employés']);
        }

        return $this->render('admin/edituser.html.twig', [
            // 'registrationForm' => $form->createView(),
            'userForm'  => $form->createView(),
            'administration' => $administration,
            'user' => $userConnecte,
            //filtre de recherche
            'filteredBiens' => $filteredBiens,
            'formRecherche' => $formRecherche->createView(),
        ]);
    }
    
    #[Route('/employeurs/{id}/delete', name: "user_delete",methods: ['POST'], requirements: ['id' => "[0-9]+"])]
    #[IsGranted(data:'ROLE_ADMIN', message: "Vous n'avez pas les autorisations nécessaires", statusCode: 403)]
    public function delete(Request $request, User $user, UserRepository $userRepository, UserInterface $admin)
    {
        if ($this->isCsrfTokenValid('delete' . $user->getId(), $request->request->get('_token'))) {
            
        
            //on recupere tout les biens qui sont attachés au user à supprimer
           $allBiensUser = $user->getBiens();
           //on les parcours 
           foreach( $allBiensUser as $unBien){
                //on les attache avec l'admin
                $unBien->setEmployeur($admin);
           }
           
            $userRepository->remove($user);
        }

        $this->addFlash("success", "L'utilisateur a été supprimé avec succés");
        return $this->redirectToRoute('administration', ['administration'=>'Employés'], Response::HTTP_SEE_OTHER);
       
    }

}
