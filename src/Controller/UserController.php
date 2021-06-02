<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\RegistrationType;
use App\Repository\UserRepository;
use App\Traits\ServicesTrait;
use Doctrine\ORM\EntityManagerInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class UserController extends AbstractController
{

    use ServicesTrait;

    /**
     * @var UserPasswordEncoderInterface $encoder
     */
    private $encoder;

    /**
     * @var EntityManagerInterface $manager
     */
    private $manager;

    /**
     * @var UserRepository $repository
     */
    private $repository;

    public function __construct(UserPasswordEncoderInterface $encoder, EntityManagerInterface $manager, UserRepository $repository)
    {
        $this->encoder = $encoder;
        $this->manager = $manager;
        $this->repository = $repository;
    }

    /**
     * @Route(
     *  "/profile",
     *  name="app_profile",
     *  methods={"GET"}
     * )
     */
    public function profileAction(): Response
    {
        return $this->render('user/profile.html.twig', [
            'profile' => $this->getUser(),
        ]);
    }

    /**
     * @Route(
     *  "/register",
     *  name="app_register",
     *  methods={"GET", "POST"}
     * )
     */
    public function registerAction(Request $request): Response
    {

        if ($this->getUser()) {
            return $this->redirectToRoute('app_home');
        }

        $form = $this->createForm(RegistrationType::class);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $user = $form->getData();
            $user->setPassword($this->encoder->encodePassword($user, $user->getPassword()))
                ->setRoles(['ROLE_USER'])
                ->setCreatedAt($this->now())
            ;

            $this->manager->persist($user);
            $this->manager->flush();

            $this->addFlash('success', 'Vous êtes inscrit ! 👍');

            return $this->redirectToRoute('app_login');
        }

        return $this->render('user/register.html.twig', [
            'form' => $form->createView(),
            'title' => 'Page d\'inscription',
        ]);
    }
}
