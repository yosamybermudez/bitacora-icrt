<?php

namespace App\Controller;

use App\Entity\EventoCalendario;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/guardia")
 */
class GuardiaController extends AppController
{
    /**
     * @Route("/", name="app_guardia_index")
     */
    public function index(): Response
    {

        return $this->render('guardia/index.html.twig');
    }
}
