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

        //search by id, name, surname, age, or by curriculum

        $question = new ChoiceQuestion(
            'Please select your search criteria by typing a related number: (default=0)',
            ['All',
            'Search by student id',
            'Search by name',
            'Search by surname', 
            'Search by age', 
            'Search by curriculum'],
            0
        );
        $question->setErrorMessage('Selected criteria %s is invalid.');

        $criteria = (string)$helper->ask($input, $output, $question);

        switch($criteria) {
            case 'Search by student id':
                $keyword = $this->searchByStudentId($input, $output); 
                break;
            case 'Search by name':
                $keyword = $this->searchByField($input, $output, 'name', 'Please enter name: ');
                break;
            case 'Search by surname':
                $keyword = $this->searchByField($input, $output, 'surname', 'Please enter surname: ');
                break;
            case 'Search by age':
                $keyword = $this->searchByField($input, $output, 'age', 'Please enter age: ');
                break;
            case 'Search by curriculum':
                $keyword = $this->searchByChoice($input, $output);
                break;
            default:
                $keyword = '';
                break;
        } 

        $data = $this->getData($keyword);

        $this->renderData($output, $data);
        
    }

    protected function searchByStudentId($input, $output) 
    {
        $helper = $this->getHelper('question');
        $question = new Question('Please enter student id: ', '');
        $question->setNormalizer(function ($value) {
             return $value ? trim($value) : '';
        });
        $question->setValidator(function ($answer) {
             if (!preg_match('/^\d+$/', $answer) || strlen($answer) !== 7 ){
                 throw new \RuntimeException(
                     'Student id required - must be numeric - must be 7 chars long.'
                 );
            }
             return $answer;
        });

        $question->setMaxAttempts(2);

        return $helper->ask($input, $output, $question);

    }

    protected function searchByChoice($input, $output) : String
    {
        $helper = $this->getHelper('question');

        $question = new ChoiceQuestion(
            'Please select your curriculum by typing a related number: ',
            ['(M.c.A) - Master of Computer Application',
            '(M.S.C) - Master Of Science', 
            '(B.COM) - Bachelor Of Commerce', 
            '(B.TECH) - Bachelor of Technology',
            '(M.B.A) - Master Of Business Administration',
            '(B.A) - Bachelor Of Arts'],
            0
        );
        $question->setErrorMessage('Curriculum %s is invalid.');

        return $helper->ask($input, $output, $question);
    }

    protected function searchByField($input, $output, $field, $question) : String 
    {
        $helper = $this->getHelper('question');
        $question = new Question($question, '');
        $question->setNormalizer(function ($value) {
             return $value ? trim($value) : '';
        });
        $question->setValidator(function ($answer) {
            if (!is_string($answer) ||  $answer === '') {
                throw new \RuntimeException(
                    "Field is required."
                );
            }

            return $answer;
        });

        $question->setMaxAttempts(2);

        return $helper->ask($input, $output, $question);
    }

    protected function getData($studentId=null)
    {
        $finder = new Finder();

        if(is_numeric($studentId) & strlen($studentId) === 7) {
            $finder->files()->name($studentId.'.json');
        } else {
            $finder->files()->name('*.json');
        }

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