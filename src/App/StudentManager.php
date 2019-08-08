<?php
namespace App;

use Symfony\Component\Finder\Finder;

/**
 * Student class
 */
class StudentManager
{
	/**
	 * Get user record
	 */ 
	public function get($id) : array
	{

        $finder = new Finder();

        $finder->files()->name($id.'.json');
        $finder->files()->in('studentsdb');

        $contents = [];

        foreach ($finder as $file) {
            $contents = json_decode($file->getContents(), true);
            // $contents[] = array_values($array);
        }

        return $contents;
	}

	/**
     * Check if student exists
     */

    public function getStudent($id) : bool
    {
        $data = $this->get($id);

        if(count($data) > 0) {
            return true;
        } 
        return false;
    }
}
