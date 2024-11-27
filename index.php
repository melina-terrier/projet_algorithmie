<?php

require_once 'functions.php';

function displayMenu() {

    while (true) {

        echo "----------------------------------\n";
        echo "1. Add book\n";
        echo "2. Modify book\n";
        echo "3. Delete book\n";
        echo "4. Show all books\n";
        echo "5. Show book\n";
        echo "6. Sort books\n";
        echo "7. Search for a book\n";
        echo "8. View History\n";
        echo "9. Exit\n";
        echo "----------------------------------\n";
        echo "Choose an option: ";

        $choice = readInput("");
        switch($choice){
            case 1:
                addBook();
                break;
            case 2:
                updateBook();
                break;
            case 3:
                deleteBook();
                break;
            case 4:
                displayBooks();
                break;
            case 5:
                displayBook();
                break;
            case 6:
                sortBooks();
                break;
            case 7:
                searchBook();
                break;
            case 8:
                viewHistory();
                break;
            case 9:
                exit;
                break;
            default:
                echo "Invalid option";
        }
    }
}

displayMenu();
?>
