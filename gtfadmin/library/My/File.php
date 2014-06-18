<?php

/**
 * My file abstraction object.
 * @access public
 */
class My_File
{
	public function __construct() 
	{
		
	}	

	public static function uploadFile($type, $fileData, $fileName)
	{
		$ext = pathinfo($fileName, PATHINFO_EXTENSION);
		$parseFile = new My_Parse_File($type, $fileData);
		$upload = $parseFile->save('image.' . $ext);

		return $upload->request_successfull ? $upload->name : false;
	}

	public static function deleteFile($fileName){

		$parseFile = new My_Parse_File();
		$delete = $parseFile->delete($fileName);

		return $delete ? true : false;
	}
}