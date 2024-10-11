<?php

namespace App\Service;

use App\Entity\Developer;
use App\Entity\Task;
use Doctrine\ORM\EntityManagerInterface;

class TaskDistributorService
{
    private $entityManager;
    private $tasks;
    private $developers;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
        $this->tasks = $this->entityManager->getRepository(Task::class)->findAll();
        $this->developers = $this->entityManager->getRepository(Developer::class)->findAll();
    }

    public function distributeTasks(): array
    {
        $finishedTasks = [];
        $taskAssignments = [];
        $weekCount = 0;
        $totalTaskCount = count($this->tasks);

        usort($this->tasks, function ($a, $b) {
            return $b->getDuration() - $a->getDuration();
        });

        while (!empty($this->tasks)) {
            $taskAssigned = false;

            foreach ($this->tasks as $key => $task) {
                foreach ($this->developers as $developer) {
                    if ($developer->getActiveTask() === null) {
                        if ($task->getDifficulty() > 5) {
                            foreach ($this->developers as $dev1) {
                                foreach ($this->developers as $dev2) {
                                    if ($dev1 !== $dev2
                                        && $dev1->getActiveTask() === null
                                        && $dev2->getActiveTask() === null
                                        && $dev1->getAbility() + $dev2->getAbility() == $task->getDifficulty()
                                        && $dev1->getCapacity() >= $task->getDuration()
                                        && $dev2->getCapacity() >= $task->getDuration()) {
                                        $dev1->setCapacity($dev1->getCapacity() - $task->getDuration());
                                        $dev2->setCapacity($dev2->getCapacity() - $task->getDuration());
                                        $dev1->setActiveTask($task);
                                        $dev2->setActiveTask($task);
                                        unset($this->tasks[$key]);
                                        $finishedTasks[] = $task;
                                        $taskAssigned = true;

                                        $taskAssignments[] = [
                                            'task' => $task->getId(),
                                            'developers' => [$dev1->getName(), $dev2->getName()]
                                        ];
                                        break 3;
                                    }
                                }
                            }
                        } elseif ($developer->getAbility() === $task->getDifficulty() && $developer->getCapacity() >= $task->getDuration()) {
                            $developer->setCapacity($developer->getCapacity() - $task->getDuration());
                            $developer->setActiveTask($task);
                            unset($this->tasks[$key]);
                            $finishedTasks[] = $task;
                            $taskAssigned = true;

                            $taskAssignments[] = [
                                'task' => $task->getId(),
                                'developers' => [$developer->getName()]
                            ];
                            break;
                        }
                    }
                }
            }

            if (!$taskAssigned) {
                foreach ($this->developers as $developer) {
                    $developer->setCapacity(45);
                    $developer->clearActiveTask();
                }
                $weekCount++;
            }
        }

        $remainingCapacities = [];
        foreach ($this->developers as $developer) {
            $remainingCapacities[$developer->getId()] = $developer->getCapacity();
        }
        sort($remainingCapacities);

        return [
            'weekCount' => $weekCount,
            'extraTime' => 45 - $remainingCapacities[0],
            'taskCount' => $totalTaskCount,
            'finishedTasksCount' => count($finishedTasks),
            'unfinishedTasksCount' => count($this->tasks),
            'taskAssignments' => $taskAssignments
        ];
    }
}
