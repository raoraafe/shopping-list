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

function redirect() {
    header("Location: index.php"); // Redirect to refresh the page
    exit();
}

// Instantiate the shopping list with database credentials
use model\ShoppingList;

$shoppingList = new ShoppingList('localhost', 'root', '', 'shopping_list');

// Handle adding a new item
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['newItem'])) {
    $newItem = $_POST['newItem'];
    if (!empty($newItem)) {
        $shoppingList->addItem($newItem);
    }
    redirect();
}

// Handle removing an item
if ($_SERVER['REQUEST_METHOD'] === 'GET' && isset($_GET['removeItem'])) {
    $removeItemId = intval($_GET['removeItem']);
    $shoppingList->deleteItem($removeItemId);
    redirect();
}

// Handle update checked item(s)
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['checkedItems'])) {
    $checkedItems = $_POST['checkedItems'];
    foreach ($checkedItems as $checkedItem) {

        $shoppingList->markItemAsChecked($checkedItem);
    }
    redirect();
}

// Get the updated shopping list
$items = $shoppingList->getItems();

// Handle editing an item param
if ($_SERVER['REQUEST_METHOD'] === 'GET' && isset($_GET['editItem'])) {
    $editItemId = intval($_GET['editItem']);
    foreach ($items as $item) {
        if ($item->getId() === $editItemId) {

            $item->setEditing(true);
        } else {

            $item->setEditing(false);
        }
    }
}
// Handle saving edited items
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['editedItems'])) {
    $editedItems = $_POST['editedItems'];
    foreach ($editedItems as $itemId => $newName) {
        if (!empty($newName)) {
            $shoppingList->editItem(intval($itemId), $newName);
        }
    }
    redirect();
}

$buttonText = 'Update Checked Items';
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
                        <?php if ($item->isEditing()) : ?>
                            <input type="text" name="editedItems[<?php echo $item->getId(); ?>]" value="<?php echo $item->getName(); ?>"/>
                        <?php else: ?>
                            <?php echo $item->getName(); ?>
                        <?php endif; ?>
                    </label>
                    <a href="?removeItem=<?php echo $item->getId(); ?>">Remove</a>
                    <?php if (!$item->isEditing()): ?> | <a
                            href="?editItem=<?php echo $item->getId(); ?>">Edit</a>
                    <?php endif; ?>
                </li>
            <?php endforeach; ?>
        </ul>

            <button type="submit"><?=$buttonText?></button>
    </form>
<?php endif; ?>
</body>
</html>