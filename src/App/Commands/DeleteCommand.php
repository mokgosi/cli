<?php
namespace App\Commands;
 
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Question\ConfirmationQuestion;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Question\Question;
use App\FileManager;

class DeleteCommand extends Command
{
    /**
     * {@inheritdoc}
     */
    protected function configure()
    {
        $this->setName('delete')
            ->setDescription('Delete student')
            ->addArgument('studentId', InputArgument::REQUIRED, 'Student Id.');
    }
 
    /**
     * {@inheritdoc}
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $helper = $this->getHelper('question');
        $question = new ConfirmationQuestion(
            '<question>Are you sure you want to proceed ?</question> (y/N)', 
            false
        );

        if (!$helper->ask($input, $output, $question)) {
            $output->writeln([
                '=================================================================',
                '<info>        Command halted. Nothing has been done.      </info>',
            ]);
            return;
        }

        //Delete file
        $fileManager = new FileManager;

        $fileManager->delete($input, $output);
    }
}