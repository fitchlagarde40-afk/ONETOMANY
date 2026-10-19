<?php
require_once __DIR__ . "/CORE/models.php";

$flowers = getFlowers($pdo);
$flash = $_SESSION["flash"] ?? null;
unset($_SESSION["flash"]);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Bloom & Blossom Flower Shop</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>

<header class="header">
    <h1>🌸 Bloom & Blossom</h1>
    <p>Flower Shop Management System</p>
</header>

<nav>
    <a href="/lagarde/index.php">Dashboard</a>
    <a href="/lagarde/viewprojects.php">View Orders</a>
</nav>


<main class="container">

    <?php if ($flash): ?>
        <div class="alert <?= e($flash["type"]) ?>">
            <?= e($flash["message"]) ?>
        </div>
    <?php endif; ?>

    <section class="card">
        <h2>🌷 Add New Flower</h2>

        <form action="CORE/handleForms.php" method="POST">
            <input type="hidden" name="action" value="add_flower">
            <input type="hidden" name="csrf_token"
                   value="<?= e(csrfToken()) ?>">

            <label>Flower Name</label>
            <input type="text" name="flower_name"
                   placeholder="Example: Pink Roses"
                   maxlength="100" required>

            <label>Price (PHP)</label>
            <input type="number" name="price"
                   min="0" step="0.01" required>

            <button type="submit">Add Flower</button>
        </form>
    </section>

    <section class="card">
        <h2>🌼 Available Flowers</h2>

        <div class="table-wrap">
            <table>
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Flower Name</th>
                        <th>Price</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                <?php foreach ($flowers as $flower): ?>
                    <tr>
                        <td><?= e($flower["flower_id"]) ?></td>
                        <td><?= e($flower["flower_name"]) ?></td>
                        <td>₱<?= number_format($flower["price"], 2) ?></td>
                        <td>
                            <a class="btn small"
                               href="editflowers.php?id=<?= e($flower["flower_id"]) ?>">
                                Edit
                            </a>
                            <form action="deleteflowers.php" method="POST">
                                <input type="hidden" name="csrf_token"
                                       value="<?= e(csrfToken()) ?>">
                                <input type="hidden" name="flower_id"
                                       value="<?= e($flower["flower_id"]) ?>">
                                <button class="danger small" type="submit">
                                    Delete
                                </button>
                            </form>
                        </td>
                    </tr>
                <?php endforeach; ?>

                <?php if (empty($flowers)): ?>
                    <tr>
                        <td colspan="4">No flowers added yet.</td>
                    </tr>
                <?php endif; ?>
                </tbody>
            </table>
        </div>
    </section>

    <section class="card">
        <h2>💐 Create Flower Order</h2>

        <form action="CORE/handleForms.php" method="POST">
            <input type="hidden" name="action" value="add_order">
            <input type="hidden" name="csrf_token"
                   value="<?= e(csrfToken()) ?>">

            <label>Customer Name</label>
            <input type="text" name="customer_name" required>

            <label>Select Flower</label>
            <select name="flower_id" required>
                <option value="">-- Select Flower --</option>
                <?php foreach ($flowers as $flower): ?>
                    <option value="<?= e($flower["flower_id"]) ?>">
                        <?= e($flower["flower_name"]) ?>
                    </option>
                <?php endforeach; ?>
            </select>

            <label>Quantity</label>
            <input type="number" name="quantity" min="1" value="1" required>

            <label>Order Status</label>
            <select name="status">
                <option value="Pending">Pending</option>
                <option value="Completed">Completed</option>
                <option value="Cancelled">Cancelled</option>
            </select>

            <button type="submit" <?= empty($flowers) ? "disabled" : "" ?>>
                Place Order
            </button>
        </form>
    </section>

</main>

<footer>
    <p>Bloom & Blossom Flower Shop | PHP PDO Project</p>
</footer>

</body>
</html>
