<?php

require_once 'core/config.php';
require_once PROJECT_PATH . 'core/utils.php';
require_once CLASSES_PATH . 'Processor.class.php';
require_once CLASSES_PATH . 'FileHandler.class.php';


if ( $_GET['file'] ) {
	
	$readPath = 'data/in/';
	$writePath = 'data/out/';
	$fileName = $_GET['file'];
	$doneFileName = FileHandler::changeFileExtension($fileName, '.done.dat' );
	
	$processor = new Processor;
	$processor->processFile( $readPath, $fileName );
	
	FileHandler::writeFile( $writePath, $doneFileName, 'w', $processor->getProcessReport() );

}
	
header( 'Location: index.php');