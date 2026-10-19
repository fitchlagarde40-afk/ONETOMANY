<?php
require_once __DIR__ . "/CORE/models.php";

if ($_SERVER["REQUEST_METHOD"] !== "POST") {
    header("Location: index.php");
    exit;
}

verifyCsrf();

$id = filter_input(
    INPUT_POST, "flower_id", FILTER_VALIDATE_INT
);

if ($id && $id > 0) {
    $stmt = $pdo->prepare(
        "DELETE FROM flowers WHERE flower_id = ?"
    );

    $stmt->execute([$id]);

    setFlash("Flower deleted successfully!");
} else {
    setFlash("Invalid flower ID.", "error");
}

header("Location: index.php");
exit;
?>