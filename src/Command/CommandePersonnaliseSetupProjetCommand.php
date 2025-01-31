<?php

namespace App\Command;

use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\Process\Process;
use Doctrine\ORM\EntityManagerInterface;

#[AsCommand(
    name: 'commande_personnalise_setup_projet',
    description: 'Réinitialise la base de données et configure le projet',
)]
class CommandePersonnaliseSetupProjetCommand extends Command
{
    private EntityManagerInterface $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        parent::__construct();
        $this->entityManager = $entityManager;
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);

        $io->title('🚀 Début du setup du projet 🚀');

        try {
            $io->section('1) Installation des dépendances Composer...');
            $this->runCommand(['composer', 'install'], $output);

            $io->section('2) Suppression du schéma existant...');
            $this->runCommand(['php', 'bin/console', 'doctrine:schema:drop', '--force'], $output);

            $io->section('3) Suppression des entrées dans doctrine_migration_versions...');
            $this->clearMigrationVersionsTable();

            $io->section('4) Exécution des migrations...');
            $this->runCommand(['php', 'bin/console', 'doctrine:migrations:migrate', '--no-interaction'], $output);

            $io->section('5) Chargement des fixtures...');
            $this->runCommand(['php', 'bin/console', 'doctrine:fixtures:load', '--no-interaction'], $output);

            $io->success('✅ Setup du projet terminé avec succès !');

            return Command::SUCCESS;
        }
        catch (\Exception $e)
        {
            $io->error('❌ Erreur durant le setup : ' . $e->getMessage());
            return Command::FAILURE;
        }
    }

    private function runCommand(array $command, OutputInterface $output): void
    {
        $process = new Process($command);
        $process->setTimeout(null);
        $process->run(function ($type, $buffer) use ($output)
        {
            $output->write($buffer);
        });

        if (!$process->isSuccessful())
        {
            throw new \RuntimeException(sprintf('Commande échouée: %s', implode(' ', $command)));
        }
    }

    private function clearMigrationVersionsTable(): void
    {
        $connection = $this->entityManager->getConnection();
        $connection->executeStatement('DELETE FROM doctrine_migration_versions');
    }
}
