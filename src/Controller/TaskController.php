<?php

namespace App\Controller;

use App\Entity\Task;
use App\Form\TaskType;
use App\Repository\TaskRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class TaskController extends AbstractController
{

    /**
     * @var EntityManagerInterface $manager
     */
    private $manager;

    /**
     * @var TaskRepository $repository
     */
    private $repository;

    public function __construct(EntityManagerInterface $manager, TaskRepository $repository)
    {
        $this->manager = $manager;
        $this->repository = $repository;
    }

    /**
     * @Route("/tasks", name="task_list")
     */
    public function listAction(Request $request)
    {

        $page = $request->query->get('page') ? $request->query->get('page') : 0;
        $itemsPerPage = $request->query->get('itemsPerPage') ? $request->query->get('itemsPerPage') : 30;
        $criteria = $request->query->get('status') ? ['isDone' => (int) $request->query->get('status')] : [];
        $orderBy = $request->query->get('sort') ? $request->query->get('sort') : 'ASC';
        $tasks = $this->repository->findPaginatedTasks(
            $page,
            $itemsPerPage,
            $criteria,
            $orderBy
        );

        $nbPage = (int) ceil($this->repository->countTasks($page, $itemsPerPage, $criteria) / $itemsPerPage);

        return $this->render('task/list.html.twig', compact('tasks', 'nbPage'));
    }

    /**
     * @Route("/tasks/create", name="task_create")
     */
    public function createAction(Request $request)
    {
        $task = new Task();
        $form = $this->createForm(TaskType::class, $task);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $task->setUser($this->getUser())
                ->setIsDone()
                ->setCreatedAt()
            ;

            $this->manager->persist($task);
            $this->manager->flush();

            $this->addFlash('success', 'La tâche a été bien été ajoutée.');

            return $this->redirectToRoute('task_list');
        }

        return $this->render('task/create.html.twig', ['form' => $form->createView()]);
    }

    /**
     * @Route("/tasks/{id}/edit", name="task_edit")
     */
    public function editAction(Task $task, Request $request)
    {

        $this->denyAccessUnlessGranted('task_edit', $task);

        $form = $this->createForm(TaskType::class, $task);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $referer = $request->headers->get('referer');
            $this->manager->persist($task);
            $this->manager->flush();

            $this->addFlash('success', 'La tâche a bien été modifiée.');

            return $this->redirectToRoute('task_list');
        }

        return $this->render('task/edit.html.twig', [
            'form' => $form->createView(),
            'task' => $task,
        ]);
    }

    /**
     * @Route("/tasks/{id}/toggle", name="task_toggle")
     */
    public function toggleTaskAction(Task $task)
    {
        $this->denyAccessUnlessGranted('task_edit', $task);

        $task->toggle(!$task->getIsDone());
        $this->manager->persist($task);
        $this->manager->flush();

        $this->addFlash('success', sprintf('La tâche %s a bien été marquée comme faite.', $task->getTitle()));

        return $this->redirectToRoute('task_list');
    }

    /**
     * @Route("/tasks/{id}/delete", name="task_delete")
     */
    public function deleteTaskAction(Task $task)
    {

        $this->denyAccessUnlessGranted('task_delete', $task);
        // $this->manager->remove($task);
        // $this->manager->flush();

        $this->addFlash('success', 'La tâche a bien été supprimée.');

        return $this->redirectToRoute('task_list');
    }
}
