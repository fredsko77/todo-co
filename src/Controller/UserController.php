<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\RegistrationType;
use App\Form\UserType;
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

    public function __construct(UserPasswordEncoderInterface $encoder, EntityManagerInterface $manager)
    {
        $this->encoder = $encoder;
        $this->manager = $manager;
    }

    /**
     * @Route(
     *  "/admin/users",
     *  name="user_list",
     *  methods={"GET"}
     * )
     */
    public function listAction(): Response
    {
        return $this->render('user/list.html.twig', ['users' => $this->getDoctrine()->getRepository('App:User')->findAll()]);
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
     *  "/admin/users/create",
     *  name="user_create",
     *  methods={"GET", "POST"}
     * )
     */
    public function createAction(Request $request): Response
    {
        $user = new User();
        $form = $this->createForm(UserType::class, $user);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $password = $this->encoder->encodePassword($user, $user->getPassword());
            $user->setPassword($password);

            $this->manager->persist($user);
            $this->manager->flush();

            $this->addFlash('success', "L'utilisateur a bien été ajouté.");

            return $this->redirectToRoute('user_list');
        }

        return $this->render('user/create.html.twig', ['form' => $form->createView()]);
    }

    /**
     * @Route(
     *  "/admin/users/{id}/edit",
     *  name="user_edit",
     *  methods={"GET", "POST"}
     * )
     */
    public function editAction(User $user, Request $request): Response
    {
        $form = $this->createForm(UserType::class, $user);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $password = $this->encoder->encodePassword($user, $user->getPassword());
            $user->setPassword($password);

            $this->manager->persist($user);
            $this->manager->flush();

            $this->addFlash('success', "L'utilisateur a bien été modifié");

            return $this->redirectToRoute('user_list');
        }

        return $this->render('user/edit.html.twig', ['form' => $form->createView(), 'user' => $user]);
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
        $form = $this->createForm(RegistrationType::class);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $user = $form->getData();
            $user->setPassword($this->encoder->encodePassword($user, $user->getPassword()))
                ->setRoles(User::ROLES['Utilisateur'])
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
