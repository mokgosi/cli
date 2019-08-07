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

        $this->delete($input, $output);
    }

    protected function delete($input, $output)
    {
        $filesystem = new Filesystem();

        try {
            
            $dir = 'studentsdb/'.substr($input->getArgument('studentId'), 0,2).'/';
            $file = $dir.$input->getArgument('studentId').'.json';

            if($filesystem->exists($file)) {
                $filesystem->remove($file);
                $output->writeln([
                    '=================================================================',
                    '<info>              Sudent successfuly deleted!           </info>',
                ]);
            } else {
                $output->writeln([
                    '=================================================================',
                    '<info>  Student record does not exist - no action taken!  </info>',
                ]);
            }
            
        } catch (IOExceptionInterface $exception) {
            echo "Student record not found ".$exception->getPath();
        }
    }
}