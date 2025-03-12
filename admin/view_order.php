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
            include 'db_connect.php';

            try {
                // Fetch delivery charge from the orders table
                $order_id = isset($_GET['id']) ? intval($_GET['id']) : 0;
                $stmt_delivery = $conn->prepare("SELECT delivery_charge FROM orders WHERE id = :order_id");
                $stmt_delivery->execute([':order_id' => $order_id]);
                $delivery_charge_row = $stmt_delivery->fetch(PDO::FETCH_ASSOC);
                $delivery_charge = $delivery_charge_row ? floatval($delivery_charge_row['delivery_charge']) : 0;

                // Fetch order list and calculate the total
                $stmt_order = $conn->prepare("SELECT o.*, p.name FROM order_list o INNER JOIN product_list p ON o.product_id = p.id WHERE o.order_id = :order_id");
                $stmt_order->execute([':order_id' => $order_id]);
                $order_items = $stmt_order->fetchAll(PDO::FETCH_ASSOC);

                foreach ($order_items as $row):
                    $total += $row['qty'] * $row['price'];
                    ?>
                    <tr>
                        <td><?php echo htmlspecialchars($row['qty']); ?></td>
                        <td><?php echo htmlspecialchars($row['name']); ?> <?php echo htmlspecialchars($row['size']); ?></td>
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
                    <th colspan="2" class="text-right">Delivery Charge</th>
                    <th><?php echo number_format($delivery_charge, 2); ?></th>
                </tr>
                <tr>
                    <th colspan="2" class="text-right">Grand Total</th>
                    <th><?php echo number_format($total + $delivery_charge, 2); ?></th>
                </tr>
            </tfoot>
        </table>
        <?php
        $order_status = 0;
        $stmt_status = $conn->prepare("SELECT payment_status FROM orders WHERE id = :order_id");
        $stmt_status->execute([':order_id' => $order_id]);
        $order_status_row = $stmt_status->fetch(PDO::FETCH_ASSOC);
        if ($order_status_row) {
            $order_status = $order_status_row['payment_status'];
        }
        ?>
        <div class="text-center">
            <?php if ($order_status == 0): // Show only if not confirmed ?>
                <button class="btn btn-primary" id="confirm" type="button" onclick="confirm_order()">Confirm</button>
                <button class="btn btn-danger" id="cancel" type="button" onclick="cancel_order()">Cancel Order</button>
            <?php endif; ?>
            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
        </div>
    </div>

    <style>
        #uni_modal .modal-footer {
            display: none;
        }
    </style>

    <script>
        function confirm_order() {
            start_load();
            $.ajax({
                url: 'ajax.php?action=confirm_order',
                method: 'POST',
                data: {id: '<?php echo isset($_GET['id']) ? $_GET['id'] : 0; ?>'},
                dataType: 'json',
                success: function(resp) {
                    if (resp.success) {
                        Swal.fire({
                            title: "Order Confirmed!",
                            text: resp.success,
                            icon: "success",
                            timer: 1500,
                            showConfirmButton: false
                        }).then(() => {
                            location.reload();
                        });
                    } else {
                        Swal.fire({
                            title: "Error!",
                            text: resp.error,
                            icon: "error",
                            timer: 1500,
                            showConfirmButton: false
                        });
                    }
                },
                error: function() {
                    Swal.fire({
                        title: "An error occurred!",
                        text: "Please try again.",
                        icon: "error",
                        timer: 1500,
                        showConfirmButton: false
                    });
                }
            });
        }

        function cancel_order() {
            start_load();
            $.ajax({
                url: 'ajax.php?action=cancel_order',
                method: 'POST',
                data: {id: '<?php echo isset($_GET['id']) ? $_GET['id'] : 0; ?>'},
                dataType: 'json',
                success: function(resp) {
                    if (resp.success) {
                        Swal.fire({
                            title: "Order Canceled!",
                            text: resp.message,
                            icon: "success",
                            timer: 1500,
                            showConfirmButton: false
                        }).then(() => {
                            location.reload();
                        });
                    } else {
                        Swal.fire({
                            title: "Error!",
                            text: resp.message,
                            icon: "error",
                            timer: 1500,
                            showConfirmButton: false
                        });
                    }
                },
                error: function() {
                    Swal.fire({
                        title: "An error occurred!",
                        text: "Please try again.",
                        icon: "error",
                        timer: 1500,
                        showConfirmButton: false
                    });
                }
            });
        }
    </script>

    <script>
        $(document).ready(function() {
            var toastMessage = localStorage.getItem('toast_message');
            var toastType = localStorage.getItem('toast_type');

            if (toastMessage) {
                alert_toast(toastMessage, toastType);
                localStorage.removeItem('toast_message');
                localStorage.removeItem('toast_type');
            }

            $('#preloader').fadeOut('fast', function() {
                $(this).remove();
            });
        });
    </script>

    <?php
    } catch(PDOException $e){
        echo "<div class='alert alert-danger'>Error loading order details. Please try again later.</div>";
        error_log("view_order error: " . $e->getMessage());
    }
    ?>