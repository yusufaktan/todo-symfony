<?php

namespace App\Command;

use App\Entity\Developer;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

class AddDeveloperCommand extends Command
{
    protected static $defaultName = 'app:add-developer';
    private $entityManager;

    public function __construct(EntityManagerInterface $entityManager){
        $this->entityManager = $entityManager;
        parent::__construct();
    }

    protected function configure(): void
    {
        $this->setDescription('Add developer');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $developers = [
            ['name' => 'DEV1', 'ability' => 1, 'capacity' => 45],
            ['name' => 'DEV2', 'ability' => 2, 'capacity' => 45],
            ['name' => 'DEV3', 'ability' => 3, 'capacity' => 45],
            ['name' => 'DEV4', 'ability' => 4, 'capacity' => 45],
            ['name' => 'DEV5', 'ability' => 5, 'capacity' => 45]
        ];

        foreach ($developers as $developer) {
            $newDeveloper = new Developer();
            $newDeveloper->setName($developer['name']);
            $newDeveloper->setAbility($developer['ability']);
            $newDeveloper->setCapacity($developer['capacity']);
            $this->entityManager->persist($newDeveloper);
            $output->writeln("{$newDeveloper->getName()} created");
        }
        $this->entityManager->flush();

        $output->writeln("SUCCESS");
        return 0;
    }
}
