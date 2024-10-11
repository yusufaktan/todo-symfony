<?php

namespace App\Command;

use App\Entity\Task;
use App\Service\ProviderOne;
use App\Service\ProviderTwo;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class InsertTaskCommand extends Command
{
    protected static $defaultName = 'app:insert-task';

    private $entityManager;
    private $providerOne;
    private $providerTwo;

    public function __construct(EntityManagerInterface $entityManager, ProviderOne $providerOne, ProviderTwo $providerTwo){
        parent::__construct();
        $this->entityManager = $entityManager;
        $this->providerOne = $providerOne;
        $this->providerTwo = $providerTwo;
    }

    protected function configure(): void
    {
        $this->setDescription('Insert task');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $tasks = array_merge($this->providerOne->getTasks(), $this->providerTwo->getTasks());
        foreach ($tasks as $data) {
            $task = new Task();
            if (isset($data['zorluk']) && $data['sure']) {
                $task->setDifficulty($data['zorluk']);
                $task->setDuration($data['sure']);
            }
            elseif (isset($data['value']) && $data['estimated_duration']) {
                $task->setDifficulty($data['value']);
                $task->setDuration($data['estimated_duration']);
            }
            $this->entityManager->persist($task);
            $output->writeln("Task #{$task->getId()} created");
        }
        $this->entityManager->flush();
        $output->writeln("SUCCESS");
        return 0;
    }
}