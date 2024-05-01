<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Library System</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <div class="container">
    <div class="left-section">
            <h1>Library System</h1>
            <?php
            include 'Library.php';
            $library = new Library();

            if (isset($_POST['add'])) {
                $new_book = new Book(null, $_POST['new_title'], $_POST['new_author'], $_POST['new_year']);
                $library->addBook($new_book);
                echo '<p class="success">New book added successfully!</p>';
            }

            if (isset($_POST['borrow'])) {
                $userId = 1; // Contoh ID pengguna, bisa diganti sesuai kebutuhan
                $borrowTime = time(); // Waktu peminjaman
                $message = $library->borrowBook($_POST['id'], $userId, $borrowTime);

                // Set borrowTime pada objek buku yang sesuai
                foreach ($library->getBooks() as $book) {
                    if ($book->getId() == $_POST['id']) {
                        $book->borrowTime = $borrowTime;
                        break;
                    }
                }

                echo "<p>$message</p>";
            }

            if (isset($_POST['return'])) {
                $userId = 1; // Contoh ID pengguna, bisa diganti sesuai kebutuhan
                $returnTime = time(); // Waktu pengembalian
                $message = $library->returnBook($_POST['id'], $userId, $returnTime);

                // Set returnTime dan fine pada objek buku yang sesuai
                foreach ($library->getBooks() as $book) {
                    if ($book->getId() == $_POST['id']) {
                        $book->returnTime = $returnTime;
                        break;
                    }
                }

                echo "<p>$message</p>";
            }

            if (isset($_POST['search'])) {
                $results = $library->searchBooks($_POST['search_term']);
            }

            if (isset($_POST['delete'])) {
                $message = $library->deleteBook($_POST['id']);
                echo "<p>$message</p>";
            }

            if (isset($_POST['sort'])) {
                if ($_POST['sort'] == 'year') {
                    $library->sortBooksByYear();
                } elseif ($_POST['sort'] == 'author') {
                    $library->sortBooksByAuthor();
                }
            }
            ?>

            <div class="form-group">
                <form method="post">
                    <input type="text" name="new_title" placeholder="Enter new book title">
                    <input type="text" name="new_author" placeholder="Enter author">
                    <input type="text" name="new_year" placeholder="Enter year of publication">
                    <button type="submit" name="add">Add New Book</button>
                </form>
            </div>

            <div class="form-group">
                <form method="post">
                    <input type="text" name="id" placeholder="Enter book ID to borrow">
                    <button type="submit" name="borrow">Borrow Book</button>
                </form>
            </div>

            <div class="form-group">
                <form method="post">
                    <input type="text" name="id" placeholder="Enter book ID to return">
                    <button type="submit" name="return">Return Book</button>
                </form>
            </div>

            <div class="form-group">
                <form method="post">
                    <input type="text" name="search_term" placeholder="Enter title or author to search">
                    <button type="submit" name="search">Search Books</button>
                </form>
            </div>

            <div class="form-group">
                <form method="post">
                    <input type="text" name="id" placeholder="Enter book ID to delete">
                    <button type="submit" name="delete">Delete Book</button>
                </form>
            </div>
        </div>
        <div class="right-section">
            <form method="post">
                <select name="sort">
                    <option value="year">Sort by Year</option>
                    <option value="author">Sort by Author</option>
                </select>
                <button type="submit">Sort</button>
            </form>

            <h2>Book List:</h2>
            <table>
                <tr>
                    <th>ID</th>
                    <th>Title</th>
                    <th>Author</th>
                    <th>Year</th>
                    <th>Status</th>
                    <th>Borrow Time</th>
                    <th>Return Time</th>
                    <th>Fine</th>
                </tr>
                <?php if (isset($results)): ?>
                    <?php foreach ($results as $book): ?>
                        <tr>
                            <td><?php echo $book->getId(); ?></td>
                            <td><?php echo $book->getTitle(); ?></td>
                            <td><?php echo $book->getAuthor(); ?></td>
                            <td><?php echo $book->getYear(); ?></td>
                            <td><?php echo $book->isBorrowed() ? 'Borrowed' : 'Available'; ?></td>
                            <td><?php echo $book->borrowTime ? date('Y-m-d H:i:s', $book->borrowTime) : ''; ?></td>
                            <td><?php echo $book->returnTime ? date('Y-m-d H:i:s', $book->returnTime) : ''; ?></td>
                            <td><?php echo $book->calculateFine(); ?></td>
                        </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <?php foreach ($library->getBooks() as $book): ?>
                        <tr>
                            <td><?php echo $book->getId(); ?></td>
                            <td><?php echo $book->getTitle(); ?></td>
                            <td><?php echo $book->getAuthor(); ?></td>
                            <td><?php echo $book->getYear(); ?></td>
                            <td><?php echo $book->isBorrowed() ? 'Borrowed' : 'Available'; ?></td>
                            <td><?php echo $book->borrowTime ? date('Y-m-d H:i:s', $book->borrowTime) : ''; ?></td>
                            <td><?php echo $book->returnTime ? date('Y-m-d H:i:s', $book->returnTime) : ''; ?></td>
                            <td><?php echo $book->calculateFine(); ?></td>
                        </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </table>
        </div>
    </div>
</body>
</html>
