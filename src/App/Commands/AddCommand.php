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


class AddCommand extends Command
{
    protected function configure()
    {
        $this->setName('add')
            ->setDescription('Create new student.');
    }
 
    protected function execute(InputInterface $input, OutputInterface $output)
    {

        $data = [];
        
        $data['studentId'] = $this->studentId($input, $output);

        $data['name'] = $this->question('first', 'Please enter your first name: ', $input, $output);

        $data['surname'] = $this->question('last', 'Please enter your last name: ', $input, $output);

        $data['age'] = $this->age($input, $output) ;

        $data['email'] = $this->email($input, $output);

        $data['curriculum'] = $this->curriculum($input, $output);

        $json = json_encode($data);

        //save
        $this->save($data['studentId'], $json);

        $output->writeln('User successfuly created!');
    }

    protected function studentId($input, $output)
    {
        $helper = $this->getHelper('question');

        $question = new Question('Please enter student id: ', '');
        $question->setNormalizer(function ($value) {
            return $value ? trim($value) : '';
        });
        $question->setValidator(function ($answer) {
            if (!preg_match('/^\d+$/', $answer) ) {
                throw new \RuntimeException(
                    'You must provide a valid student id.'
                );
            }

            return $answer;
        });

        return $helper->ask($input, $output, $question);
    }

    protected function question($wich, $question, $input, $output)
    {
        $helper = $this->getHelper('question');

        $question = new Question($question, '');
        $question->setNormalizer(function ($value) {
            return $value ? trim($value) : '';
        })
        ->setValidator(function ($answer) {
            if (!is_string($answer) ||  $answer === '') {
                throw new \RuntimeException(
                    "You must provide {$wich} name."
                );
            }

            return $answer;
        });

        return $helper->ask($input, $output, $question);
    }

    protected function age($input, $output) 
    {
        $helper = $this->getHelper('question');

        $question = new Question('Please enter age: ', '');
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

    protected function email($input, $output)
    {
        $helper = $this->getHelper('question');
        $question = new Question('Please enter your email address: ', '');
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

    protected function curriculum($input, $output)
    {
        $helper = $this->getHelper('question');

        $question = new ChoiceQuestion(
            'Please select your course: ',
            ['(M.c.A) - Master of Computer Application',
            '(M.S.C) - Master Of Science', 
            '(B.COM) - Bachelor Of Commerce', 
            '(B.TECH) - Bachelor of Technology',
            '(M.B.A) - Master Of Business Administration',
            '(B.A) - Bachelor Of Arts']
        );

        $curriculum = $helper->ask($input, $output, $question);

        return $curriculum;
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