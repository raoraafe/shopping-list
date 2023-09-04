<?php
namespace model;

use PDO;
use PDOException;

class ItemRepository
{
    private PDO $pdo;

    public function __construct(PDO $pdo)
    {
        $this->pdo = $pdo;
    }

    public function addItem(Item $item): array
    {
        try {
            // preparing sql statement
            $stmt = $this->pdo->prepare("INSERT INTO items (name, checked) VALUES (:name, :checked)");

            // binding parameters
            $name = $item->getName();
            $checked = $item->isChecked() ? 1 : 0;

            $stmt->bindParam(':name', $name);
            $stmt->bindParam(':checked', $checked);

            // query execution
            $stmt->execute();

            return [
                'success' => true,
                'data' => $this->getItems($this->pdo->lastInsertId())
            ]; // Successful insertion
        } catch (PDOException $e) {
            // Handle any database errors
            // You might want to log the error or throw an exception
            return ['success' => false, 'error' => $e->getMessage()]; // Failed insertion
        }
    }

    public function deleteItem($itemId): array
    {
        try {
            // Prepare the SQL statement to delete the item
            $stmt = $this->pdo->prepare("DELETE FROM items WHERE id = :id");
            $stmt->bindParam(':id', $itemId, PDO::PARAM_INT);
            $stmt->execute();
            
            // Check if any rows were affected
            if ($stmt->rowCount() > 0) {
                return [
                    'success' => true,
                    'data' => $this->getItems($this->pdo->lastInsertId())
                ]; // Successful insertion // Item Deleted
            }

            return [];
        } catch (PDOException $e) {

            return ['success' => false, 'error' => $e->getMessage()];
        }
    }

    public function editItem($itemId, $newName): array
    {
        try {
            // Prepare the SQL statement to update the 'checked' status
            $stmt = $this->pdo->prepare("UPDATE items SET name = :name WHERE id = :id");
            $stmt->bindParam(':id', $itemId, PDO::PARAM_INT);
            $stmt->bindParam(':name', $newName);
            $stmt->execute();

            // Check if any rows were affected
            if ($stmt->rowCount() > 0) {
                return [
                    'success' => true,
                    'data' => $this->getItems($itemId)
                ]; // Item edited
            }

            return [];
        } catch (PDOException $e) {

            return ['success' => false, 'error' => $e->getMessage()];
        }
    }

    public function checkItem(int $itemId, bool $checked): array
    {
        try {
            // Prepare the SQL statement to update the 'checked' status
            $stmt = $this->pdo->prepare("UPDATE items SET checked = :checked WHERE id = :id");
            $stmt->bindParam(':id', $itemId, PDO::PARAM_INT);
            $stmt->bindParam(':checked', $checked, PDO::PARAM_INT);
            $stmt->execute();

            // Check if any rows were affected
            if ($stmt->rowCount() > 0) {
                return [
                    'success' => true,
                    'data' => $this->getItems($itemId)
                ]; // Item marked as checked
            }

            return [];
        } catch (PDOException $e) {

            return ['success' => false, 'error' => $e->getMessage()];
        }
    }

    public function getItems(int $itemId = null): array
    {
        try {
            if ($itemId === null) {
                // Fetch all items
                $stmt = $this->pdo->query("SELECT * FROM items");
            } else {
                // Fetch a single item by ID
                $stmt = $this->pdo->prepare("SELECT * FROM items WHERE id = :id");
                $stmt->bindParam(':id', $itemId, PDO::PARAM_INT);
                $stmt->execute();
            }

            // Fetch items into an array
            $items = [];
            while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                $item = new Item($row['name'], $row['id'], $row['checked']);
                $items[] = $item;
            }

            return [
                'success' => true,
                'data' => $items
            ];
        } catch (PDOException $e) {
            // Handle any database errors
            // You might want to log the error or throw an exception
            return ['success' => false, 'error' => $e->getMessage()];
        }
    }
}
