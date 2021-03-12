<?php

	function d( ...$args ) {

		foreach( $args as $arg ) {

			echo '<div><pre style="text-align: left"> ', var_dump($arg), '</pre></div>';

		}

	}

	function dd( ...$args ) {

		foreach( $args as $arg ) {

			echo '<div><pre style="text-align: left"> ', var_dump($arg), '</pre></div>';

		}

		die();

	}
	
	function removeNumbers( $data ) {
	
		$data = trim(preg_replace('/[0-9]/', '', $data) );
		return $data;
		
	}
	
	function removeSpecialCharacters ( $data ) {
		
		$data = preg_replace('/[^A-Za-ÿ\-]/', '', $data);
		$data = str_replace( '-', '', $data );
		
		return $data;
		
	}

?>