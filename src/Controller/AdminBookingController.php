<?php

namespace App\Controller;

use App\Entity\Booking;
use App\Form\AdminBookingType;
use App\Form\BookingType;
use App\Repository\BookingRepository;
use App\Service\PaginationService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class AdminBookingController extends AbstractController
{
     /**
     * @Route("/admin/bookings/{page<\d+>?1}", name="admin_bookings_index")
     */
    public function index(BookingRepository $repo, $page, PaginationService $pagination)
    {
        $pagination->setEntityClass(Booking::class)
                   ->setCurrentPage($page);

        return $this->render('admin/booking/index.html.twig', [
            'pagination' => $pagination
        ]);
    }

    /**
     * Undocumented function
     * @Route("/admin/bookings/{id}/edit", name="admin_bookings_edit")
     * @param Booking $booking
     * 
     */
    public function edit(Booking $booking, Request $request, EntityManagerInterface $manager)
    {
        $form = $this->createForm(AdminBookingType::class, $booking);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            //in order to activate auto-calculation 
            $booking->setAmount(0);
            $manager->persist($booking);
            $manager->flush();
            $this->addFlash('success', "La réservation <strong>{$booking->getId()}</strong> a été bien modifiée");

            return $this->redirectToRoute("admin_bookings_index");
        }
        return $this->render('admin/booking/edit.html.twig', [
            'booking' => $booking,
            'form' => $form->createView()
        ]);
    }

    /**
     * Undocumented function
     *@Route("/admin/bookings/{id}/delete", name="admin_bookings_delete")
     * @param Booking
     */
    public function delete(Booking $booking, EntityManagerInterface $manager)
    {

        $manager->remove($booking);
        $manager->flush();
        $this->addFlash('success', "L'annonce <strong>{$booking->getId()}</strong> a été bien supprimée");

        return $this->redirectToRoute("admin_bookings_index");
    }
}
