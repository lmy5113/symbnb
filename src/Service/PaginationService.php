<?php
namespace App\Service;

use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\RequestStack;
use Twig\Environment;

class PaginationService {
    private $entityClass;
    private $limit = 10;
    private $currentPage = 1;
    private $route;
    private $manager;
    private $twig;
    private $templatePath;

    public function __construct(EntityManagerInterface $manager, Environment $twig, RequestStack $request, $templatePath)
    {
        //get current route 
        $this->route = $request->getCurrentRequest()->attributes->get('_route');
        $this->manager = $manager;
        $this->twig = $twig;  
        $this->templatePath = $templatePath;  
    }

    public function getData()
    {
        if (empty($this->entityClass)) {
            throw new \Exception("You must define entity class");
        }
        //calculate offset
        $offset = $this->currentPage * $this->limit - $this->limit;    
        //ask repo to find elements
        $repo = $this->manager->getRepository($this->entityClass);
        $data = $repo->findBy([], [], $this->limit, $offset);    
        //return elements
        return $data;
    }

    public function display()
    {
        $this->twig->display($this->templatePath, [
            'page' => $this->currentPage,
            'pages' => $this->getPages(),
            'route' => $this->getRoute()
        ]);
    }

    public function getPages()
    {
        $repo = $this->manager->getRepository($this->entityClass);
        $total = count($repo->findAll());

        return ceil($total / $this->limit);
    }

    public function setRoute( $route): PaginationService
    {
        $this->route = $route;

        return $this;
    }

    public function getRoute()
    {
        return $this->route;
    }

    public function setEntityClass( $entityClass): PaginationService
    {
        $this->entityClass = $entityClass;

        return $this;
    }

    public function getEntityClass()
    {
        return $this->entityClass;
    }

    public function setLimit($limit): PaginationService
    {
        $this->limit = $limit;

        return $this;
    }

    public function getLimit()
    {
        return $this->limit;
    }

    public function setCurrentPage($currentPage): PaginationService
    {
        $this->currentPage = $currentPage;

        return $this;
    }

    public function getCurrentPage()
    {
        return $this->currentPage;
    }

    public function setTemplatePath($templatePath): PaginationService
    {
        $this->templatePath = $templatePath;

        return $this;
    }

    public function getTemplatePath()
    {
        return $this->templatePath;
    }
}