
<?php
session_start();

// Database connection
$host = "localhost";
$dbname = "flower_shop";
$username = "root";
$password = "";

try {
    $pdo = new PDO(
        "mysql:host=$host;dbname=$dbname;charset=utf8mb4",
        $username,
        $password
    );

    $pdo->setAttribute(
        PDO::ATTR_ERRMODE,
        PDO::ERRMODE_EXCEPTION
    );

} catch (PDOException $e) {
    die("Database connection failed. Check your database settings.");
}

// Prevent unsafe HTML output
function e($value) {
    return htmlspecialchars(
        (string)$value,
        ENT_QUOTES,
        "UTF-8"
    );
}

// Get all flowers
function getFlowers($pdo) {
    $stmt = $pdo->query(
        "SELECT * FROM flowers ORDER BY flower_id DESC"
    );
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

// Get one flower
function getFlower($pdo, $id) {
    $stmt = $pdo->prepare(
        "SELECT * FROM flowers WHERE flower_id = ?"
    );
    $stmt->execute([$id]);
    return $stmt->fetch(PDO::FETCH_ASSOC);
}

// Get one order
function getOrder($pdo, $id) {
    $stmt = $pdo->prepare(
        "SELECT * FROM orders WHERE order_id = ?"
    );
    $stmt->execute([$id]);
    return $stmt->fetch(PDO::FETCH_ASSOC);
}

// Display notifications
function setFlash($message, $type = "success") {
    $_SESSION["flash"] = [
        "message" => $message,
        "type" => $type
    ];
}

// Create CSRF token
function csrfToken() {
    if (empty($_SESSION["csrf_token"])) {
        $_SESSION["csrf_token"] = bin2hex(random_bytes(32));
    }
    return $_SESSION["csrf_token"];
}

// Verify form submissions
function verifyCsrf() {
    if (
        !isset($_POST["csrf_token"]) ||
        !hash_equals(
            csrfToken(),
            (string)$_POST["csrf_token"]
        )
    ) {
        http_response_code(403);
        exit("Invalid security token.");
    }
}
?>
