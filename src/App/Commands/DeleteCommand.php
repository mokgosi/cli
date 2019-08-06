<?php
namespace App\Commands;
 
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Question\ConfirmationQuestion;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Question\Question;
use Symfony\Component\Console\Question\ChoiceQuestion;
use Symfony\Component\Filesystem\Exception\IOExceptionInterface;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Finder\Finder;
use Symfony\Component\Console\Helper\Table;

class DeleteCommand extends Command
{
    protected function configure()
    {
        $this->setName('delete')
            ->setDescription('Delete student')
            ->addArgument('studentId', InputArgument::REQUIRED, 'Student Id.');

    }
 
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $helper = $this->getHelper('question');
        $question = new ConfirmationQuestion('Continue with this action? ', false, '/^(y|j)/i');

        if (!$helper->ask($input, $output, $question)) {
            return;
        }

        $filesystem = new Filesystem();

        try {
            
            $dir = 'studentsdb/'.substr($input->getArgument('studentId'), 0,2).'/';
            $file = $dir.$input->getArgument('studentId').'.json';

            if($filesystem->exists($file)) {
                $filesystem->remove($file);
                $output->writeln('User successfuly deleted!');
            }
            
        } catch (IOExceptionInterface $exception) {
            echo "Student record not found ".$exception->getPath();
        }
    }
}