<?php

class Book {
    public $id;
    public $title;
    public $author;
    public $year;
    public $borrowed;
    public $borrowTime;
    public $returnTime;

    public function __construct($id, $title, $author, $year, $borrowed = false, $borrowTime = null, $returnTime = null) {
        $this->id = $id;
        $this->title = $title;
        $this->author = $author;
        $this->year = $year;
        $this->borrowed = $borrowed;
        $this->borrowTime = $borrowTime;
        $this->returnTime = $returnTime;
    }

    public function setBorrowed($borrowed) {
        $this->borrowed = $borrowed;
    }

    public function getId() {
        return $this->id;
    }

    public function getTitle() {
        return $this->title;
    }

    public function getAuthor() {
        return $this->author;
    }

    public function getYear() {
        return $this->year;
    }

    public function isBorrowed() {
        return $this->borrowed;
    }

    public function calculateFine() {
        if ($this->returnTime > 0) {
            $borrowTime = $this->borrowTime;
            $returnTime = $this->returnTime;
            $lateSeconds = $returnTime - $borrowTime - (5 * 60); // 5 menit tambahan batas waktu
            if ($lateSeconds > 0) {
                $fine = ceil($lateSeconds / 60) * 5000; // Denda per menit terlambat adalah 5000
                return $fine;
            }
        }
        return 0; // Tidak ada denda jika tidak terlambat
    }
}

class ReferenceBook extends Book {
    public $isbn;
    public $publisher;

    public function __construct($id, $title, $author, $year, $isbn, $publisher, $borrowed = false, $borrowTime = null, $returnTime = null) {
        parent::__construct($id, $title, $author, $year, $borrowed, $borrowTime, $returnTime);
        $this->isbn = $isbn;
        $this->publisher = $publisher;
    }
}

class Library {
    public $books;

    public function __construct() {
        $json = file_get_contents('books.json');
        $bookData = json_decode($json, true); // Mengonversi ke array asosiatif
        $this->books = [];

        // Membuat objek Book dari array asosiatif dan menambahkannya ke dalam $books
        foreach ($bookData as $book) {
            $newBook = new Book($book['id'], $book['title'], $book['author'], $book['year'], $book['borrowed'], $book['borrowTime'], $book['returnTime']);
            $this->books[] = $newBook;
        }
    }

    // Function to get the last book ID
    private function getLastBookId() {
        if (!empty($this->books)) {
            $lastBook = end($this->books);
            return $lastBook->id;
        } else {
            return 0; // Return 0 if there are no books
        }
    }

    // Function to add a book
    public function addBook($new_book) {
        $new_id = $this->getLastBookId() + 1; // Generate new ID
        $new_book->id = $new_id; // Assign the new ID to the new book
        $this->books[] = $new_book; // Add the new book to the array
        $this->saveBooks(); // Save the updated book list
    }

    public function borrowBook($id, $userId) {
        $borrowTime = time(); // Waktu peminjaman
        foreach ($this->books as &$book) {
            if ($book->id == $id) {
                if ($book->borrowed) {
                    return "Book already borrowed.";
                } else {
                    $book->setBorrowed(true);
                    $book->borrowTime = $borrowTime;
                    $this->saveBooks();
                    return "Book borrowed successfully.";
                }
            }
        }
        return "Book not found.";
    }

    public function returnBook($id, $userId) {
        $returnTime = time(); // Waktu pengembalian
        foreach ($this->books as &$book) {
            if ($book->id == $id) {
                if ($book->borrowed) {
                    $book->setBorrowed(false);
                    $book->returnTime = $returnTime;
                    $this->saveBooks();
                    return "Book returned successfully.";
                } else {
                    return "Book is not borrowed.";
                }
            }
        }
        return "Book not found.";
    }

    public function searchBooks($search_term) {
        $results = [];
        foreach ($this->books as $book) {
            if (stripos($book->title, $search_term) !== false || stripos($book->author, $search_term) !== false) {
                $results[] = $book;
            }
        }
        return $results;
    }

    public function deleteBook($id) {
        foreach ($this->books as $key => $book) {
            if ($book->id == $id) {
                unset($this->books[$key]);
                $this->saveBooks();
                return "Book deleted successfully.";
            }
        }
        return "Book not found.";
    }

    public function sortBooksByYear() {
        usort($this->books, function($a, $b) {
            return $a->year - $b->year;
        });
    }

    public function sortBooksByAuthor() {
        usort($this->books, function($a, $b) {
            return strcmp($a->author, $b->author);
        });
    }

    public function getBooks() {
        return $this->books;
    }

    private function saveBooks() {
        file_put_contents('books.json', json_encode($this->books));
    }
}

?>
