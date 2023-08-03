<?php

namespace model;


use PDO;

class ShoppingList
{
	private PDO $pdo;

	public function __construct($host, $username, $password, $dbname)
	{
		$dsn = "mysql:host=$host;dbname=$dbname";
		try {
			$this->pdo = new PDO($dsn, $username, $password);
			$this->pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
		} catch (\Exception $e) {
			die("Connection failed: " . $e->getMessage());
		}
	}

	public function addItem($name): void {
		$sql = "INSERT INTO items (name) VALUES (:name)";
		$stmt = $this->pdo->prepare($sql);
		$stmt->bindValue(':name', $name );
		$stmt->execute();
	}

	public function deleteItem($id): void {
		$sql = "DELETE FROM items WHERE id = :id";
		$stmt = $this->pdo->prepare($sql);
		$stmt->bindValue(':id', $id, PDO::PARAM_INT);
		$stmt->execute();
	}

	public function editItem($id, $newName): void {
		$sql = "UPDATE items SET name = :newName WHERE id = :id";
		$stmt = $this->pdo->prepare($sql);
		$stmt->bindValue(':id', $id, PDO::PARAM_INT);
		$stmt->bindValue(':newName', $newName, PDO::PARAM_STR);
		$stmt->execute();
	}

	public function markItemAsChecked($id): void {
		$sql = "UPDATE items SET checked = 1 WHERE id = :id";
		$stmt = $this->pdo->prepare($sql);
		$stmt->bindValue(':id', $id, PDO::PARAM_INT);
		$stmt->execute();
	}

	public function markItemAsUnchecked($id): void {
		$sql = "UPDATE items SET checked = 0 WHERE id = :id";
		$stmt = $this->pdo->prepare($sql);
		$stmt->bindValue(':id', $id, PDO::PARAM_INT);
		$stmt->execute();
	}

	public function getItems(): array {
		$sql = "SELECT * FROM items";
		$stmt = $this->pdo->query($sql);
		$items = [];

		while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
			$item = new Item($row['name']);
			$item->checkItem($row['checked']);
			$items[] = $item;
		}

		return $items;
	}
}