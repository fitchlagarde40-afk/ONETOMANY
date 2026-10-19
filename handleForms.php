<?php
require_once __DIR__ . "/models.php";

if ($_SERVER["REQUEST_METHOD"] !== "POST") {
    header("Location: ../index.php");
    exit;
}

verifyCsrf();

$action = $_POST["action"] ?? "";
$redirect = "../index.php";

try {

    if ($action === "add_flower" ||
        $action === "update_flower") {

        $name = trim($_POST["flower_name"] ?? "");
        $price = filter_input(
            INPUT_POST, "price", FILTER_VALIDATE_FLOAT
        );

        if ($name === "" || strlen($name) > 100 ||
            $price === false || $price === null ||
            !is_finite($price) || $price < 0) {
            throw new Exception("Enter a valid flower name and price.");
        }

        if ($action === "add_flower") {
            $stmt = $pdo->prepare(
                "INSERT INTO flowers (flower_name, price)
                 VALUES (?, ?)"
            );

            $stmt->execute([$name, $price]);

            setFlash("Flower added successfully!");
        } else {
            $id = filter_input(
                INPUT_POST, "flower_id", FILTER_VALIDATE_INT
            );

            if (!$id || $id < 1) {
                throw new Exception("Invalid flower ID.");
            }

            $stmt = $pdo->prepare(
                "UPDATE flowers
                 SET flower_name = ?, price = ?
                 WHERE flower_id = ?"
            );

            $stmt->execute([$name, $price, $id]);

            setFlash("Flower updated successfully!");
        }

    } elseif ($action === "add_order" ||
              $action === "update_order") {

        $flowerId = filter_input(
            INPUT_POST, "flower_id", FILTER_VALIDATE_INT
        );
        $quantity = filter_input(
            INPUT_POST, "quantity", FILTER_VALIDATE_INT
        );
        $customer = trim($_POST["customer_name"] ?? "");
        $status = $_POST["status"] ?? "Pending";

        if (!$flowerId || $flowerId < 1 ||
            !$quantity || $quantity < 1 ||
            $customer === "" || strlen($customer) > 100) {
            throw new Exception("Complete the order form correctly.");
        }

        $allowedStatuses = [
            "Pending", "Completed", "Cancelled"
        ];

        if (!in_array($status, $allowedStatuses, true)) {
            throw new Exception("Invalid order status.");
        }

        $flower = getFlower($pdo, $flowerId);

        if (!$flower) {
            throw new Exception("Selected flower does not exist.");
        }

        $total = round(
            (float) $flower["price"] * $quantity,
            2
        );

        if ($total > 99999999.99) {
            throw new Exception("Order total is too large.");
        }

        if ($action === "add_order") {
            $stmt = $pdo->prepare(
                "INSERT INTO orders
                (flower_id, customer_name, quantity,
                 total_price, status)
                VALUES (?, ?, ?, ?, ?)"
            );

            $stmt->execute([
                $flowerId, $customer, $quantity,
                $total, $status
            ]);

            setFlash("Order added successfully!");
        } else {
            $orderId = filter_input(
                INPUT_POST, "order_id", FILTER_VALIDATE_INT
            );

            if (!$orderId || $orderId < 1 ||
                !getOrder($pdo, $orderId)) {
                throw new Exception("Invalid order ID.");
            }

            $stmt = $pdo->prepare(
                "UPDATE orders
                 SET flower_id = ?, customer_name = ?,
                     quantity = ?, total_price = ?,
                     status = ?
                 WHERE order_id = ?"
            );

            $stmt->execute([
                $flowerId, $customer, $quantity,
                $total, $status, $orderId
            ]);

            setFlash("Order updated successfully!");
        }

        $redirect = "../viewprojects.php";

    } else {
        throw new Exception("Unknown form action.");
    }

} catch (Exception $e) {
    setFlash($e->getMessage(), "error");
}

header("Location: " . $redirect);
exit;
?>