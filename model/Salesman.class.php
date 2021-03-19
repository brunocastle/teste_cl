<?php

require_once MODEL_PATH . 'Model.class.php';
	
class Salesman extends Model {

	private $cpf = '';
	private $name = '';
	private $salary = 0.0;
	
	
	public function __construct( string $cpf, string $name, float $salary ) {
		$this->setCpf($cpf );
		$this->setName( $name );
		$this->setSalary( $salary );
	}
	
	public function getCpf(): string {
		return $this->cpf;
	}
	
	public function setCpf(string $cpf): void {
		
		$cpf = str_replace( ['.', '-'], '', $cpf );
		
		if ( strlen( $cpf) != 11 )
			$this->setError( 'CPF inválido: ' . $cpf . ' não possui a quantidade de caracteres de um CPF.');
		
		if ( !ctype_digit( $cpf ) )
			$this->setError( 'CPF inválido: ' . $cpf . ' não possui apenas números.');
		
		$this->cpf = $cpf;
	}
	
	public function getName(): string {
		return $this->name;
	}
	
	public function setName(string $name): void {
		
		// Buscando números no nome do vendedor
		$numbers = filter_var( $name, FILTER_SANITIZE_NUMBER_INT );
		if ( $numbers != "" ) {
			$this->setError( 'Nome inválido. O nome do vendedor ' . $name . ' contém números ou sinais gráficos.');
		}
		
		// Buscando caracteres especiais no nome do vendedor
		$specialChars = preg_replace( '/^[a-záàâãéèêíïóôõöúçñ ]+$/i', '', $name);
		
		if ( $specialChars != "" ) {
			$this->setError('Nome inválido. O nome do vendedor ' . $name . ' contém caracteres especiais.');
		}
		
		$this->name = $name;
		
	}
	
	public function getSalary(): float {
		return $this->salary;
	}
	
	public function setSalary( float $salary): void {
		
		if ( $salary <= 0 )
			$this->setError( 'Salário inválido: ' . $salary . ' não é um valor de salário válido (Valores não numéricos são considerados 0)' );
		
		$this->salary = $salary;
	}
	
}