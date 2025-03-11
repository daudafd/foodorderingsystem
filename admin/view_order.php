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

            // Fetch delivery charge from the orders table
            $order_id = $_GET['id'];
            $order_details = $conn->query("SELECT delivery_charge FROM orders WHERE id = $order_id");
            if ($order_details->num_rows > 0) {
                $delivery_charge = $order_details->fetch_assoc()['delivery_charge'];
            }

            // Fetch order list and calculate the total
            $qry = $conn->query("SELECT o.*, p.name FROM order_list o INNER JOIN product_list p ON o.product_id = p.id WHERE o.order_id = $order_id");
            while ($row = $qry->fetch_assoc()):
                $total += $row['qty'] * $row['price'];
            ?>
            <tr>
                <td><?php echo $row['qty'] ?></td>
                <td><?php echo $row['name'] ?>  <?php echo $row['size'] ?></td>
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
    <?php
        $order_status = 0;
        $order_details = $conn->query("SELECT payment_status FROM orders WHERE id = $order_id");
        if ($order_details->num_rows > 0) {
            $order_data = $order_details->fetch_assoc();
            $order_status = $order_data['payment_status'];
        }
    ?>
    <div class="text-center">
    <?php if ($order_status == 0): // Show only if not confirmed ?>
            <button class="btn btn-primary" id="confirm" type="button" onclick="confirm_order()">Confirm</button>
            <button class="btn btn-danger" id="cancel" type="button" onclick="cancel_order()">Cancel Order</button>
        <?php endif; ?>
        <!--<button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>-->
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
    start_load(); // Show loading animation (if necessary)

    $.ajax({
        url: 'ajax.php?action=confirm_order',
        method: 'POST',
        data: {id: '<?php echo $_GET['id']; ?>'}, // Send the order ID via POST
        dataType: 'json', // Expect a JSON response
        success: function(resp) {
            if (resp.success) {
                Swal.fire({
                    title: "Order Confirmed!",
                    text: resp.success,
                    icon: "success",
                    timer: 1500,
                    showConfirmButton: false
                }).then(() => {
                    location.reload(); // Reload after showing success message
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
    start_load(); // Show loading animation (if necessary)

    $.ajax({
        url: 'ajax.php?action=cancel_order',
        method: 'POST',
        data: {id: '<?php echo $_GET['id']; ?>'},
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
// When the page loads, display the toast message if it exists in localStorage
$(document).ready(function(){
    // Check if a toast message exists in localStorage
    var toastMessage = localStorage.getItem('toast_message');
    var toastType = localStorage.getItem('toast_type');

    if (toastMessage) {
        // Display the toast with the message
        alert_toast(toastMessage, toastType);
        
        // Remove the message from localStorage after showing it
        localStorage.removeItem('toast_message');
        localStorage.removeItem('toast_type');
    }

    // Hide preloader if it's visible
    $('#preloader').fadeOut('fast', function() {
        $(this).remove();
    });
});
</script>
