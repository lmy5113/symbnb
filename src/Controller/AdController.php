<?php

namespace App\Controller;

use App\Entity\Ad;
use App\Entity\Image;
use App\Form\AdType;
use App\Repository\AdRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;

class AdController extends AbstractController
{
    /**
     * denpendency injection example => tell symfony which param that you need
     * @Route("/ads", name="ads_index")
     */
    public function index(AdRepository $repo)
    {
        $ads = $repo->findAll();

        return $this->render('ad/index.html.twig', [
            'ads' => $ads
        ]);
    }

    /**
     * role control example
     * @IsGranted("ROLE_USER")
     * create an annonce
     * @Route("/ads/new", name="ads_create")
     *
     */
    public function create(Request $request, EntityManagerInterface $manager)
    {
        $ad = new Ad();
             
        $form = $this->createForm(AdType::class, $ad);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            foreach($ad->getImages() as $image) {
                $image->setAd($ad);
                $manager->persist($image);
            }
            
            $ad->setAuthor($this->getUser());

            $manager->persist($ad);
            $manager->flush();
            $this->addFlash('success', "L'annonce <strong>{$ad->getTitle()}</strong> a bien été enregistrée.");

            return $this->redirectToRoute('ads_show', [
                'slug' => $ad->getSlug()
            ]);
        }

        return $this->render('ad/new.html.twig', [
            'form' => $form->createView()
        ]);
    }

    /**
     * @Security("is_granted('ROLE_USER') and user === ad.getAuthor()", message = "Cette annonce vous appartient pas")
     * @Route("/ads/{slug}/edit", name="ads_edit")
     *
     */
    public function edit(Ad $ad, Request $request, EntityManagerInterface $manager) 
    {
        $form = $this->createForm(AdType::class, $ad);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            foreach($ad->getImages() as $image) {
                $image->setAd($ad);
                $manager->persist($image);
            }

            $manager->persist($ad);
            $manager->flush();
            $this->addFlash('success', "L'annonce <strong>{$ad->getTitle()}</strong> a bien été modifié.");

            return $this->redirectToRoute('ads_show', [
                'slug' => $ad->getSlug()
            ]);
        }

        return $this->render("ad/edit.html.twig", [
            'form' => $form->createView(),
            'ad' => $ad
        ]);
    }

    /**
     * @Route("/ads/{slug}", name="ads_show")
     *  ParamConverter example
     */
    public function show(Ad $ad)
    {
        //use findBy{fieldName}/findOneBy{fieldName}
        //$ad = $repo->findOneBySlug($slug);

        return $this->render('ad/show.html.twig', [
            'ad' => $ad
        ]);
    }

    /**
     * @Route("/ads/{slug}/delete", name="ads_delete")
     * @Security("is_granted('ROLE_USER') and user == ad.getAuthor()")
     */
    public function delete(Ad $ad, EntityManagerInterface $manager)
    {
        $manager->remove($ad);
        $manager->flush();

        $this->addFlash('success', "L'annonce {$ad->getTitle()} a bien été supprimé");

        return $this->redirectToRoute("ads_index");
    }
}
