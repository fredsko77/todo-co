<?php
namespace App\Controller\Admin;

use App\Entity\User;
use App\Form\UserType;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

/**
 * Require ROLE_ADMIN for *every* controller method in this class.
 *
 * @IsGranted("ROLE_ADMIN")
 */
class UserController extends AbstractController
{

    /**
     * @var UserRepository $repository
     */
    private $repository;

    /**
     * @var EntityManagerInterface $manager
     */
    private $manager;

    /**
     * @var UserPasswordEncoderInterface $encoder
     */
    private $encoder;

    public function __construct(UserRepository $repository, EntityManagerInterface $manager, UserPasswordEncoderInterface $encoder)
    {
        $this->repository = $repository;
        $this->manager = $manager;
        $this->encoder = $encoder;
    }

    /**
     * @Route(
     *  "/admin/users",
     *  name="admin_user_list",
     *  methods={"GET"}
     * )
     */
    public function listAction(): Response
    {
        return $this->render('user/list.html.twig', [
            'users' => $this->repository->except($this->getUser()),
        ]);
    }

    /**
     * @Route(
     *  "/admin/users/create",
     *  name="admin_user_create",
     *  methods={"GET", "POST"}
     * )
     */
    public function createAction(Request $request): Response
    {
        $user = new User();
        $form = $this->createForm(UserType::class, $user);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $password = $this->encoder->encodePassword($user, 'P@ssTod0');
            $user->setPassword($password);
            $user->setCreatedAt();

            $this->manager->persist($user);
            $this->manager->flush();

            $this->addFlash('success', "L'utilisateur a bien été ajouté.");

            return $this->redirectToRoute('admin_user_list');
        }

        return $this->render('user/create.html.twig', ['form' => $form->createView()]);
    }

    /**
     * @Route(
     *  "/admin/users/{id}/edit",
     *  name="admin_user_edit",
     *  methods={"GET", "POST"}
     * )
     */
    public function editAction(User $user, Request $request): Response
    {
        $form = $this->createForm(UserType::class, $user);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $this->manager->persist($user);
            $this->manager->flush();

            $this->addFlash('success', "L'utilisateur a bien été modifié");

            return $this->redirectToRoute('admin_user_list');
        }

        return $this->render('user/edit.html.twig', ['form' => $form->createView(), 'user' => $user]);
    }

}
