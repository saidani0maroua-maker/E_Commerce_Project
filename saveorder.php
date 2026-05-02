<?php
// Connect to database
require_once __DIR__ . "/php/db.php";

// Start session to identify logged-in user
session_start();

// Only allow POST requests (coming from fetch/AJAX)
if ($_SERVER["REQUEST_METHOD"] !== "POST") {
    http_response_code(405);
    echo json_encode(["error" => "Invalid request"]);
    exit();
}

// Read JSON input from JavaScript
$data = json_decode(file_get_contents("php://input"), true);

// Validate incoming data
if (!isset($data["cart"]) || !is_array($data["cart"]) || count($data["cart"]) === 0) {
    echo json_encode(["error" => "Cart is empty"]);
    exit();
}

// Get logged-in user email from session
$userEmail = $_SESSION["user"] ?? null;

if (!$userEmail) {
    echo json_encode(["error" => "User not logged in"]);
    exit();
}

// ==============================
// 1. GET CUSTOMER ID
// ==============================
$stmt = $conn->prepare("SELECT customer_id FROM customer WHERE email = ?");
$stmt->bind_param("s", $userEmail);
$stmt->execute();
$result = $stmt->get_result();
$customer = $result->fetch_assoc();

if (!$customer) {
    echo json_encode(["error" => "Customer not found"]);
    exit();
}

$customer_id = $customer["customer_id"];

// ==============================
// 2. INSERT ORDER
// ==============================
$total_price = 0;

// Calculate total first
foreach ($data["cart"] as $item) {
    $price = floatval(preg_replace('/[^\d.]/', '', $item["price"]));
    $qty = intval($item["quantity"]);
    $total_price += $price * $qty;
}

// Insert into orders table
$stmt = $conn->prepare("INSERT INTO orders (customer_id, product_id, quantity, order_date, total_price) VALUES (?, ?, ?, NOW(), ?)");
    
// ==============================
// 3. INSERT EACH CART ITEM
// ==============================
foreach ($data["cart"] as $item) {

    $product_id = $item["id"]; // must match DB ids like electronics-001
    $quantity = intval($item["quantity"]);
    $price = floatval(preg_replace('/[^\d.]/', '', $item["price"]));
    $row_total = $price * $quantity;

    $stmt->bind_param("isid", $customer_id, $product_id, $quantity, $row_total);
    $stmt->execute();
}

// ==============================
// SUCCESS RESPONSE
// ==============================
echo json_encode([
    "success" => true,
    "message" => "Order saved successfully"
]);

?>