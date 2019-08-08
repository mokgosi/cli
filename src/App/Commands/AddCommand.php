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
use App\FileManager;
use App\StudentManager;
use App\QuestionManager;


class AddCommand extends Command
{
    /**
     * {@inheritdoc}
     */
    protected function configure()
    {
        $this->setName('add')
            ->setDescription('Create new student.');
    }
 
    /**
     * {@inheritdoc}
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $output->writeln([
            '=================================================================',
            '                          Create new student                     ',
            '=================================================================',
            '',
        ]);

        $data = [];

        $helper = $this->getHelper('question');

        $question = new QuestionManager($helper);

        $data['studentId'] = $question->question('studentId','Please enter student id: ', $input, $output);

        $student = new StudentManager;

        if($student->getStudent($data['studentId'])) {
            $output->writeln('<info>Student already exists - no action taken.</info>');
            return;
        }

        $data['name']       = $question->question('name','Please enter your first name: ', $input, $output);
        $data['surname']    = $question->question('surname','Please enter your last name: ', $input, $output);
        $data['age']        = $question->question('age','Please enter your age: ', $input, $output);
        $data['email']      = $question->question('email','Please enter your email: ', $input, $output);
        $data['curriculum'] = $question->choice($input, $output);

        $fileManager = new FileManager;
        $fileManager->save($data['studentId'], json_encode($data));

        $output->writeln([
            '=================================================================',
        ]);

        $output->writeln('Student created successfuly!');
    }

    
}