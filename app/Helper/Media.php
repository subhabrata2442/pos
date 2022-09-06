<?php

namespace App\Helper;

use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;

trait Media
{

    public static function uploads($file, $path)
    {
        if ($file) {

            $fileName   = time() . $file->getClientOriginalName();
            Storage::disk('public')->put($path . '/' . $fileName, File::get($file));
            $file_name  = $file->getClientOriginalName();
            $file_type  = $file->getClientOriginalExtension();
            $filePath   = 'storage/' . $path . '/' . $fileName;

            return $file = [
                'fileName' => $file_name,
                'fileType' => $file_type,
                'filePath' => $filePath,
            ];
        }
    }
	
	public static function create_slug($string){
		$replace = '-';
	   	$string = strtolower($string);
	   //replace / and . with white space
	   	$string = preg_replace("/[\/\.]/", " ", $string);
	   	$string = preg_replace("/[^a-z0-9_\s-]/", "", $string);

	   //remove multiple dashes or whitespaces
	   	$string = preg_replace("/[\s-]+/", " ", $string);
	   
	   //convert whitespaces and underscore to $replace
	  	 $string = preg_replace("/[\s_]/", $replace, $string);

	   //limit the slug size
	  	 $string = substr($string, 0, 100);
	   
	   //slug is generated
	  	 return $string;
	  }
}
