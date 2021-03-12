<?php

require_once 'core/config.php';
require_once CLASSES_PATH . 'FileHandler.class.php';
require_once CLASSES_PATH . 'Recorder.class.php';

$acceptedExtension = ['txt','dat'];
$fileFieldName = 'file';

$file = FileHandler::getFileInfo( $_FILES[$fileFieldName] );

if ( !FileHandler::isAcceptedExtension( $file['extension'], $acceptedExtension)) {
	Recorder::record('Erro', 'A extensão do arquivo submetido não é suportada');
} else {
	FileHandler::uploadFile( $file,'data/in/', date('Ymd_His') . '_sales_info.' . $file['extension'] );
}

Recorder::printAndErase();