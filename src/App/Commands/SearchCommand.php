<?php
namespace App\Commands;
 
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Question\Question;
use Symfony\Component\Console\Question\ChoiceQuestion;
use Symfony\Component\Filesystem\Exception\IOExceptionInterface;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Finder\Finder;
use Symfony\Component\Console\Helper\Table;

class SearchCommand extends Command
{
    protected function configure()
    {
        $this->setName('search')
            ->setDescription('Searh student');

    }
 
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $helper = $this->getHelper('question');

        $studentIdQuestion = new Question('Please enter student id: ', '');
        $studentIdQuestion->setNormalizer(function ($value) {
            return $value ? trim($value) : '';
        });
        $studentIdQuestion->setValidator(function ($answer) {
            if (!preg_match('/^\d+$/', $answer) || strlen($answer) !== 7 ){
                throw new \RuntimeException(
                    'Student id must be numeric and 7 characters long.'
                );
            }

            return $answer;
        });

        $studentIdQuestion->setMaxAttempts(2);

        $studentId = $helper->ask($input, $output, $studentIdQuestion);

        //get data from file system
        $data['studentId'] = $studentId;

        $data = $this->getData($studentId);

        $this->renderData($output, $data);
        
    }

    protected function getData($studentId=null)
    {
        $finder = new Finder();

        $finder->files()->name('*.json');

        $finder->files()->in('studentsdb');

        $contents = [];

        foreach ($finder as $file) {

            $array = json_decode($file->getContents(), true);
            $contents[] = array_values($array);
        }

        return $contents;
    } 

    protected function renderData($output, $data) : void
    {
        $table = new Table($output);
        $table
            ->setHeaders(['Student Id', 'Name', 'Surname', 'Age', 'Email', 'Curriculum'])
            ->setRows($data)
        ;
        $table->render();
    }
}