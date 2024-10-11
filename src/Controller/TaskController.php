<?php

namespace App\Controller;

use App\Service\TaskDistributorService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;

class TaskController extends AbstractController
{
    private $taskDistributorService;

    public function __construct(TaskDistributorService $taskDistributorService)
    {
        $this->taskDistributorService = $taskDistributorService;
    }

    /**
     * @Route("/", name="app_task")
     */
    public function index(): Response
    {
        $tasks = $this->taskDistributorService->distributeTasks();

        return $this->render('task/index.html.twig', [
            'weekCount' => $tasks['weekCount'],
            'extraTime' => $tasks['extraTime'],
            'taskCount' => $tasks['taskCount'],
            'finishedTasksCount' => $tasks['finishedTasksCount'],
            'unfinishedTasksCount' => $tasks['unfinishedTasksCount'],
            'taskAssignments' => $tasks['taskAssignments']
        ]);
    }
}
