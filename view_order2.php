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
        <?php
        $total = 0;
        $delivery_charge = 0;
        include 'admin/db_connect.php';

        // Fetch delivery charge from the orders table
        $order_id = $_GET['id'];
        $order_details = $conn->query("SELECT delivery_charge FROM orders WHERE id = $order_id");
        if ($order_details->num_rows > 0) {
            $delivery_charge = $order_details->fetch_assoc()['delivery_charge'];
        }

        // Fetch order list and calculate the total using o.price
        $qry = $conn->query("SELECT o.*, p.name, p.category_id FROM order_list o INNER JOIN product_list p ON o.product_id = p.id WHERE o.order_id = $order_id");
        while ($row = $qry->fetch_assoc()):
            $total += $row['qty'] * $row['price'];
        ?>
            <tr>
                <td><?php echo $row['qty'] ?></td>
                <td>
                    <?php
                    echo $row['name'];

                    // Conditional "with" for swallow foods
                    $swallow_categories = [10]; // Replace with your swallow category IDs
                    if (isset($row['category_id']) && in_array($row['category_id'], $swallow_categories)) {
                        echo " with " . $row['soup'];
                    } elseif (strpos(strtolower($row['name']), 'semo') !== false || strpos(strtolower($row['name']), 'fufu') !== false || strpos(strtolower($row['name']), 'eba') !== false){
                        echo " with " . $row['soup'];
                    }

                    // Conditional "Size:" for protein items
                    $protein_categories = [1]; // Replace with your protein category IDs
                    if (isset($row['category_id']) && in_array($row['category_id'], $protein_categories)) {
                        echo " : " . ucfirst($row['size']);
                    } elseif (strpos(strtolower($row['name']), 'chicken') !== false || strpos(strtolower($row['name']), 'beef') !== false || strpos(strtolower($row['name']), 'goat meat') !== false || strpos(strtolower($row['name']), 'fish') !== false){
                        echo " : " . ucfirst($row['size']);
                    }
                    ?>
                </td>
                <td><?php echo number_format($row['qty'] * $row['price'], 2) ?></td>
            </tr>
        <?php endwhile; ?>
    </tbody>
    <tfoot>
        <tr>
            <th colspan="2" class="text-right">Subtotal</th>
            <th><?php echo number_format($total, 2) ?></th>
        </tr>
        <tr>
            <th colspan="2" class="text-right">Delivery Charge</th>
            <th><?php echo number_format($delivery_charge, 2) ?></th>
        </tr>
        <tr>
            <th colspan="2" class="text-right">Grand Total</th>
            <th><?php echo number_format($total + $delivery_charge, 2) ?></th>
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