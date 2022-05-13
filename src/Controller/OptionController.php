<?php

namespace App\Controller;

use App\Entity\Bien;
use App\Entity\Option;
use App\Form\OptionType;
use App\Form\RechercheFormType;
use App\Repository\BienRepository;
use App\Repository\OptionRepository;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\User\UserInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

#[Route('/option')]
#[IsGranted(data:'ROLE_USER', message: "Vous n'avez pas les autorisations nécessaires", statusCode: 403)]
class OptionController extends AbstractController
{
    #[Route('/', name: 'app_option_index', methods: ['GET'])]
    public function index(ManagerRegistry $manager, Request $request, OptionRepository $optionRepository, BienRepository $repository, UserInterface $user): Response
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
             $administration = 'Biens';
        }
        // return $this->redirectToRoute('administration', ['administration' => 'Bien'], Response::HTTP_SEE_OTHER);
        return $this->render('option/index.html.twig', [
            //retourne la listes des options
            'options' => $optionRepository->findAll(),
            'administration' => $administration,
            'biens' => $repository->findAll(),
            'filteredBiens' =>  $filteredBiens,
            'formRecherche' => $formRecherche->createView(),
            'user' => $user
        ]);
    }

    /**
     * fonction retourne un formulaire pour l'ajout d'option
     *
     * @param Request $request
     * @param OptionRepository $optionRepository
     * @return Response
     */
    #[Route('/new', name: 'app_option_new', methods: ['GET', 'POST'])]
    public function new(ManagerRegistry $manager, Request $request, OptionRepository $optionRepository, BienRepository $repository, UserInterface $user): Response
    {
        $option = new Option();
        $form = $this->createForm(OptionType::class, $option);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $optionRepository->add($option);
            $this->addFlash("success", "L'option a été ajouté avec succès");
            return $this->redirectToRoute('app_option_index', [], Response::HTTP_SEE_OTHER);
            
        }

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
            $administration = 'Biens';
        }

        
        return $this->renderForm('option/new.html.twig', [
            'option' => $option,
            'form' => $form,
            'administration' => $administration,
            'biens' => $repository->findAll(),
            'filteredBiens' =>  $filteredBiens,
            'formRecherche' => $formRecherche,
            'user' => $user
        ]);
    }



    #[Route('/{id}', name: 'app_option_delete', methods: ['POST'])]
    public function delete(Request $request, Option $option, OptionRepository $optionRepository): Response
    {
        if ($this->isCsrfTokenValid('delete'.$option->getId(), $request->request->get('_token'))) {
            $optionRepository->remove($option);
            $this->addFlash("success", "L'option a été supprimé avec succès");
        }

        return $this->redirectToRoute('app_option_index', [], Response::HTTP_SEE_OTHER);
    }
}
