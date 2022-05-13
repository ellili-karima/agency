<?php

namespace App\Controller;


use App\Entity\Bien;
use App\Entity\Photo;
use App\Form\PhotoType;
use App\Repository\PhotoRepository;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;


#[IsGranted(data:'ROLE_USER', message: "Vous n'avez pas les autorisations nécessaires", statusCode: 403)]
class PhotoController extends AbstractController
{
//     #[Route('/photo', name: 'app_photo_index')]
//     public function index(ManagerRegistry $manager): Response
//     {

//         return $this->render('photo/index.html.twig', [
            
//             'photos' => $manager->getRepository(Photo::class)->findAll()
//         ]);
//     }

    // #[Route('/photo/save', name: 'app_photo_new', methods: ['GET', 'POST'])]
    // public function save(Request $request, ManagerRegistry $manager): Response
    // {
    //     $p = new Photo();
    //     $form = $this->createForm(PhotoType::class, $p);
    //     $form->handleRequest($request);

    //     if ($form->isSubmitted() && $form->isValid()) {
    //         $p= $form->getData();
    //         // On récupère les informations de l'image reçue à travers le form
    //         $photo = $form->get('photo')->getData();

    //         if ($photo) {
    //             // On génère un nouveau nom de fichier pour éviter les conflits entre les fichiers existants
    //             $imageName = md5(uniqid()). "." .$photo->guessExtension();
    //             // On déplace le fichier dans le dossier définit par le paramètre upload_dir
    //             // On copie ce fichier avec le nom qui vient d'être généré
    //             $photo->move($this->getParameter('upload_dir'), $imageName);
    //             // On enregistre en BDD le nouveau nom de fichier
    //             $p->setPhoto($imageName);
    //         }

    //         $em = $manager->getManager();
    //         $em->persist($p);
    //         $em->flush();
    //         $this->addFlash("success", "La photo a été ajouté avec succés");
    //         return $this->redirectToRoute('single_bien', ['id' => $p->getId()], 201);
    //     }
    //     return $this->renderForm('photo/save.html.twig', [
    //         'form' => $form
    //     ]);
    // }

    //#[Route('/{id}', name: 'app_photo_show', methods: ['GET'])]
    // public function show(Photo $photo): Response
    // {
    //     return $this->render('photo/show.html.twig', [
    //         'photo' => $photo,
    //     ]);
    // }

    // #[Route('/{id}/edit', name: 'app_photo_edit', methods: ['GET', 'POST'])]
    // public function edit(Request $request, Photo $photo, PhotoRepository $photoRepository): Response
    // {
    //     $form = $this->createForm(PhotoType::class, $photo);
    //     $form->handleRequest($request);

    //     if ($form->isSubmitted() && $form->isValid()) {
    //         $photoRepository->add($photo);
    //         return $this->redirectToRoute('app_photo_index', [], Response::HTTP_SEE_OTHER);
    //     }

    //     return $this->renderForm('photo/edit.html.twig', [
    //         'photo' => $photo,
    //         'form' => $form,
    //     ]);
    // }

    #[Route('/photo/{id}/delete', name: 'delete_photo', methods: ['POST'], requirements:['id'=> "[0-9]+"])]
    public function delete(ManagerRegistry $manager, Request $request, Photo $photo , PhotoRepository $photoRepository): Response
    {
        
        //on verifier si le token est valide
        if ($this->isCsrfTokenValid('delete'.$photo->getId(), $request->request->get('_token'))) {

            //on récupère le nom de la photo
            $nom = $photo->getPhoto();
            //on supprime la photo du dossier img
            unlink($this->getParameter('upload_dir') . '/' . $nom);

            //on supprime la photo de la base
            $em= $manager->getManager();
            $em->remove($photo);
            $em->flush();

            $this->addFlash("success", "La photo a été supprimé avec succès");
            return $this->redirectToRoute('administration', ['administration' => 'Biens'], Response::HTTP_SEE_OTHER);
        } else {
            $this->addFlash('error', "Echec la photo n'a pas été supprimé avec succès");
        }


        return $this->redirectToRoute('biens', [], Response::HTTP_SEE_OTHER);
    }
}
