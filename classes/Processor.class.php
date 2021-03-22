<?php

require_once MODEL_PATH . 'Salesman.class.php';
require_once MODEL_PATH . 'Customer.class.php';
require_once MODEL_PATH . 'Sale.class.php';
require_once MODEL_PATH . 'SaleItem.class.php';
	
class Processor {
	
	private $salesmenList = [];
	private $customersList = [];
	private $salesList = [];
	
	private $salesmenSalarySum = 0.0;
	
	private $mostExpensiveSale = ['value' => 0.0, 'id' => 0, 'salesman' => '' ];
	private $worstSalesman = '';
	
	public $successLines = [];
	public $failedLines = [];
	
	public function processFile( string $path, string $fileName ) : void {
		
		$lines = file( $path . $fileName);
		sort( $lines ); // Ordenação para garantir que os vendedores sejam registrados antes das vendas
		
		foreach ( $lines as $line ) {
		
			$this->processLine( trim( $line) );
		
		}
	
	}
	
	private function processLine( string $line ) : void {
		
		// Busca nas o código de tipo de entidade nos primeiros 3 caracteres da linha
		$code = substr( $line, 0, 3);
		
		switch ( $code ) {
			
			// Salesman
			case '001':
				
				$salesman = $this->processSalesman( $line );
				
				if ( is_a( $salesman, 'Salesman' ) ) {
					array_push( $this->successLines, $line);
					array_push($this->salesmenList, $salesman);
					$this->salesmenSalarySum += $salesman->getSalary();
				}
				
				break;
			
			// Customer
			case '002':

				$customer = $this->processCustomer( $line );
				
				if ( is_a( $customer, 'Customer' ) ) {
					array_push( $this->successLines, $line);
					array_push($this->customersList, $customer);
				}
				
				break;

			// Sale
			case '003':
				
				$sale = $this->processSale( $line );
				
				if ( is_a( $sale, 'Sale' ) ) {
					array_push( $this->successLines, $line);
					array_push($this->salesList, $sale);
				}
				
				break;
			
			default:
				
				// Linhas vazias não são retornadas como erros, mas qualquer outra não aceita nos casos acima, sim
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
	
	private function processSalesman( string $line ) : ?Salesman {
	
		$data = explode( ',', $line );
		
		if ( count( $data) != 4 ) {
			
			$this->setFailedLine($line, 'O registro não está no formato esperado.');
			
		} else {
		
			$salesman = new Salesman($data[1], $data[2], floatval($data[3]));
			
			if ( !empty( $salesman->getErrors() ) ) {
				foreach ($salesman->getErrors() as $error) {
					$this->setFailedLine($line, $error);
				}
			} else {
				return $salesman;
			}
		
		}
		
		return null;
		
	}
	
	private function processCustomer( string $line ) : ?Customer {
		
		$data = explode( ',', $line );
	
		if ( count( $data ) != 4) {
	
			$this->setFailedLine($line, 'O registro não está no formato esperado.');
	
		} else {
	
			$customer = new Customer($data[1], $data[2], $data[3] );
			if ( !empty( $customer->getErrors() ) ) {
				foreach ($customer->getErrors() as $error) {
					$this->setFailedLine($line, $error);
				}
			} else {
				return $customer;
			}
		}
		
		return null;
	
	}
	
	private function processSaleItem( string $line ) : ?SaleItem {
	
		$data = explode( '-', $line );
		
		if ( count( $data ) != 3) {
			
			$this->setFailedLine($line, 'O registro não está no formato esperado.');
			
		} else {
			
			$saleItem = new SaleItem( intval($data[0]), intval($data[1]), floatval($data[2]));
			if ( !empty( $saleItem->getErrors() ) ) {
				foreach ($saleItem->getErrors() as $error) {
					$this->setFailedLine($line, $error);
				}
			} else {
				return $saleItem;
			}
			
		}
		
		return null;
		
	}
	
	private function getSaleItems( string $line ) : ?array {
	
		$itemsLine = $this->getSaleItemsLine( $line );
		$items = [];
		
		if ( !is_null( $itemsLine) ) {
		
			$itemsGroup = explode(',', $itemsLine);
			foreach ($itemsGroup as $item) {
		
				$saleItem = $this->processSaleItem($item);
		
				if (is_a($saleItem, 'SaleItem')) {
					array_push($items, $saleItem);
				}
				
			}
			
		}
		
		return $items;
		
	}
	
	private function getSaleItemsLine( string $line ) : ?string {
//		$line = '003,15,1-30-100, 2-30-2.50, 3-40-3.10 ,Manolo';
		$items = null;
		$itemsStart = strpos($line, '[');
		$itemsEnd = strpos( $line, ']');
		
		if ( $itemsStart && $itemsEnd ) {
			$start = $itemsStart + 1;
			$end = $itemsEnd - $itemsStart;
			$items = substr( $line, $start, $end );
		}
		
		return $items;
		
	}
	
	private function processSale( string $line ) : ?Sale {
		
		$items = $this->getSaleItems( $line );
		$itemsLine = $this->getSaleItemsLine( $line );

		// Os dados são verificado sem os itens, pois estes já foram processados
		$data = explode( ',', str_replace($itemsLine, '', $line) );

		if ( count( $data ) != 4) {

			$this->setFailedLine($line, 'O registro não está no formato esperado.');

		} elseif ( is_null( $items ) ) {

			$this->setFailedLine($line, 'As vendas não foram informadas corretamente.');

		} elseif ( !is_numeric($data[1] ) ) {

			$this->setFailedLine( $line, 'O identificador da venda deve ser um número inteiro.');
			
		} elseif ( !$this->salesmanExists( $data[3] ) ) {

			$this->setFailedLine($line, 'O vendedor ' . $data[3] . ' não está cadastrado no sistema');

		} else {

			$sale = new Sale(intval($data[1]), $items, $data[3] );

			if ( !empty($sale->getErrors() ) ) {

				foreach ($sale->getErrors() as $error) {
					$this->setFailedLine($line, $error);
				}
				
			} else {

				array_push($this->salesList, $sale);
				if ( $this->mostExpensiveSale['value'] <= $sale->getSaleValue() ) {
					$this->mostExpensiveSale['value'] = $sale->getSaleValue();
					$this->mostExpensiveSale['id'] = $sale->getId();
					$this->mostExpensiveSale['salesman'] = $sale->getSalesmanId();
				}
				return $sale;
			}

		}
		
		return null;
		
	}
	
	private function salesmanExists( $name ) : bool {
		
		foreach ( $this->salesmenList as $list => $salesman ) {
			
			if ( $salesman->getName() == $name ) {
				return true;
			}
		
		}
		
		return false;
	
	}
	
	private function getCustomersQuantity() : int {
		return count( $this->customersList );
	}
	
	private function getSalesmenQuantity() : int {
		return count( $this->salesmenList );
	}
	
	private function getSalesmenAverageWage() : float {
		if ( $this->getSalesmenQuantity() > 0 ) {
			return number_format( $this->salesmenSalarySum / $this->getSalesmenQuantity(), 2, '.', '');
		}
		return 0;
	}
	
	private function getMostExpensiveSale() : string {
		return $this->getSalesmenQuantity() == 0 ? 0 : 'Venda #' .$this->mostExpensiveSale['id'] . ', no valor de R$ ' . $this->mostExpensiveSale['value'] . ' por ' . $this->mostExpensiveSale['salesman'];
	}
	
	private function getWorstSalesman() : string {
		
		$salesmen = [];
		
		// Preenche um array com os nomes dos vendedores como indexes, e valor float zero para todos.
		foreach ( $this->salesmenList as $saleman ) {
			if( !isset( $salesmen[ $saleman->getName() ] )) {
				$salesmen[ $saleman->getName() ] = 0.0;
			}
		}
		
		// Aplica o valor somado de todas as vendas dos vendedores indexados
		// OBS: Os indexes não precisam ser verificados nesta parte,
		// pois vendas com nomes de vendedores que não existem, não são cadastradas.
		foreach ( $this->salesList as $sale ) {
			$salesmen[ $sale->getSalesmanid() ] += $sale->getSaleValue();
		}
		
		//Organiza os valores das vendas somadas do maior para o menor e retorna o índice do valor mais baixo como pior vendedor
		//OBS: Verificar com a liderança o que fazer em caso de empates
		arsort( $salesmen );
		return array_key_last( $salesmen);
	
	}

	private function getSuccessLines() : string {
		$info = '';
		foreach ( $this->successLines as $line ) {
			$info .= $line . '
';
		}
		return $info;
	}
	
	private function getFailedLines() : string {
		$info = '';
		foreach ( $this->failedLines as $fail => $errors ) {
			$info .= trim( $fail ) . '
';
			foreach ( $errors as $error => $desc ) {
				$info .= '- ' . $desc . '
';
			}
			$info .= '
';
		}
		return $info;
	}

	public function getProcessReport() : string {
		
		$report = '|RELATÓRIO|
Quantidade de clientes: ' . $this->getCustomersQuantity() . '
Quantidade de vendedores: ' . $this->getSalesmenQuantity() . '
Média salarial dos vendedores: ' . $this->getSalesmenAverageWage() . '
Venda mais cara: ' . $this->getMostExpensiveSale() . '
Vendedor menos produtivo: ' . $this->getWorstSalesman() . '

------------------------------------------------------------------------------------------------
Linhas processadas: ' . count( $this->successLines ) . '

'.

$this->getSuccessLines()
.
'
------------------------------------------------------------------------------------------------
Linhas não processadas: ' . count( $this->failedLines ) . '

' .
$this->getFailedLines();
		
//		// DEBUG! REMOVER OU COMENTAR!!!!
//		echo '<pre>' . $report;
//
//		dd();
		
		return $report;
	
	}

}