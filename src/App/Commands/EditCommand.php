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
use App\FileManager;
use App\StudentManager;
use App\QuestionManager;


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
        $output->writeln([
            '=================================================================',
            '                          Edit student record                    ',
            '=================================================================',
            '',
        ]);
        //get student
        $studentManager = new StudentManager;
        $student = $studentManager->get($input->getArgument('studentId'));

        if(!$student) {
            $output->writeln('<info>Student record not found.</info>');
            return;
        }

        $helper = $this->getHelper('question');

        $questionManager = new QuestionManager($helper);

        $student['name']    = $questionManager->question('name', "Please enter your first name ({$student['name']}): ", $input, $output, $student);
        $student['surname'] = $questionManager->question('surname', "Please enter your last name ({$student['surname']}): ", $input, $output, $student);
        $student['age']     = $questionManager->question('age', "Please enter your age ({$student['age']}):", $input, $output, $student);
        $student['email']   = $questionManager->question('email', "Please enter your email ({$student['email']}):", $input, $output, $student);
        $student['curriculum'] = $questionManager->choice($input, $output, $student);

        $json = json_encode($student);

        //save
        $fileManager = new FileManager;
        $fileManager->save($student['studentId'], $json);

        $output->writeln([
            '=================================================================',
        ]);

        $output->writeln('Student record updated successfuly!');
    }
   



    // protected function curriculum($input, $output, $student)
    // {
    //     $helper = $this->getHelper('question');

    //     $question = new ChoiceQuestion(
    //         "<question>Please select your curriculum ({$student['curriculum']}): </question>",
    //         $options = ['(M.c.A) - Master of Computer Application',
    //         '(M.S.C) - Master Of Science', 
    //         '(B.COM) - Bachelor Of Commerce', 
    //         '(B.TECH) - Bachelor of Technology',
    //         '(M.B.A) - Master Of Business Administration',
    //         '(B.A) - Bachelor Of Arts'],
    //         array_search("curriculum",array_keys($options))
    //     );

    //     $curriculum = $helper->ask($input, $output, $question);

    //     return $curriculum;
    // }


}