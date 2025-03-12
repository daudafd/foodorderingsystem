<?php
include 'admin/db_connect.php';

if (!isset($_GET['id']) || empty($_GET['id'])) {
    die("Error: Order ID not provided.");
}

$order_id = intval($_GET['id']);

try {
    // Fetch delivery charge from the orders table
    $stmt_delivery = $conn->prepare("SELECT delivery_charge FROM orders WHERE id = :order_id");
    $stmt_delivery->execute([':order_id' => $order_id]);
    $delivery_charge_row = $stmt_delivery->fetch(PDO::FETCH_ASSOC);
    $delivery_charge = $delivery_charge_row ? floatval($delivery_charge_row['delivery_charge']) : 0;

    // Fetch order list and calculate the total using o.price
    $stmt_order = $conn->prepare("SELECT o.*, p.name, p.category_id FROM order_list o INNER JOIN product_list p ON o.product_id = p.id WHERE o.order_id = :order_id");
    $stmt_order->execute([':order_id' => $order_id]);
    $order_items = $stmt_order->fetchAll(PDO::FETCH_ASSOC);

    $total = 0;
    ?>

    <div class="container-fluid">
        <table class="table table-bordered">
            <thead>
                <tr>
                    <th>Qty</th>
                    <th>Order</th>
                    <th>Amount</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($order_items as $row): ?>
                    <?php $total += $row['qty'] * $row['price']; ?>
                    <tr>
                        <td><?php echo htmlspecialchars($row['qty']); ?></td>
                        <td>
                            <?php
                            echo htmlspecialchars($row['name']);

                            // Conditional "with" for swallow foods
                            $swallow_categories = [10]; // Replace with your swallow category IDs
                            if (isset($row['category_id']) && in_array($row['category_id'], $swallow_categories)) {
                                echo " with " . htmlspecialchars($row['soup']);
                            } elseif (strpos(strtolower($row['name']), 'semo') !== false || strpos(strtolower($row['name']), 'fufu') !== false || strpos(strtolower($row['name']), 'eba') !== false) {
                                echo " with " . htmlspecialchars($row['soup']);
                            }

                            // Conditional "Size:" for protein items
                            $protein_categories = [1]; // Replace with your protein category IDs
                            if (isset($row['category_id']) && in_array($row['category_id'], $protein_categories)) {
                                echo "(" . ucfirst(htmlspecialchars($row['size'])) . ")";
                            } elseif (strpos(strtolower($row['name']), 'chicken') !== false || strpos(strtolower($row['name']), 'beef') !== false || strpos(strtolower($row['name']), 'goat meat') !== false || strpos(strtolower($row['name']), 'turkey') !== false) {
                                echo "(" . ucfirst(htmlspecialchars($row['size'])) . ")";
                            }
                            ?>
                        </td>
                        <td><?php echo number_format($row['qty'] * $row['price'], 2); ?></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
            <tfoot>
                <tr>
                    <th colspan="2" class="text-right">Subtotal</th>
                    <th><?php echo number_format($total, 2); ?></th>
                </tr>
                <tr>
                    <th colspan="2" class="text-right">Delivery Charge + Takeaway</th>
                    <th><?php echo number_format($delivery_charge, 2); ?></th>
                </tr>
                <tr>
                    <th colspan="2" class="text-right">Grand Total</th>
                    <th><?php echo number_format($total + $delivery_charge, 2); ?></th>
                </tr>
            </tfoot>
        </table>
    </div>
    <div class="text-center">
        <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
    </div>

    <style>
        #uni_modal .modal-footer {
            display: none;
        }
    </style>

    <?php
} catch (PDOException $e) {
    error_log("View order error: " . $e->getMessage());
    echo "<div class='alert alert-danger'>Error loading order details. Please try again later.</div>";
}
?>