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

    if (file_exists($file)) {
        require $file;
    }
});

require 'helper/helper.php';

// Instantiate the shopping list with database credentials
use model\ItemRepository;
use model\ShoppingListRepository;

// Create a PDO instance and configure it as needed
$dsn = 'mysql:host=localhost;dbname=shopping_list';
$username = 'root';
$password = '';

try {
    $pdo = new PDO($dsn, $username, $password);
    // Set PDO to throw exceptions on errors
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die('Database connection failed: ' . $e->getMessage());
}

$buttonText = 'Update Checked Items';
// Create instances of repositories and pass the PDO instance
$itemRepository = new ItemRepository($pdo);
$shoppingListRepository = new ShoppingListRepository($itemRepository);

// Handle adding a new item
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['newItem'])) {
    $newItem = $_POST['newItem'];
    if (!empty($newItem)) {
        $shoppingListRepository->addItemToShoppingList($newItem);
    }
    redirect();
}

// Handle removing an item
if ($_SERVER['REQUEST_METHOD'] === 'GET' && isset($_GET['removeItem'])) {
    $removeItemId = intval($_GET['removeItem']);
    $shoppingListRepository->deleteItemFromShoppingList($removeItemId);
    redirect();
}

// Handle update checked item(s)

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['checkedItems'])) {

    $checkedItems = $_POST['checkedItems']; //array of items checked

    foreach ($checkedItems as $itemId) {

        $checked = 1;

        $shoppingListRepository->markItemAsChecked($itemId, $checked);
    }
    redirect();
}

// Get the updated shopping list
$items = $shoppingListRepository->getShoppingList();
$editingItems = [];
// Handle editing an item param
if ($_SERVER['REQUEST_METHOD'] === 'GET' && isset($_GET['editItem'])) {

    $editItemId = intval($_GET['editItem']);

    foreach ($items['data'] as $item) {

        if ($item->getId() === $editItemId) {
            //edit mode enabled for the item
            $editingItems[$item->getId()] = true;
            $buttonText = 'Update';
        } else {
            //remove item from array for edit
            unset($editingItems[$item->getId()]);
        }
    }
}
// Handle saving edited items
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['editedItems'])) {

    $editedItems = $_POST['editItem'];
    foreach ($editedItems as $itemId => $newName) {
        if (!empty($newName)) {
            $shoppingListRepository->editItemInShoppingList(intval($itemId), $newName);
        }
    }
    redirect();
}


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

<?php if (!empty($items['data'])): ?>

    <!-- update for checked item form -->
    <form method="POST">
        <!-- Display the shopping list -->
        <ul>
            <?php foreach ($items['data'] as $item): ?>
                <li>

                    <label>

                        <input type="checkbox" name="checkedItems[]" value="<?php echo $item->getId(); ?>"
                            <?php echo $item->isChecked() ? 'checked' : ''; ?>>
                        <?php if (isset($editingItems[$item->getId()])) : ?>
                            <input type="text" name="editedItems[<?php echo $item->getId(); ?>]" value="<?php echo $item->getName(); ?>"/>
                        <?php else: ?>
                            <?php echo $item->getName(); ?>
                        <?php endif; ?>

                    </label>
                    <a href="?removeItem=<?php echo $item->getId(); ?>">Remove</a>
                    <?php if (!isset($editingItems[$item->getId()])): ?> | <a
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