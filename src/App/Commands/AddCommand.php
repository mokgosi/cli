<?php
namespace App\Commands;
 
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Question\ConfirmationQuestion;
use Symfony\Component\Console\Question\Question;
use Symfony\Component\Console\Question\ChoiceQuestion;
use Symfony\Component\Filesystem\Exception\IOExceptionInterface;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Finder\Finder;


class AddCommand extends Command
{
    protected function configure()
    {
        $this->setName('add')
            ->setDescription('Create new student.');
    }
 
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $output->writeln([
            '=================================================================',
            '                          Create new student                     ',
            '=================================================================',
            '',
        ]);

        $data = [];
        
        $data['studentId'] = $this->studentId($input, $output);

        if($this->getStudent($data['studentId'])) {
            $output->writeln('Student already exists!');
            return;
        }

        $data['name'] = $this->question('Please enter your first name: ', $input, $output);

        $data['surname'] = $this->question('Please enter your last name: ', $input, $output);

        $data['age'] = $this->age($input, $output) ;

        $data['email'] = $this->email($input, $output);

        $data['curriculum'] = $this->curriculum($input, $output);

        $json = json_encode($data);

        //save
        $this->save($data['studentId'], $json);

        $output->writeln([
            '=================================================================',
        ]);

        $output->writeln('Student created successfuly!');
    }

    /**
     * Check if student already exists
     */

    protected function getStudent($id)
    {
        $helper = $this->getHelper('question');

        $finder = new Finder();

        $finder->files()->name($id.'.json');

        $finder->files()->in('studentsdb');

        $contents = [];

        foreach ($finder as $file) {
            $array = json_decode($file->getContents(), true);
            $contents[] = array_values($array);
        }

        if(count($contents)>0) {
            return true;
        }
        return false;
    } 

    protected function studentId($input, $output) : Int
    {
        $helper = $this->getHelper('question');

        $question = new Question('Please enter student id: ', '');
        $question->setNormalizer(function ($value) {
            return $value ? trim($value) : '';
        });
        $question->setValidator(function ($answer) {
            if (!preg_match('/^\d+$/', $answer) || strlen($answer) !== 7 ) {
                throw new \RuntimeException(
                    'Field is required - must be numeric - must be 7 chars long.'
                );
            }

            return $answer;
        });

        $question->setMaxAttempts(2);

        return (int)$helper->ask($input, $output, $question);
    }

    protected function question($question, $input, $output) : String
    {
        $helper = $this->getHelper('question');

        $question = new Question($question, '');
        $question->setNormalizer(function ($value) {
            return $value ? trim($value) : '';
        })
        ->setValidator(function ($answer) {
            if (!is_string($answer) ||  $answer === '') {
                throw new \RuntimeException(
                    "Field is required."
                );
            }

            return $answer;
        });

        return $helper->ask($input, $output, $question);
    }

    protected function age($input, $output) : Int
    {
        $helper = $this->getHelper('question');

        $question = new Question('Please enter age: ', '');
        $question->setNormalizer(function ($value) {
            return $value ? trim($value) : '';
        })
        ->setValidator(function ($answer) {
            if (!preg_match('/^\d+$/', $answer) || strlen($answer) > 3 ) {
                throw new \RuntimeException(
                    'Field is required - must be numeric - not longer than 3 chars.'
                );
            }

            return $answer;
        });

        return (int)$helper->ask($input, $output, $question);            
    }

    protected function email($input, $output) : String
    {
        $helper = $this->getHelper('question');
        $question = new Question('Please enter your email address: ', '');
        $question->setNormalizer(function ($value) {
            return $value ? trim($value) : '';
        });
        $question->setValidator(function ($answer) {
            if (!filter_var($answer, FILTER_VALIDATE_EMAIL)) {
                throw new \RuntimeException(
                    'Field is required - must be a valid email address.'
                );
            }

            return $answer;
        });

        return $helper->ask($input, $output, $question);
    }

    protected function curriculum($input, $output) : String
    {
        $helper = $this->getHelper('question');

        $question = new ChoiceQuestion(
            'Please select your course by typing a related number: ',
            ['(M.c.A) - Master of Computer Application',
            '(M.S.C) - Master Of Science', 
            '(B.COM) - Bachelor Of Commerce', 
            '(B.TECH) - Bachelor of Technology',
            '(M.B.A) - Master Of Business Administration',
            '(B.A) - Bachelor Of Arts'],
            0
        );
        $question->setErrorMessage('Course %s is invalid.');

        return $helper->ask($input, $output, $question);
    }

    protected function save($studentId, $json) : void
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