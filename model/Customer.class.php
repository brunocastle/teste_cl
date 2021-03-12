<?php

require_once MODEL_PATH . 'Model.class.php';
	
class Customer extends Model {

	private $cnpj = '';
	private $name = '';
	private $businessArea = '';
	
	
	public function __construct( $cnpj, $name, $businessArea ) {
		
		$this->setCnpj( $cnpj );
		$this->setName( $name );
		$this->setBusinessArea( $businessArea );
	
	}
	
	public function getCnpj(): string {
		return $this->cnpj;
	}
	
	public function setCnpj(string $cnpj): void {
		
		if ( strlen( $cnpj) != 14 )
			$this->setError( 'CNPJ inválido: ' . $cnpj . ' não possui a quantidade de caracteres de um CNPJ');
		
		if ( !ctype_digit( $cnpj) )
			$this->setError( 'CNPJ inválido: ' . $cnpj . ' não possui apenas números');
		
		$this->cnpj = $cnpj;
	}
	
	public function getName(): string {
		return $this->name;
	}
	
	public function setName(string $name): void {
		$this->name = $name;
	}
	
	public function getBusinessArea(): string {
		return $this->businessArea;
	}
	
	public function setBusinessArea(string $businessArea): void {
		
		$this->businessArea = removeNumbers( $businessArea );
		
	}
	
}