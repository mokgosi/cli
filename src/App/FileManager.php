<?php
namespace App;

use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Filesystem\Exception\IOExceptionInterface;


class FileManager 
{
	public function save($studentId, $json) : void
    {
        $filesystem = new Filesystem();

        try {

            $dir = 'studentsdb/'.substr($studentId, 0,2).'/';

            $filesystem->mkdir($dir, 0700);
            
            $filesystem->dumpFile($dir.$studentId.'.json', $json );

        } catch (IOExceptionInterface $exception) {
            echo "<error>An error occurred while creating your directory at </error>".$exception->getPath();
        }
    }

	public function delete($input, $output) : void
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