<?php

require_once 'bookManager.php';
$manager = new BookManager();

function readInput($message): string
{
    echo $message;
    return trim(fgets(STDIN));
}

function validateBookInput($name, $description, $inStock) {
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

function addBook(){
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

function displayBooks(){
    global $manager;
    $books = $manager->getBooks();
    if(count($books) === 0){
        echo "No books found.\n";
    } else {
        foreach($books as $book){
            echo "ID: " . $book->getId() . "\n";
            echo "Name: " . $book->getName() . "\n";
            echo "Description: " . $book->getDescription() . "\n";
            echo "In stock: " . ($book->getInStock() ? 'yes' : 'no') . "\n";
            echo "----------------------------------\n";
        }
    }
}

function displayBook(){
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

function deleteBook(){
    global $manager;
    $criteria = readInput("Enter the criteria to delete the book (name/description/stock/id): ");
    $value = readInput("Enter the value for the selected criteria: ");

    switch($criteria) {
        case 'name':
            $books = $manager->findBookByName($value);
            break;
        case 'description':
            $books = $manager->findBookByDescription($value);
            break;
        case 'stock':
            $inStock = ($value === 'yes');
            $books = $manager->findBookByStock($inStock);
            break;
        case 'id': 
            $book = $manager->findBookById($value);
            if ($book !== null) {
                $books[] = $book;
            }
            break;
        default:
            echo "Invalid criteria.\n";
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

function updateBook(){
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
        echo "Book updated successfully.\n";
    } catch (Exception $e) {
        echo "Error: " . $e->getMessage() . "\n";
        return;
    }
}

function sortBooks() {
    global $manager;
    $column = readInput("Enter the column to sort by (name, description, inStock): ");
    if (!in_array($column, ['name', 'description', 'inStock'])) {
        echo "Invalid column name. Please enter a valid column (name, description, inStock).\n";
        return;
    }
    $order = readInput("Enter the order to sort by (asc/desc): ");
    if (!in_array($order, ['asc', 'desc'])) {
        echo "Invalid order. Please enter 'asc' or 'desc'.\n";
        return;
    }

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

function searchBook(){
    global $manager;
    $column = readInput("Enter the column to search by (id, name, description, inStock): ");
    if (!in_array($column, ['id', 'name', 'description', 'inStock'])) {
        echo "Invalid column name. Please enter a valid column (name, description, inStock, id).\n";
        return;
    }
    $value = readInput("Enter the value to search for: ");
    $books = $manager->binarySearch($column, $value);
    if(empty($books)){
        echo "No books found.\n";
    } else {
        if ($column == 'id') {
            $books = [$books]; // Wrap single book in an array for uniform handling
        }
        foreach($books as $book){
            echo "ID: " . $book->getId() . "\n";
            echo "Name: " . $book->getName() . "\n";
            echo "Description: " . $book->getDescription() . "\n";
            echo "In stock: " . ($book->getInStock() ? 'yes' : 'no') . "\n";
            echo "----------------------------------\n";
        }
    }
}

function viewHistory() {
    global $manager;
    $logContent = $manager->displayHistoryLog();

    echo "History Log:\n";
    echo "-------------------------\n";
    echo $logContent;
}
