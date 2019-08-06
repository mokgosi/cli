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

class EditCommand extends Command
{
    protected function configure()
    {
        $this->setName('edit')
            ->setDescription('Edit student')
            ->addArgument('studentId', InputArgument::REQUIRED, 'Student Id.');
    }
 
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        //get student
        $student = $this->getStudent($input);

        if(!$student) {
            $output->writeln('Student record not found.');
            return;
        }

        //update first name
        $student['name'] = $this->question('name', "Please enter your first name ({$student['name']}): ", $input, $output, $student);

        //update last name
        $student['surname'] = $this->question('surname', "Please enter your last name ({$student['surname']}): ", $input, $output, $student);

        //update age
        $student['age'] = $this->age($input, $output, $student);

        //update email
        $student['email'] = $this->email($input, $output, $student);

        //update curriculum
        $student['curriculum'] = $this->curriculum($input, $output, $student);

        $json = json_encode($student);

        //save
        $this->save($student['studentId'], $json);
    }

    protected function getStudent($input)
    {
        $helper = $this->getHelper('question');

        $finder = new Finder();

        $finder->files()->name($input->getArgument('studentId').'.json');

        $finder->files()->in('studentsdb');

        $array = [];

        foreach ($finder as $file) {

            $array = json_decode($file->getContents(), true);
        }

        return $array;
    } 

    protected function question($field, $question, $input, $output, $student)
    {
        $helper = $this->getHelper('question');

        // var_dump($student);

        $question = new Question($question, $student[$field]);
        $question->setNormalizer(function ($value) {
            return $value ? trim($value) : '';
        })
        ->setValidator(function ($answer) {
            if (!is_string($answer) ||  $answer === '') {
                throw new \RuntimeException(
                    "You must provide {$field}"
                );
            }

            return $answer;
        });

        return $helper->ask($input, $output, $question);
    }

    protected function age($input, $output,$student) 
    {
        $helper = $this->getHelper('question');

        $question = new Question("Please enter age ({$student['age']}): ", $student['age']);
        $question->setNormalizer(function ($value) {
            return trim($value);
        })
        ->setValidator(function ($answer) {
            if (!preg_match('/^\d+$/', $answer) ) {
                throw new \RuntimeException(
                    'You must provide a valid age.'
                );
            }

            return $answer;
        });

        $age = $helper->ask($input, $output, $question);            

        return $age;

    }

    protected function email($input, $output,$student)
    {
        $helper = $this->getHelper('question');
        $question = new Question("Please enter your email address ({$student['email']}): ", $student['email']);
        $question->setNormalizer(function ($value) {
            return $value ? trim($value) : '';
        });
        $question->setValidator(function ($answer) {
            if (!filter_var($answer, FILTER_VALIDATE_EMAIL)) {
                throw new \RuntimeException(
                    'You must provide a valid email.'
                );
            }

            return $answer;
        });

        $email = $helper->ask($input, $output, $question);

        return $email;
    }

    protected function curriculum($input, $output, $student)
    {
        $helper = $this->getHelper('question');

        $question = new ChoiceQuestion(
            "Please select your curriculum ({$student['curriculum']}): ",
            $options = ['(M.c.A) - Master of Computer Application',
            '(M.S.C) - Master Of Science', 
            '(B.COM) - Bachelor Of Commerce', 
            '(B.TECH) - Bachelor of Technology',
            '(M.B.A) - Master Of Business Administration',
            '(B.A) - Bachelor Of Arts'],
            array_search("curriculum",array_keys($options))
        );

        $curriculum = $helper->ask($input, $output, $question);

        return $curriculum;
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

    protected function save($studentId, $json)
    {
        $filesystem = new Filesystem();

        try {

            $dir = 'studentsdb/'.substr($studentId, 0,2).'/';

            $filesystem->mkdir($dir, 0700);
            
            $filesystem->dumpFile($dir.$studentId.'.json', $json );

        } catch (IOExceptionInterface $exception) {
            echo "An error occurred while creating your directory at ".$exception->getPath();
        }
    }
}