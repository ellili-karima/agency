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
use App\Repository\BienRepository;
use Doctrine\Persistence\ManagerRegistry;
use App\Repository\AppointementRepository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class BienController extends AbstractController
{
    #[Route('/', name: 'accueil')]
    public function index(ManagerRegistry $manager): Response
    {
        $biens = $manager->getRepository(Bien::class)->getLast5();
        return $this->render('bien/home.html.twig', [
            'biens' => $biens,
        ]);
    }

    #[Route('/biens', name: 'biens')]
    public function search( ManagerRegistry $manager,BienRepository $repository, Request $request): Response
    {
        //on définit le nombre d'éléments par page
        $limit = 10;
        //on récupere le numéro de la page
        $page = (int)$request->query->get("page", 1);
        $data = new SearchData();
        $form = $this->createForm(SearchFormType::class, $data);
        $form->handleRequest($request);
        $tri= '';
        if ($form->isSubmitted() && $form->isValid()) {
            $biens = $repository->findSearch($data);
        }
        elseif ($tri) {
            $biens = $repository->triBiens();
        }
        else{
            // on récupere les biens de la page
            $biens = $manager->getRepository(Bien::class)->getPaginatedBiens($page, $limit);
            
        }
        // on récupere le nombre total du biens
        $total = $repository->getTotalBiens();
        
       
        return $this->render('search/biens.html.twig', [
            'biens' => $biens,
            "total" => $total,
            "limit" => $limit,
            "page" => $page,
            "tri" => $tri,
            'form' => $form->createView()
        ]);
    }


    #[Route('/bien/{id}', name:"single_bien", requirements:['id'=> "[0-9]+"])]
    public function single($id, ManagerRegistry $manager, Request $request, AppointementRepository $appointementRepository): Response
    {
        
        $bien = $manager->getRepository(Bien::class)->find($id);
       
        
        $appointement = new Appointement();
        $form = $this->createForm(AppointementType::class, $appointement);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $appointement->setTitre($bien);
            $appointementRepository->add($appointement);
            return $this->redirectToRoute('biens', [], Response::HTTP_SEE_OTHER);
        }

        if ($bien) {
            return $this->renderForm('bien/single.html.twig', [
                'bien' => $bien,
                'form' => $form
            ]);
        } else {   
            $this->addFlash("danger", "Le bien demandé n'existe pas");
            return $this->redirectToRoute("accueil");
        }
    }

    #[Route('/bien/save', name: 'bien_save', methods:["GET", "POST"] )]
    public function save(Request $request, ManagerRegistry $manager): Response
    {
        $bien = new Bien();
        $form = $this->createForm(BienType::class, $bien);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            // on récupere les photos transmises
            $photos = $form->get('photos')->getData();
            //on boucle sur les photos
            foreach ($photos as $photo) {
                //on genere un nouveau nom du fichier image.
                $fichier = md5(uniqid()).'.'.$photo->guessExtension();

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

             // //on recupere le checkbox valide
            // $checked = $form->get('options')->getData();
            // //on recupere les options du bien
            // $designation = $bien->getOptionbiens()->getValues();
            // //on parcours la liste des checkbox du table option
            // foreach($checked as $option){
            //     //on parcours la liste de designation
            //     foreach ($designation as $optionEx) {
            //         if($optionEx->getIdoption()->getId() == $option->getId()){

            //         }

                     
            //     }
            //     if($option==true){
            //         $optionbien= new Optionbien();
            //         $optionbien->setIdbien($bien);
            //         $optionbien->setIdoption($option);
            //         $em->persist($optionbien);                  
            //       }
            // }
            
            $em->flush();
            $this->addFlash("success", "Le bien a été ajouté avec succés");
            return $this->redirectToRoute('accueil',  [], Response::HTTP_SEE_OTHER);
        }
        return $this->render('bien/save.html.twig', [
            'bien' => $bien,
            'form' => $form->createView()
        ]);

    }

    #[Route('/bien/{id}/update', name:"bien_update", requirements:['id'=> "[0-9]+"])]
    public function edit(Request $request, Bien $bien , ManagerRegistry $manager ): Response
    {
        $form = $this->createForm(BienType::class, $bien);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // on récupere les photos transmises
            $photos = $form->get('photos')->getData();
            //on boucle sur les photos
            foreach ($photos as $photo) {
                //on genere un nouveau nom du fichier image.
                $fichier = md5(uniqid()).'.'.$photo->guessExtension();

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
        
            //on recuper le checkbox valided
            $checked = $form->get('options')->getData();
            foreach($checked as $option){
                if($option==true){
                    $optionbien= new Optionbien();
                    $optionbien->setIdbien($bien);
                    $optionbien->setIdoption($option);
                    $em->persist($optionbien);                    }
            }

            $em->flush();
            $this->addFlash("success", "Le bien a été modifié avec succés");

            return $this->redirectToRoute('accueil', [], Response::HTTP_SEE_OTHER);
        }
        
        return $this->render('bien/update.html.twig', [
            'bien' => $bien,
            'form' => $form->createView()
        ]);
      
    }

    #[Route('bien/{id}/delete', name:'bien_delete', methods: ['POST'])]
    public function delete(Request $request, Bien $bien, BienRepository $bienRepository): Response
    {
        if ($this->isCsrfTokenValid('delete'.$bien->getId(), $request->request->get('_token'))) {
            $bienRepository->remove($bien);
        }

        return $this->redirectToRoute('biens', [], Response::HTTP_SEE_OTHER);
    }

    // #[Route('/photo/{id}/delete', name:'delete_photo', methods: ['POST'])]
    // public function deletePhoto(Photo $photo, Request $request, ManagerRegistry $manager){






    //     $data =Json_decode($request->getContent(), true);
    //     //on verifier si le token est valide
    //     if($this->isCsrfTokenValid('delete'.$photo->getId(), $data['_token'])){

    //         //on récupère le nom de l'image
    //         $nom = $photo->getPhoto();
    //         //on supprime le fichier
    //         unlink($this->getParameter('upload_dir').'/'.$nom);
        
    //         //on supprime l'entrée de la base
    //         $em = $manager->getManager();
    //         $em->remove($photo);
    //         $em->flush();

    //         //on répont en Json_decode
    //         return new JsonResponse(['success'=>1]);
    //     }else{
    //         return new JsonResponse(['error' => 'Token invalide'], 400);
    //     }
    // }
    
}


