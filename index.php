<?php
//autoload files
spl_autoload_register(function ($class) {
	$prefix = 'model\\';
	$baseDir = __DIR__ . '/model/';
	$len = strlen($prefix);
	if (strncmp($prefix, $class, $len) !== 0) {
		return; // The class does not belong to the 'model' namespace, so return
	}
	$relativeClass = substr($class, $len);
	$file = $baseDir . str_replace('\\', '/', $relativeClass) . '.php';
	//echo $file;exit;
	if (file_exists($file)) {
		require $file;
	}
});


// Instantiate the shopping list with database credentials
use model\ShoppingList;

$shoppingList = new ShoppingList('localhost', 'root', '', 'shopping_list');

// Add items
$shoppingList->addItem('Milk');
$shoppingList->addItem('Eggs');
$shoppingList->addItem('Bread');

// Mark an item as checked
$shoppingList->markItemAsChecked(2);

// Edit an item
$shoppingList->editItem(2, 'Butter');

// Delete an item
$shoppingList->deleteItem(1);

// Get the updated shopping list
$items = $shoppingList->getItems();

// Display the shopping list
foreach ($items as $index => $item) {
	echo ($item->isChecked() ? '[X] ' : '[ ] ') . $item->getName() . PHP_EOL;
}