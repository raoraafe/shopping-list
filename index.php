<?php
//autoload files
spl_autoload_register(function ($class) {
    $prefix = 'model\\';
    $baseDir = __DIR__.'/model/';
    $len = strlen($prefix);
    if (strncmp($prefix, $class, $len) !== 0) {
        return; // The class does not belong to the 'model' namespace, so return
    }
    $relativeClass = substr($class, $len);
    $file = $baseDir.str_replace('\\', '/', $relativeClass).'.php';
    //echo $file;exit;
    if (file_exists($file)) {
        require $file;
    }
});


// Instantiate the shopping list with database credentials
use model\ShoppingList;

$shoppingList = new ShoppingList('localhost', 'root', '', 'shopping_list');

// Handle adding a new item
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['newItem'])) {
    $newItem = $_POST['newItem'];
    if (!empty($newItem)) {
        $shoppingList->addItem($newItem);
    }
}

// Handle removing an item
if ($_SERVER['REQUEST_METHOD'] === 'GET' && isset($_GET['removeItem'])) {
    $removeItemId = intval($_GET['removeItem']);
    $shoppingList->deleteItem($removeItemId);
    header("Location: index.php"); // Redirect to refresh the page
    exit();
}

// Handle update checked item(s)
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['checkedItems'])) {
    $checkedItems = $_POST['checkedItems'];
    foreach ($checkedItems as $checkedItem) {

        $shoppingList->markItemAsChecked($checkedItem);
    }

    header("Location: index.php"); // Redirect to refresh the page
    exit();
}

// Get the updated shopping list
$items = $shoppingList->getItems();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <title>Shopping List</title>
</head>
<body>
<h1>Shopping List</h1>

<!-- Add new item form -->
<form method="POST">
    <label>
        <input type="text" name="newItem" placeholder="Enter new item">
    </label>
    <button type="submit">Add Item</button>
</form>

<?php if (!empty($items)): ?>

    <!-- update for checked item form -->
    <form method="POST">
        <!-- Display the shopping list -->
        <ul>
            <?php foreach ($items as $item): ?>
                <li>
                    <label>
                        <input type="checkbox" name="checkedItems[]" value="<?php echo $item->getId(); ?>"
                            <?php echo $item->isChecked() ? 'checked' : ''; ?>>
                        <?php echo $item->getName(); ?>
                    </label>
                    <a href="?removeItem=<?php echo $item->getId(); ?>">Remove</a> | <a
                            href="?editItem=<?php echo $item->getId(); ?>">Edit</a>
                </li>
            <?php endforeach; ?>
        </ul>
        <button type="submit">Update Checked Items</button>
    </form>
<?php endif; ?>
</body>
</html>