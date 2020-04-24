<?php 

namespace App\Controller;

use App\Repository\AdRepository;
use App\Repository\UserRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class HomeController extends AbstractController {
    
    /**
     * Multi route example, it's important to set name so that it can be called by path() in twig template
     * @Route("/hello/{name}", name="hello_name", requirements={"name"="\w+"})
     * @Route("/hello", name="hello_base")
     * @return void
     */
    public function hello(string $name = 'test', int $age = 0) {
        return $this->render(
            'hello.html.twig',
            [
                'name' => $name,
                'age' => $age,
            ]
            );  
    }
    
    /**
     * @Route("/", name="homepage")
     */
    public function home(AdRepository $adRepository, UserRepository $userRepository) {
        return $this->render(
            'home.html.twig', 
            [
                'ads' => $adRepository->findBestAds(10),
                'users' => $userRepository->findBestUsers(10)
            ]    
        );
    }
}

?>