<?php
namespace App;

use Symfony\Component\Console\Question\Question;
use Symfony\Component\Console\Question\ChoiceQuestion;
use Symfony\Component\Filesystem\Exception\IOExceptionInterface;

class QuestionManager
{
	private $helper;

	public function __construct($helper)
	{
		$this->helper = $helper;
	}

	public function question($field, $question, $input, $output, $student=null) : String
    {
        
        $question = new Question($question, $student[$field]);
        $question->setNormalizer(function ($value) use ($field) {
            if(in_array($field, ['studentId','age'])) {
                return preg_replace('/\s+/', '', $value);
            }
            return $value ? trim($value) : '';
        })
        ->setValidator(function ($answer) use ($field) {

            if($field === 'studentId') {
                if (!preg_match('/^\d+$/', $answer) || strlen($answer) !== 7 ) {
                    throw new \RuntimeException(
                        'Field is required - must be numeric - must be 7 chars long.'
                    );
                }              
            } elseif($field === 'age') {
                if (!preg_match('/^\d+$/', $answer) || strlen($answer) > 3 ) {
                    throw new \RuntimeException(
                        'Field is required - must be numeric - not longer than 3 chars.'
                    );
                }              
            } elseif($field === 'email') {
                if (!filter_var($answer, FILTER_VALIDATE_EMAIL)) {
                    throw new \RuntimeException(
                        'Field is required - must be a valid email address.'
                    );
                }
            } else {

                if (!is_string($answer) ||  $answer === '') {
                    throw new \RuntimeException(
                        "Field is required."
                    );
                }
            }
            return $answer;
        });
        return $this->helper->ask($input, $output, $question);
    }

    public function choice($input, $output, $student=null) : String
    {
        $question = new ChoiceQuestion(
            "Please select your course by typing a related number ({$student['curriculum']}): ",
            $options = ['(M.c.A) - Master of Computer Application',
            '(M.S.C) - Master Of Science', 
            '(B.COM) - Bachelor Of Commerce', 
            '(B.TECH) - Bachelor of Technology',
            '(M.B.A) - Master Of Business Administration',
            '(B.A) - Bachelor Of Arts'],
            array_search($student["curriculum"], $options)
        );
        $question->setErrorMessage('Course %s is invalid.');

        return $this->helper->ask($input, $output, $question);
    }
}