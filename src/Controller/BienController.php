<?php

namespace App\Controller;

use App\Entity\Bien;
use App\Entity\Photo;
use App\Form\BienType;
use App\Data\SearchData;
use App\Entity\Optionbien;
use App\Entity\Appointement;
use App\Form\SearchFormType;
use App\Form\AppointementType;
use App\Form\RechercheFormType;
use App\Form\AppointementUserType;
use App\Repository\BienRepository;
use App\Repository\UserRepository;
use Doctrine\Persistence\ManagerRegistry;
use App\Repository\AppointementRepository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class BienController extends AbstractController
{
    /**
     * cette fonction retourn les 5 derniers biens enregistrés
     * un filtre pour faciliter la recherchedes biens
     *
     * @param ManagerRegistry $manager
     * @param BienRepository $repository
     * @param Request $request
     * @return Response
     */
    #[Route('/', name: 'accueil')]
    public function index(BienRepository $repository,  Request $request): Response
    {

        //recuperer le filtre du href
        (string)$filtre = $request->query->get("filtre");
        if ($filtre) {
            //si le filtre exixte dans l url=> on affiche les biens filtrés
            $biens = $repository->getfiltre((string)$filtre);
        } else {
            // si non on affiche les 5 derniers biens enregistrés
            $biens = $repository->getLast5();
        }
        return $this->render('bien/home.html.twig', [
            'biens' => $biens,
        ]);
    }

    /**
     * fonction retourne tout les biens enregistrés
     * un filtre qui permet de faciliter la recherche
     * une pagination de 10 biens par page
     * un systheme de tri qui permet de trier les biens
     * par le nbre des pieces , la surface ou le prix
     * 
     * @param ManagerRegistry $manager
     * @param BienRepository $repository
     * @param Request $request
     * @return Response
     */
    #[Route('/biens', name: 'biens')]
    public function search(BienRepository $repository, Request $request): Response
    {
        //on définit le nombre d'éléments par page
        $limit = 10;
        //on récupere le numéro de la page
        $page = (int)$request->query->get("page", 1);
        //on instancie la class SearchData
        $data = new SearchData();
        //on crée un formulaire du filtre
        $form = $this->createForm(SearchFormType::class, $data);
        $form->handleRequest($request);

        //recuperer le triPieces du href
        (string)$triPieces = $request->query->get("triPieces");
        //recuperer le triSurfaces du href
        (string)$triSurfaces = $request->query->get("triSurfaces");
        //recuperer le triPrix du href
        (string)$triPrix = $request->query->get("triPrix");
        if ($form->isSubmitted() && $form->isValid()) {
            // on retourne les biens filtrés par le formulaire du filtre
            $biens = $repository->findSearch($data);
        } elseif ($triPieces) {
            // on retourne les biens triés par le nombre de piéces
            $biens = $repository->triPieces($triPieces);
        } elseif ($triSurfaces) {
            // on retourne les biens triés par la surface
            $biens = $repository->triSurfaces($triSurfaces);
        } elseif ($triPrix) {
            // on retourne les biens triés par le prix
            $biens = $repository->triPrix($triPrix);
        } else {
            // on retourne tout les biens et on les limite de 10 biens par page
            $biens = $repository->getPaginatedBiens($page, $limit);
        }
        // on récupere le nombre total du biens
        $total = $repository->getTotalBiens();
        // on récupere le nombre total du biens à louer
        $biensAlouer = $repository->getbiensAlouer();
        // on récupere le nombre total du biens à vendre
        $biensAvendre = $repository->getbiensAvendre();

        return $this->render('search/biens.html.twig', [

            "triPieces" => $triPieces,
            'triSurfaces' => $triSurfaces,
            'triPrix' => $triPrix,
            'biens' => $biens,
            "total" => $total,
            "biensAlouer" => $biensAlouer,
            "biensAvendre" => $biensAvendre,
            "limit" => $limit,
            "page" => $page,
            'form' => $form->createView()
        ]);
    }

   
    /**
     * fonction qui retourne un seule bien qu'on le recupere par son id
     * elle retourne aussi un formulaire d'appointement qui correspond à ce bien
     */
    #[Route('/bien/{id}', name: "single_bien", requirements: ['id' => "[0-9]+"])]
    public function single($id, ManagerRegistry $manager, Request $request, AppointementRepository $appointementRepository): Response
    {
        //or récupere un bien par son id
        $bien = $manager->getRepository(Bien::class)->find($id);
        //on instancie la class Appointement
        $appointement = new Appointement();
        //on crée un formulaire d'appointement
        $form = $this->createForm(AppointementType::class, $appointement);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            //on injecte le titre du bien dans la table appointement
            $appointement->setTitre($bien);
            //on insére les donneés envoyées par le formulaire 
            $appointementRepository->add($appointement);
            $this->addFlash("success", "La demande a été envoiée avec succès");
            return $this->redirectToRoute('single_bien', ['id' => $id,]  ,Response::HTTP_SEE_OTHER);
        }

        if ($bien) {
            //si le bien existe on retourne le bien et le formulaire
            return $this->renderForm('bien/single.html.twig', [
                'bien' => $bien,
                'form' => $form
            ]);
        } else {
            //si non on retourne un message d'erreur
            $this->addFlash("danger", "Le bien demandé n'existe pas");
            return $this->redirectToRoute("accueil");
        }
    }

    /**
     * Undocumented function
     *
     * @param Request $request
     * @param ManagerRegistry $manager
     * @return Response
     */
    #[Route('bien/save', name: 'bien_save', methods: ["GET", "POST"])]
    public function save(Request $request, ManagerRegistry $manager,UserInterface $user): Response
    {
        //recuperer le filtre de l'administartion du href
        $administration = $request->query->get("administration");
        if(!$administration){
             $administration = 'Biens';
        }

        // on instancie la class Bien
        $bien = new Bien();
        // on crée un formulaire du bien
        $form = $this->createForm(BienType::class, $bien);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // on récupere les photos transmises
            $photos = $form->get('photos')->getData();
            //on boucle sur les photos
            foreach ($photos as $photo) {
                //on génére un nouveau nom du fichier photo.
                $fichier = md5(uniqid()) . '.' . $photo->guessExtension();
                //on copie le fichier dans le dossier public/img/biens
                $photo->move(
                    $this->getParameter('upload_dir'),
                    $fichier
                );

                // on stocke l'image dans la base de données (son nom)
                $img = new Photo();
                $img->setPhoto($fichier);
                $bien->addPhoto($img);
            }
            //si l'utilisateur connecté n'est pas un admin
            //l'employeur du table bien prendra automatiquement l'utilisateur connecté
            if(!$this->isGranted('ROLE_ADMIN')){
                $bien->setEmployeur($user);
            }
            
            $em = $manager->getManager();
            $em->persist($bien);

            //on recuper le checkbox valided
            $checked = $form->get('options')->getData();
            foreach ($checked as $option) {
                if ($option == true) {
                    $optionbien = new Optionbien();
                    $optionbien->setIdbien($bien);
                    $optionbien->setIdoption($option);
                    $em->persist($optionbien);
                }
            }
            
            $em->flush();
            $this->addFlash("success", "Le bien a été ajouté avec succés");
            return $this->redirectToRoute('administration',  [], Response::HTTP_SEE_OTHER);
        }
       
        return $this->render('bien/save.html.twig', [
            'administration' => $administration,
            //on retourn l'utilisateur connecté
            'user' => $user,
            //on retourn un formilaire d'ajout de bien
            'form' => $form->createView()
        ]);
    }

    
    /**
     * une fonction qui retourne un formulaire de mise à jour pour le bien
     */
    #[Route('/bien/{id}/update', name: "bien_update", requirements: ['id' => "[0-9]+"])]
    public function edit(Request $request, Bien $bien, ManagerRegistry $manager, UserInterface $user): Response
    {
        //recuperer le filtre de l'administartion du href
        $administration = $request->query->get("administration");
        if(!$administration){
             $administration = 'Biens';
        }

        $form = $this->createForm(BienType::class, $bien);
        //on recupere la requete
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // on récupere les photos transmises
            $photos = $form->get('photos')->getData();
            //on boucle sur les photos
            foreach ($photos as $photo) {
                //on genere un nouveau nom du fichier image.
                $fichier = md5(uniqid()) . '.' . $photo->guessExtension();

                //on copie le fichier dans le dossier biens
                $photo->move(
                    $this->getParameter('upload_dir'),
                    $fichier
                );

                // on stocke l'image dans la base de données (son nom)
                $img = new Photo();
                $img->setPhoto($fichier);
                $bien->addPhoto($img);
            }
            
            $em = $manager->getManager();
            $em->persist($bien);

            //on récupére le checkbox valide
            $checked = $form->get('options')->getData();
            //on récupére les options du bien
            $designation = $bien->getOptionbiens()->getValues();
            //on parcours la liste des checkbox du table option
            foreach ($checked as $option) {
                $val = false;
                //on parcours la liste de designation
                foreach ($designation as $optionEx) {
                    if ($optionEx->getIdoption()->getId() == $option->getId()) {
                        $val = true;
                    }
                }
                 if (!$val) {
                    $optionbien = new Optionbien();
                    $optionbien->setIdbien($bien);
                    $optionbien->setIdoption($option);
                    $em->persist($optionbien);
                }
            }            

            $em->flush();
            $this->addFlash("success", "Le bien a été modifié avec succés");

            return $this->redirectToRoute('administration', ['administration'=>'Biens'], Response::HTTP_SEE_OTHER);
        }

        return $this->render('bien/update.html.twig', [
            'administration' => $administration,
            'bien' => $bien,
            //on retourn le formulaire de mise à jours du bien
            'form' => $form->createView(), 
            'user' => $user,
        ]);
    }


    /**
     * fonction qui permet de supprimer un bien par son id
     */
    #[Route('bien/{id}/delete', name: 'bien_delete', methods: ['POST'])]
    public function delete(Request $request, Bien $bien, BienRepository $bienRepository): Response
    {
        if ($this->isCsrfTokenValid('delete' . $bien->getId(), $request->request->get('_token'))) {
            //on supprime le bien
            $bienRepository->remove($bien);
        }

        $this->addFlash("success", "Le bien a été supprimé avec succés");
        return $this->redirectToRoute('administration', ['administration'=>'Biens'], Response::HTTP_SEE_OTHER);
    }
    
     /**
     * je retourne les biens qui concerne chaque employeur 
     *
     * @param BienRepository $repository
     * @param UserInterface $user
     * @return Response
     */
    #[Route('/administration', name: 'administration')]
    public function administration(ManagerRegistry $manager, BienRepository $repository,AppointementRepository $apprepository, Request $request,UserInterface $user, UserRepository $users): Response
    {
        //recuperer le filtre de l'administartion du href
        $administration = $request->query->get("administration");
        if(!$administration){
             $administration = 'Biens';
        }

        $filteredBiens = array();
        $form = $this->createForm(RechercheFormType::class);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            //ici on recupere l'information du champs
            $searchWord = $form->get('searchWord')->getData();
            //on recupere les resultats obrenus et ça sera dans un array
            $filteredBiens = $manager->getRepository(Bien::class)->findWithSearchword($searchWord);
        }
            return $this->render('bien/administration.html.twig', [
                'administration' => $administration,
                //on récupere la liste des biens de chaque utilisateur
                'biensuser' => $repository->getBiensUser($user),
                //on recupere tout les biens
                'biens' => $repository->findAll(),
                //on récupere l'identifiant de l'utlisateur connecté
                'employeur' => $user->getUserIdentifier(),
                //on récupere la liste de tout les utilisateurs
                'listeEmployeurs' => $users->findAll(),
                //on récupere la liste de tout les biens
                'listeBiens' => $repository->findAll(),
                //on recupere la liste les rendez-vous de chauqe utilisateur
                'appointements' => $apprepository->getAppointement($user),
                //on recupere la liste de tout les rendez-vous
                'listeAppointements' => $apprepository->findAll(),
                //on recupere l'utilisateur connecté
                'user' =>$user,
                //
                'filteredBiens' => $filteredBiens,
                'formRecherche' => $form->createView(),
        ]);
    }

    /**
     * fonction permet à l'utulisateur connecté de modifier son mot de passe
     *
     * @param ManagerRegistry $manager
     * @param Request $request
     * @param UserPasswordHasherInterface $passHasher
     * @return Response
     */
    #[Route('/editpass', name: 'editpass')]
    public function editpass(ManagerRegistry $manager, Request $request, UserPasswordHasherInterface $passHasher, UserInterface $userConnecte): Response
    {
        //recuperer le filtre de l'administartion du href
        $administration = $request->query->get("administration");
        if(!$administration){
             $administration = 'Fiche';
        }
        //on verifie si on est en methode POST
        if($request->isMethod('POST')){
            $em = $manager->getManager();
            $user = $this->getUser();
            //on verifie si les 2 mots de passe sont identiques
            if($request->request->get('pass') == $request->request->get('pass2')){
                $user->setPassword($passHasher->hashPassword($user,$request->request->get('pass')));
                $em->flush(); // pour metre a jour la base de donnees
                $this->addFlash("success", "Mot de passe a été mis à jour avec succès");
                return $this->redirectToRoute('administration', ['administration'=>'Fiche']);
            }else{
                $this->addFlash('error', 'Les deux mots de passes ne sont pas identiques');
            }

        }

        return $this->render('fiche/editpass.html.twig', ['administration' => $administration, 'user' => $userConnecte]);
    }
    

    /**
     * cette fonction permet aux utilisateurs connectés de prendre un rendez-vous
     *
     * @param Request $request
     * @param AppointementRepository $appointementRepository
     * @param UserInterface $user
     * @return Response
     */
    #[Route('/addappointement', name: 'addAppointement')]
    public function appointement(Request $request, AppointementRepository $appointementRepository, UserInterface $user): Response
    {
         //recuperer le filtre de l'administartion du href
         $administration = $request->query->get("administration");
         if(!$administration){
              $administration = 'Appointement';
         }
        
            $appointement = new Appointement();
            //on crée un formulaire d'appointement
            $form = $this->createForm(AppointementType::class, $appointement);
            $form->handleRequest($request);
            
            if ($form->isSubmitted() && $form->isValid()) {
            
                //on insére les donneés envoyées par le formulaire 
                $appointementRepository->add($appointement);
                $this->addFlash("success", "Le rendez-vous a été envoié avec succès");
                return $this->redirectToRoute('administration', ['administration'=>'Appointement'], Response::HTTP_SEE_OTHER);
            }
            return $this->render('bien/addAppointement.html.twig', [
                'administration' => $administration,
                'form' => $form->createView(),
                'user' => $user,
            
            
            ]);
       
    }

     /**
     * cette fonction permet aux Employés connectés de prendre un rendez-vous
     *
     * @param Request $request
     * @param AppointementRepository $appointementRepository
     * @param UserInterface $user
     * @return Response
     */
    #[Route('/addappointementUser', name: 'addAppointementUser')]
    public function appointementUser(Request $request, AppointementRepository $appointementRepository, UserInterface $user): Response
    {
         //recuperer le filtre de l'administartion du href
         $administration = $request->query->get("administration");
         if(!$administration){
              $administration = 'Appointement';
         }

        $appointementUser = new Appointement();
        //on crée un formulaire d'appointement
        $form = $this->createForm(AppointementUserType::class, $appointementUser);
        $form->handleRequest($request);
        
        if ($form->isSubmitted() && $form->isValid()) {
           
            //on insére les donneés envoyées par le formulaire 
            $appointementRepository->add($appointementUser);
            $this->addFlash("success", "Le rendez-vous a été envoié avec succès");
            return $this->redirectToRoute('administration', ['administration'=>'Appointement'], Response::HTTP_SEE_OTHER);
        }
        return $this->render('bien/addAppointementUser.html.twig', [
            'administration' => $administration,
            'form' => $form->createView(),
            'user' => $user,
           
          
        ]);
    }


}
