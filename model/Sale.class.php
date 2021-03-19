<?php

require_once MODEL_PATH . 'Model.class.php';

class Sale extends Model {
	
	private $id = 0;
	private $items = [];
	private $salesmanId = '';
	

	public function __construct( int $id, array $items, string $salesmanId ) {
		
		$this->setId( $id );
		$this->setItems( $items );
		$this->setSalesmanId( $salesmanId );
		// Verificar com o requerente se é para ser o ID ou apenas o nome (como entregue no arquivo de referência).
	
	}
	
	public function getId(): int {
		return $this->id;
	}
	
	public function setId(int $id): void {
		$this->id = $id;
	}
	
	public function getItems(): array {
		return $this->items;
	}
	
	public function setItems(array $items): void {
		$this->items = $items;
	}
	
	public function getSalesmanId(): string {
		return $this->salesmanId;
	}
	
	public function setSalesmanId(string $salesmanId): void {
		$this->salesmanId = $salesmanId;
	}
	
	
		
}