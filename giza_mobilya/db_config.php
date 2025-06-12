    <?php
    /**
    * Database connection configuration for the Giza Furniture Store Management System.
    * This file establishes a connection to the MySQL database using PDO.
    */

    $host = 'localhost'; // Your MySQL host (usually 'localhost' for XAMPP)
    $db   = 'giza_mobilya'; // The database name we created
    $user = 'root'; // Your MySQL username (default for XAMPP is 'root')
    $pass = ''; // Your MySQL password (default for XAMPP is empty)
    $charset = 'utf8mb4';

    $dsn = "mysql:host=$host;dbname=$db;charset=$charset";
    $options = [
        PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION, // Throw exceptions on errors
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,       // Fetch results as associative arrays
        PDO::ATTR_EMULATE_PREPARES   => false,                  // Disable emulation for better security and performance
    ];

    try {
        $pdo = new PDO($dsn, $user, $pass, $options);
    } catch (\PDOException $e) {
        // If connection fails, output an error message and terminate the script
        die("Database connection failed: " . $e->getMessage());
    }
    ?>
