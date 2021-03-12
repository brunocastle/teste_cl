<?php

require_once MODEL_PATH . 'Model.class.php';
	
class SaleItem extends Model {
	
	private $id = 0;
	private $quantity = 0;
	private $price = 0.0;
	
	public function __construct( $id, $quantity, $price ) {
	
	}
	
	public function getId(): int {
		return $this->id;
	}
	
	public function setId(int $id): void {
		$this->id = $id;
	}
	
	public function getQuantity(): int {
		return $this->quantity;
	}
	
	public function setQuantity(int $quantity): void {
		$this->quantity = $quantity;
	}

	public function getPrice(): float {
		return $this->price;
	}
	
	public function setPrice(float $price): void {
		$this->price = $price;
	}
	
	
		
}