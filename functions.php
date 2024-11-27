<?php

require_once 'bookManager.php';
$manager = new BookManager();

function readInput($message): string
{
    echo $message;
    return trim(fgets(STDIN));
}

function validateBookInput($name, $description, $inStock): void
{
    if (empty($name)) {
        throw new Exception("Book name cannot be empty.");
    }
    if (empty($description)) {
        throw new Exception("Book description cannot be empty.");
    }
    if (empty($inStock) || !in_array(strtolower($inStock), ['yes', 'no'])) {
        throw new Exception("In stock must be 'yes' or 'no'.");
    }
}

function addBook(): void
{
    global $manager;
    try {
        $name = readInput("Enter book name: ");
        $description = readInput("Enter book description: ");
        $inStock = readInput("Is the book in stock (yes/no): ");
        validateBookInput($name, $description, $inStock);
        $manager->addBook($name, $description, $inStock);
        echo "Book added successfully.\n";
    } catch (Exception $e) {
        echo "Error: " . $e->getMessage() . "\n";
        return;
    }
}

function displayBooks(): void
{
    global $manager;
    $books = $manager->getBooks();
    if(count($books) === 0){
        echo "No books found.\n";
    } else {
        foreach($books as $book){
            echo "----------------------------------\n";
            echo "ID: " . $book->getId() . "\n";
            echo "Name: " . $book->getName() . "\n";
            echo "Description: " . $book->getDescription() . "\n";
            echo "In stock: " . ($book->getInStock() ? 'yes' : 'no') . "\n";
            echo "----------------------------------\n";
        }
    }
}

function displayBook(): void
{
    global $manager;
    $id = readInput("Enter the ID of the book to display: ");
    $book = $manager->findBookById($id);
    if($book === null){
        echo "Book not found.\n";
    } else {
        echo "ID: " . $book->getId() . "\n";
        echo "Name: " . $book->getName() . "\n";
        echo "Description: " . $book->getDescription() . "\n";
        echo "In stock: " . ($book->getInStock() ? 'yes' : 'no') . "\n";
    }
}

function deleteBook(): void
{
    global $manager;
    echo "Select search criteria:\n";
    echo "1. Name\n";
    echo "2. Description\n";
    echo "3. Stock status\n";
    echo "4. ID\n";

    $choice = readInput("Enter your choice (1-4): ");
    $value = "";

    switch($choice) {
        case '1':
            $value = readInput("Enter the book name: ");
            $books = $manager->findBookByName($value);
            break;
        case '2':
            $value = readInput("Enter the description: ");
            $books = $manager->findBookByDescription($value);
            break;
        case '3':
            $value = readInput("Is the book in stock? (yes/no): ");
            $inStock = ($value === 'yes');
            $books = $manager->findBookByStock($inStock);
            break;
        case '4':
            $value = readInput("Enter the book ID: ");
            $book = $manager->findBookById($value);
            if ($book !== null) {
                $books = [$book];
            }
            break;
        default:
            echo "Invalid choice.\n";
            return;
    }

    if(empty($books)){
        echo "Book not found.\n";
    } else {
        foreach ($books as $book) {
            $manager->deleteBook($book->getId());
        }
        echo "Book(s) deleted successfully.\n";
    }
}

function updateBook(): void
{
    global $manager;
    try {
        $id = readInput("Enter the ID of the book to update: ");
        if($manager->findBookById($id) === null){
            echo "Book not found.\n";
            return;
        }
        $name = readInput("Enter the new name of the book: ");
        $description = readInput("Enter the new description of the book: ");
        $inStock = readInput("Is the book in stock (yes/no): ");
        validateBookInput($name, $description, $inStock);
        $manager->updateBook($id, $name, $description, $inStock);
        echo "----------------------------------\n";
        echo "Book updated successfully.\n";
        echo "----------------------------------\n";
    } catch (Exception $e) {
        echo "Error: " . $e->getMessage() . "\n";
        return;
    }
}

function sortBooks(): void
{
    global $manager;
    echo "Select column to sort by:\n";
    echo "1. Name\n";
    echo "2. Description\n";
    echo "3. Stock status\n";

    $choice = readInput("Enter your choice (1-3): ");

    $column = '';
    switch($choice) {
        case '1':
            $column = 'name';
            break;
        case '2':
            $column = 'description';
            break;
        case '3':
            $column = 'inStock';
            break;
        default:
            echo "Invalid choice.\n";
            return;
    }

    echo "Select sort order:\n";
    echo "1. Ascending\n";
    echo "2. Descending\n";

    $orderChoice = readInput("Enter your choice (1-2): ");
    $order = $orderChoice === '1' ? 'asc' : 'desc';

    $sortedBooks = $manager->mergeSort($column, $order);

    echo "Books sorted by $column in $order order:\n";
    echo "----------------------------------\n";
    foreach ($sortedBooks as $book) {
        echo "ID: " . $book->getId() . "\n";
        echo "Name: " . $book->getName() . "\n";
        echo "Description: " . $book->getDescription() . "\n";
        echo "In stock: " . ($book->getInStock() ? 'yes' : 'no') . "\n";
        echo "----------------------------------\n";
    }
}

function searchBook(): void
{
    global $manager;
    echo "Select search criteria:\n";
    echo "1. ID\n";
    echo "2. Name\n";
    echo "3. Description\n";
    echo "4. Stock status\n";

    $choice = readInput("Enter your choice (1-4): ");

    $column = '';
    switch($choice) {
        case '1':
            $column = 'id';
            break;
        case '2':
            $column = 'name';
            break;
        case '3':
            $column = 'description';
            break;
        case '4':
            $column = 'inStock';
            break;
        default:
            echo "Invalid choice.\n";
            return;
    }

    $value = $column === 'inStock'
        ? readInput("Enter value (yes/no): ")
        : readInput("Enter search value: ");

    $books = $manager->binarySearch($column, $value);

    if(empty($books)){
        echo "No books found.\n";
    } else {
        if ($column == 'id') {
            $books = [$books]; // Wrap single book in an array for uniform handling
        }
        echo "----------------------------------\n";
        foreach($books as $book){
            echo "ID: " . $book->getId() . "\n";
            echo "Name: " . $book->getName() . "\n";
            echo "Description: " . $book->getDescription() . "\n";
            echo "In stock: " . ($book->getInStock() ? 'yes' : 'no') . "\n";
            echo "----------------------------------\n";
        }
    }
}

function viewHistory(): void
{
    global $manager;
    $logContent = $manager->displayHistoryLog();

    echo "History Log:\n";
    echo "-------------------------\n";
    echo $logContent;
    echo "----------------------------------\n";
}