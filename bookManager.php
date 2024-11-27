<?php 

require_once 'Book.php';

class BookManager {
    private $books = [];
    private $filename = 'books.json';
    private $logfile = 'history.log';

    public function __construct() {
        $this->loadBooks();
    }

    private function loadBooks() {
        if (file_exists($this->filename)) {
            $data = json_decode(file_get_contents($this->filename), true);
            foreach ($data as $bookData) {
                $this->books[] = new Book($bookData['id'], $bookData['name'], $bookData['description'], $bookData['inStock']);
            }
        }
    }

    public function addBook($name, $description, $inStock) {
        $id = count($this->books) ? max(array_map(function ($book) { return $book->getId(); }, $this->books)) + 1 : 1;
        $book = new Book($id, $name, $description, $inStock === "yes" ? true : false);
        $this->books[] = $book;
        $this->saveBooks(); 
        $this->logAction("Added book: {$book->getName()}"); 
        return $book;
    }

   private function saveBooks() {
        $data = array_map(function ($book) {
            return [
                'id' => $book->getId(),
                'name' => $book->getName(),
                'description' => $book->getDescription(),
                'inStock' => $book->getInStock()
            ];
        }, $this->books);
        file_put_contents($this->filename, json_encode($data, JSON_PRETTY_PRINT));
    }
    
    private function logAction($action) {
        file_put_contents($this->logfile, $action . PHP_EOL, FILE_APPEND);
    }

    public function getBooks() {
        return $this->books;
    }
    
    public function findBookByName($name) {
        $matchingBooks = [];
        foreach ($this->books as $book) {
            if ($book->getName() === $name) {
                $matchingBooks[] = $book;
            }
        }
        return $matchingBooks;
    }

    public function findBookByDescription($description) {
        $matchingBooks = [];
        foreach ($this->books as $book) {
            if ($book->getDescription() === $description) {
                $matchingBooks[] = $book;
            }
        }
        return $matchingBooks;
    }

    public function findBookByStock($inStock) {
        $matchingBooks = [];
        foreach ($this->books as $book) {
            if ($book->getInStock() === $inStock) {
                $matchingBooks[] = $book;
            }
        }
        return $matchingBooks;
    }

    public function findBookById($id) {
        foreach ($this->books as $book) {
            if ($book->getId() == $id) {
                return $book;
            }
        }
        return null;
    }

    public function deleteBook($id){
        foreach ($this->books as $index => $book) {
            if ($book->getId() == $id) {
                array_splice($this->books, $index, 1);
                $this->saveBooks();
                $this->logAction("Deleted book: {$book->getName()}");
                return true;
            }
        }
        return false;
    }

    public function updateBook($id, $name, $description, $inStock) {
        foreach($this->books as $book) {
            if ($book->getId() == $id) {
                $book->setName($name);
                $book->setDescription($description);
                $book->setInStock($inStock);
                $this->saveBooks();
                $this->logAction("Updated book: {$book->getName()}");
                return $book;
            }
        }
        return null;
    }

    public function quickSort($column, $order = 'asc') {
        usort($this->books, function ($a, $b) use ($column, $order) {
            $methodA = 'get' . ucfirst($column);
            $methodB = 'get' . ucfirst($column);
    
            if ($a->$methodA() == $b->$methodA()) {
                return 0;
            }
            if ($order == 'asc') {
                return ($a->$methodA() < $b->$methodA()) ? -1 : 1;
            } else {
                return ($a->$methodA() > $b->$methodA()) ? -1 : 1;
            }
        });
        $this->logAction("Sorted books by $column in $order order");
        return $this->books;
    }

    public function binarySearch($column, $value) {
        $this->quickSort($column, 'asc');
        $low = 0;
        $high = count($this->books) - 1;
        $method = 'get' . ucfirst($column);
        $matchingBooks = [];

        while ($low <= $high) {
            $mid = floor(($low + $high) / 2);
            $midValue = $this->books[$mid]->$method();

            if (strcasecmp($midValue, $value) == 0) {
                if ($column == 'id') {
                    return $this->books[$mid];
                } else {
                    $matchingBooks[] = $this->books[$mid];
                    // Check for other matches on the left side
                    $left = $mid - 1;
                    while ($left >= $low && strcasecmp($this->books[$left]->$method(), $value) == 0) {
                        $matchingBooks[] = $this->books[$left];
                        $left--;
                    }
                    // Check for other matches on the right side
                    $right = $mid + 1;
                    while ($right <= $high && strcasecmp($this->books[$right]->$method(), $value) == 0) {
                        $matchingBooks[] = $this->books[$right];
                        $right++;
                    }
                    return $matchingBooks;
                }
            }elseif (strcasecmp($midValue, $value) < 0) {
                $low = $mid + 1;
            } else {
                $high = $mid - 1;
            }
        }
        return $column == 'id' ? null : $matchingBooks;
    }

    public function displayHistoryLog() {
        if (!file_exists($this->logfile)) {
            echo "Log file does not exist.\n";
            return;
        }

        $logContent = file_get_contents($this->logfile);
        if ($logContent === false) {
            echo "Failed to read log file.\n";
            return;
        }

       return $logContent;
    }

    public function mergeSort($column, $order = 'asc') {
        $this->books = $this->mergeSortRecursive($this->books, $column, $order);
        $this->logAction("Sorted books by $column in $order order using merge sort");
        return $this->books;
    }

    private function mergeSortRecursive($books, $column, $order) {
        if (count($books) <= 1) {
            return $books;
        }

        $middle = floor(count($books) / 2);
        $left = array_slice($books, 0, $middle);
        $right = array_slice($books, $middle);

        $left = $this->mergeSortRecursive($left, $column, $order);
        $right = $this->mergeSortRecursive($right, $column, $order);

        return $this->merge($left, $right, $column, $order);
    }

    private function merge($left, $right, $column, $order) {
        $result = [];
        $method = 'get' . ucfirst($column);

        while (count($left) > 0 && count($right) > 0) {
            if ($order == 'asc') {
                if ($left[0]->$method() <= $right[0]->$method()) {
                    $result[] = array_shift($left);
                } else {
                    $result[] = array_shift($right);
                }
            } else {
                if ($left[0]->$method() >= $right[0]->$method()) {
                    $result[] = array_shift($left);
                } else {
                    $result[] = array_shift($right);
                }
            }
        }
        return array_merge($result, $left, $right);
    }
}
?>