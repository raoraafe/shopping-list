<?php
namespace model;

class ShoppingListRepository
{
    private $itemRepository;

    public function __construct(ItemRepository $itemRepository)
    {
        $this->itemRepository = $itemRepository;
    }

    public function addItemToShoppingList($itemName): array
    {
        // Create a new Item instance and add it to the shopping list
        $item = new Item($itemName);
        return $this->itemRepository->addItem($item);
    }

    public function deleteItemFromShoppingList($itemId): array
    {
        // Delete an item from the shopping list
        return $this->itemRepository->deleteItem($itemId);
    }

    public function editItemInShoppingList($itemId, $newName): array
    {
        // Edit an item in the shopping list
        return $this->itemRepository->editItem($itemId, $newName);
    }

    public function markItemAsChecked($id, $checked): array
    {
        return $this->itemRepository->checkItem($id, $checked);
    }
    public function getShoppingList(): array
    {
        // Fetch the shopping list (items) from the database
        return $this->itemRepository->getItems();
    }
}