<?php

namespace App\Controller;

use App\Entity\Ad;
use App\Form\AdType;
use App\Repository\AdRepository;
use App\Service\PaginationService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class AdminAdController extends AbstractController
{
    /**
     * @Route("/admin/ads/{page<\d+>?1}", name="admin_ads_index")
     */
    public function index(AdRepository $repo, $page, PaginationService $pagination)
    {
        $pagination->setEntityClass(Ad::class)
                   ->setCurrentPage($page); 
        return $this->render('admin/ad/index.html.twig', [
            'pagination' => $pagination
        ]);
    }

    /**
     * @Route("admin/ads/{id}/edit", name="admin_ads_edit")
     * @param Ad $ad
     * @return void
     */
    public function edit(Ad $ad, Request $request, EntityManagerInterface $manager)
    {
        $form = $this->createForm(AdType::class, $ad);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $manager->persist($ad);
            $manager->flush();
            $this->addFlash('success', "L'annonce <strong>{$ad->getTitle()}</strong> a été bien enregistrée");
        }
        return $this->render('admin/ad/edit.html.twig', [
            'ad' => $ad,
            'form' => $form->createView()
        ]);
    }

    /**
     * Undocumented function
     *@Route("/admin/ads/{id}/delete", name="admin_ads_delete")
     * @param Ad $ad
     */
    public function delete(Ad $ad, EntityManagerInterface $manager)
    {
        if (count($ad->getBookings())) {
            $this->addFlash('warning', "Vous ne pouvez pas supprimer l'annonce {$ad->getTitle()} car elle possède dejà des réservations");
        } else {
            $manager->remove($ad);
            $manager->flush();
            $this->addFlash('success', "L'annonce <strong>{$ad->getTitle()}</strong> a été bien supprimée");
        }


        return $this->redirectToRoute("admin_ads_index");
    }
}
