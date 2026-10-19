<?php
require_once __DIR__ . "/Core/models.php";

$id = filter_input(INPUT_GET, "id", FILTER_VALIDATE_INT);
$flower = $id ? getFlower($pdo, $id) : false;

if (!$flower) {
    http_response_code(404);
    exit("Flower not found.");
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Edit Flower</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>

<header class="header">
    <h1>🌸 Edit Flower</h1>
</header>

<main class="container">
    <section class="card">
        <h2>Update Flower Information</h2>

        <form action="Core/handleForms.php" method="POST">
            <input type="hidden" name="action"
                   value="update_flower">
            <input type="hidden" name="csrf_token"
                   value="<?= e(csrfToken()) ?>">
            <input type="hidden" name="flower_id"
                   value="<?= e($flower["flower_id"]) ?>">

            <label>Flower Name</label>
            <input type="text" name="flower_name"
                   value="<?= e($flower["flower_name"]) ?>"
                   maxlength="100" required>

            <label>Price</label>
            <input type="number" name="price"
                   min="0" max="99999999.99" step="0.01"
                   value="<?= e($flower["price"]) ?>" required>

            <button type="submit">Save Changes</button>
            <a href="index.php" class="btn secondary">Cancel</a>
        </form>
    </section>
</main>

</body>
</html>