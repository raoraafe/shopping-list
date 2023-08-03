<?php

namespace model;
class Item {

	private string $name;
	private bool $checked;

	public function __construct( $name ) {
		$this->name    = $name;
		$this->checked = false;
	}

	public function getName(): string {
		return $this->name;
	}

	public function isChecked(): bool {
		return $this->checked;
	}

	public function checkItem(): void {
		$this->checked = true;
	}

	public function uncheckItem(): void {
		$this->checked = false;
	}
}