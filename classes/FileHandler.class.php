<?php

require_once PROJECT_PATH . 'core/utils.php';
	
class FileHandler {

	
	// Adiciona a extensão em uma posição no array do arquivo
	public static function getFileInfo( array $file ) : array {
		
		$file['extension'] = self::getFileExtension( $file['name']);
		
		return $file;
		
	}
	
	public static function getFileExtension( string $fileName ) : string {
		
		$fileName = explode( '.', $fileName);
		
		return strtolower(end($fileName));
	}
	
	public static function changeFileExtension( string $fileName, $newExtension ) : string {
		
		$oldExtension = '.' . self::getFileExtension( $fileName );
		
		return str_replace( $oldExtension, $newExtension, $fileName );
		
	}
	
	public static function isAcceptedExtension( string $fileExtension, $accept ) : bool {
		
		if ( is_array( $accept ) ) {
			
			// Caso queira aceitar mais de uma extensão para o arquivo
			return in_array($fileExtension, $accept );
			
		} else {
			
			// Teste contra uma única extensão
			return $fileExtension == $accept;
			
		}
		
	}
	
	public static function createPathIfNotExists( $path ) : bool {
		
		if( !is_dir($path) ) {
			
			return mkdir($path, 0777, true);
			
		}
		
		return true;
		
	}
	
	public static function saveFile( array $file, string $path, string $name, string $extension = null ) : void {
		
		if ( $extension ) {
			$name = self::changeFileExtension( $name, $extension );
		}
		
		if ( !self::createPathIfNotExists( $path ) ) {
			Recorder::record('Erro', 'Erro ao criar diretório');
		}
	
		// Se não houver erro ao salvar o arquivo, retorna para a página inicial
		if ( !move_uploaded_file($file['tmp_name'], $path . $name ) ) {
			
			Recorder::record('Erro', 'Erro ao salvar o arquivo');
			
		} else {
			
			header( 'Location: index.php');
			
		}
	
	}
	
	public static function uploadFile( array $file, string $path, string $name ) : void {
		
		if ( empty(file( $file['tmp_name'] ))) {
			
			Recorder::record('Erro', 'O arquivo submetido está vazio');
			
		} else {
		
			self::saveFile( $file, $path, $name, '.dat' );
		
		}
	
	}
	
	/* Verifica se existem arquivos em uma pasta
		OBS: Procurar depois por boolean nativo que faça isso, ou ao menos
		mudar a lógica para que não itere toda a lista, mas retorne assim
		que encontrar o primeiro arquivo.*/
	public static function folderHasContent( string $path ) : bool {
	
		if( is_dir( $path ) ) {
			return !empty( self::getFilesList( $path ) );
		}
		
		return false;
	
	}
	
	public static function getFilesList( $path ) : array {
		
		$list = [];
		
		if ( $handle = opendir($path ) ) {
		 
			while ( $file = readdir( $handle ) ) {
		    	
		    	// Workaround. Não consegui me livrar desses dois itens na lista de arquivos
		    	if ( $file != '.' && $file != '..' ) {
				    array_push($list, $file);
			    }
		    	
		    }
		    
		    closedir($handle);
		}
		
		return $list;
	
	}
	
	public static function getLinkButton( string $path, string $file, $label = null, $class = 'btn-primary' ) : string {
	
		return '<a class="btn btn-sm ' . ( $class ) . '" href="' . $path . $file . '" download>'. ($label ?? $file) .'</a>';
	
	}
	
	public static function getReportLinkButton( string $file ) : string {
		
		$reportPath = 'data/out/';
		$doneFileName = self::changeFileExtension($file, '.done.dat' );
		
		if ( file_exists( $reportPath . $doneFileName )) {
		
			$link = self::getLinkButton( $reportPath, $doneFileName, 'Download', 'btn-success' );
			
		} else {
		
			$link = '<a href="process.php?file='. $file . '" class="btn btn-sm btn-secondary">Processar</a>';
		
		}
		
		
		// DEBUG! REMOVER OU COMENTAR!!!!
//		$link = '<a href="process.php?file='. $file . '" class="btn btn-sm btn-secondary">Processar</a>';
		
		return $link;
	
	}
	
	public static function writeFile( string $path, string $name, $mode, string $content ) {
		
		self::createPathIfNotExists( $path );
		
		$fileOpen = fopen( $path . $name, $mode );
		fwrite( $fileOpen, $content );
		fclose( $fileOpen );
	
	}

}