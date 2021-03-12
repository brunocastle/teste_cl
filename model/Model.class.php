<?php
	
abstract class Model {

	private $errors = []; // Array para receber os erros gerados ao criar um novo objeto
	
	public function getErrors(): array {
		return $this->errors;
	}
	
	public function setError(string $error): void {
		array_push( $this->errors, $error);
	}
		
}