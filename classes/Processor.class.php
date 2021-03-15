<?php

require_once MODEL_PATH . 'Salesman.class.php';
require_once MODEL_PATH . 'Customer.class.php';
require_once MODEL_PATH . 'Sale.class.php';
	
class Processor {
	
	private $salesmenList = [];
	private $customersList = [];
	private $salesList = [];
	
	private $salesmenSalarySum = 0.0;
	private $mostExpensiveSale = 0;
	private $worstSalesman = '';
	
	public $failedLines = [];
	
	public function processFile( string $path, string $fileName ) : void {
		
		$lines = file( $path . $fileName);
		sort( $lines ); // Ordenação para garantir que os vendedores sejam registrados antes da venda
		
		foreach ( $lines as $line ) {
		
			$this->processLine( trim( $line) );
		
		}
	
	}
	
	private function processLine( string $line ) : void {
		
		$item = explode( ',', $line);
		
		switch ($item[0]) {
			
			// Salesman
			case '001':
				
				if ( count( $item ) != 4) {
					
					$this->setFailedLine($line, 'O registro não está no formato esperado.');
					
				} else {
					
					$salesman = new Salesman($item[1], $item[2], $item[3]);
					if ( !empty( $salesman->getErrors() ) ) {
						
						foreach ($salesman->getErrors() as $error) {
							$this->setFailedLine($line, $error);
						}
						
					} else {
						
						array_push($this->salesmenList, $salesman);
						$this->salesmenSalarySum += $salesman->getSalary();
						
					}
					
				}
				break;
			
			// Customer
			case '002':
				
				if ( count( $item ) != 4) {
					
					$this->setFailedLine($line, 'O registro não está no formato esperado.');
					
				} else {
					
					$customer = new Customer($item[1], $item[2], $item[3]);
					
					if ( !empty( $customer->getErrors() ) ) {
						
						array_push($this->customersList, $customer);
						
					} else {
						
						foreach ($customer->getErrors() as $error) {
							$this->setFailedLine($line, $error);
						}
						
					}
				}
				break;
			
			// Sale
			case '003':

				$salesStart = strpos($line, '[') + 1;
				$salesEnd = strpos( $line, ']') - $salesStart;

				$saleItemsLine = substr( $line, $salesStart, $salesEnd);
				$saleItemsList = $this->processSaleItems( $saleItemsLine );

				// Reprocessando a linha sem os itens, na falta de opção melhor atualmente
				$newline = str_replace($saleItemsLine, '', $line);
				$item = explode( ',', $newline);

				if ( count( $item ) != 4) {

					$this->setFailedLine($line, 'O registro não está no formato esperado.');

				} elseif ( !strpos( $line, '[') || !strpos( $line, ']') ) {
					
					$this->setFailedLine($line, 'As vendas não foram informadas corretamente.');
					
				}
				
				else {

					$sale = new Sale($item[1], $saleItemsList, $item[3] );

					if ( !empty($sale->getErrors() ) ) {

						foreach ($sale->getErrors() as $error) {
							$this->setFailedLine($line, $error);
						}

					} elseif ( !$this->saleslmanExists( $item[3] ) ) {

						$this->setFailedLine($line, 'O vendedor ' . $item[3] . ' não está cadastrado no sistema');

					} else {

						array_push($this->salesList, $sale);

					}

				}

				break;
			
			default:
				
				// Se não for uma linha vazia, registra o erro
				if( trim($line) != '')
					$this->setFailedLine( $line, 'Identificador do tipo de entidade não reconhecido');
				
				break;
				
		}
	
	}
	
	private function setFailedLine( string $line, string $reason ) : void {
		
		if ( !array_key_exists( $line, $this->failedLines) )
			$this->failedLines[ $line ] = [];
		
		array_push( $this->failedLines[ $line ], $reason);
	
	}
	
	private function saleslmanExists( $name ) : bool {
		
		foreach ( $this->salesmenList as $list => $salesman ) {
			
			if ( $salesman->getName() == $name ) {
				return true;
			}
		
		}
		
		return false;
	
	}
	
	private function processSaleItems( string $saleItems ) : array {
		
		return [];
	
	}
	
	private function getCustomersQuantity() : int {
		return count( $this->customersList );
	}
	
	private function getSalesmenQuantity() : int {
		return count( $this->salesmenList );
	}
	
	private function getSalesmenAverageWage() : float {
		return $this->getSalesmenQuantity() == 0 ? 0 : $this->salesmenSalarySum / $this->getSalesmenQuantity();
	}
	
	private function getMostExpensiveSale() : float {
		return $this->getSalesmenQuantity() == 0 ? 0 : $this->mostExpensiveSale;
	}
	
	public function getProcessReport() : string {
		
		$report = '|RELATÓRIO|
Quantidade de clientes: ' . $this->getCustomersQuantity() . '
Quantidade de vendedores: ' . $this->getSalesmenQuantity() . '
Média salarial dos vendedores: ' . $this->getSalesmenAverageWage() . '
Venda mais cara: ' . 'TODO
Vendedor menos produtivo: ' . 'TODO

--------------------------------
Linhas não processadas: ' . count( $this->failedLines ) . '

';

		foreach ( $this->failedLines as $fail => $errors ) {
			
			$report .= trim( $fail);
			
			foreach ($errors as $error => $desc) {
		
				$report .= '
- ' .$desc;
			
			}

			$report .= '
			
';
		}
		
		// DEBUG! REMOVER OU COMENTAR!!!!
//		echo '<pre>' . $report;
//
//		dd( $this->salesList);
		
		return $report;
	
	}

}