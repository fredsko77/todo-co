<?php

namespace App\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/documentation")
 * @IsGranted("IS_AUTHENTICATED_ANONYMOUSLY")
 */
class DocumentationController extends AbstractController
{
    /**
     * @Route("", name="app_documentation")
     */
    public function index(): Response
    {
        return $this->render('documentation/index.html.twig');
    }
}
