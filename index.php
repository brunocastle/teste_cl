<?php
	
	date_default_timezone_set('America/Sao_Paulo');
	
	require_once 'core/config.php';
	require_once 'core/utils.php';
	require_once CLASSES_PATH . 'FileHandler.class.php';
	require_once CLASSES_PATH . 'Recorder.class.php';
	
?>
<!DOCTYPE html>
<html lang="pt-br">
	<head>
		<meta charset="utf-8"/>
		<title>localhost</title>
		<link rel="stylesheet" href="resources/css/bootstrap.min.css"/>
	</head>
	<body class="bg-light">
		<div class="col-10 offset-1 my-xl-5">
			<img class="mx-auto d-block" src="resources/images/ck_logo.png" alt="checklistfacil"/>
		</div>
		<div class="row col-6 offset-3 my-xl-5 justify-content-center">
			<h4 class="text-center">Prova Técnica para o Cargo de Programador Web</h4>
			<div>
				<form class="form-inline center mt-3" method="post" action="upload.php" enctype="multipart/form-data">
					<div class="form-group">
						<input type="file" name="file" class="form-control-file" required/>
					</div>
					<div class="form-group">
						<input type="submit" value="Importar" class="btn btn-sm btn-primary ml-2"/>
					</div>
				</form>
			</div>
		</div>
		<div class="row col-6 offset-3 my-xl-5 justify-content-center">
			<?php
				
				$path = 'data/in/';
				
				if ( FileHandler::folderHasContent($path ) ) {
				
					$files = FileHandler::getFilesList( $path );
					
					echo '
					<table class="table text-center">
						<thead class="thead-light">
							<tr>
								<th scope="col">Arquivos</th>
							</tr>
						</thead>
						<tbody>';
							
							foreach ( $files as $file ) {
								
								echo '<tr><td>' . $file . ' ' . FileHandler::getLinkButton($path, $file, 'Download' ) . ' ';
								
								echo FileHandler::getReportLinkButton( $file ) . '</td></tr>';
									
//								echo '<td><a href="process.php?file='. $file . '" class="btn btn-sm btn-success">Processar</a></td></tr>';
								
							}
					echo '
						</tbody>
					</table>
					';
					
				}
			?>
		</div>
	</body>
</html>
