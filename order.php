<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
?>
<header class="masthead">
    <div class="container h-100">
        <div class="row h-100 align-items-center justify-content-center text-center">
            <div class="col-lg-10 align-self-end mb-4 page-title">
                <h3 class="text-white">My Orders</h3>
                <hr class="divider my-4" />
            </div>
        </div>
    </div>
</header>

<div class="container-fluid">
    <div class="card">
        <div class="card-body">
            <div class="table-responsive">
                <table id="order-table" class="table table-bordered table-striped">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Reference No.</th>
                            <th>Total Amount</th>
                            <th>Order Date</th>
                            <th>Order Status</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        include('admin/db_connect.php');

                        $data = ""; // Initialize query filter

                        // Check if the user is logged in
                        if (isset($_SESSION['login_user_id'])) {
                            $data = "WHERE user_id = :user_id AND payment_status IN (0, 1, 2, 3)";
                        } else {
                            $ip = isset($_SERVER['HTTP_CLIENT_IP']) ? $_SERVER['HTTP_CLIENT_IP'] :
                                (isset($_SERVER['HTTP_X_FORWARDED_FOR']) ? $_SERVER['HTTP_X_FORWARDED_FOR'] : $_SERVER['REMOTE_ADDR']);
                            $data = "WHERE client_ip = :ip AND payment_status IN (0, 1, 2, 3)";
                        }

                        // Fetch orders with latest order first
                        $qry = $conn->prepare("SELECT * FROM orders $data ORDER BY created_at DESC");

                        if (isset($_SESSION['login_user_id'])) {
                            $qry->execute([':user_id' => $_SESSION['login_user_id']]);
                        } else {
                            $qry->execute([':ip' => $ip]);
                        }

                        if ($qry->rowCount() > 0):
                            // Calculate the total number of rows
                            $totalRows = $qry->rowCount();
                            $i = $totalRows; // Initialize counter with total rows

                            while ($row = $qry->fetch(PDO::FETCH_ASSOC)):
                                ?>
                                <tr>
                                    <td><?php echo $i--; ?></td>
                                    <td><?php echo $row['reference_id']; ?></td>
                                    <td><?php echo $row['total_amount']; ?></td>
                                    <td><?php echo $row['created_at']; ?></td>
                                    <td class="text-center">
                                        <?php
                                        if ($row['payment_status'] == 0): ?>
                                            <span class="badge bg-info">Awaiting confirmation</span>
                                        <?php elseif ($row['payment_status'] == 1): ?>
                                            <span class="badge bg-success">Ready For Pickup</span>
                                        <?php elseif ($row['payment_status'] == 2): ?>
                                            <span class="badge bg-success">Dispatched</span>
                                        <?php elseif ($row['payment_status'] == 3): ?>
                                            <span class="badge bg-danger">Cancelled</span>
                                        <?php endif; ?>
                                    </td>
                                    <td>
                                        <button class="btn btn-sm btn-primary view_order2" data-id="<?php echo $row['id']; ?>">View Order</button>
                                    </td>
                                </tr>
                            <?php endwhile; else: ?>
                            <tr>
                                <td colspan="6" class="text-center">No orders found.</td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="orderModal" tabindex="-1" aria-labelledby="orderModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="orderModalLabel">Order Details</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div id="order-details"></div>
            </div>
        </div>
    </div>
</div>


<link href="https://cdn.datatables.net/v/bs5/jq-3.7.0/dt-2.1.8/af-2.7.0/b-3.2.0/b-colvis-3.2.0/date-1.5.4/r-3.0.3/sc-2.4.3/sb-1.8.1/datatables.min.css" rel="stylesheet">
<script src="https://cdn.datatables.net/v/bs5/jq-3.7.0/dt-2.1.8/af-2.7.0/b-3.2.0/b-colvis-3.2.0/date-1.5.4/r-3.0.3/sc-2.4.3/sb-1.8.1/datatables.min.js"></script>
<script>
$(document).ready(function() {
    // Initialize DataTable if there are rows
    if ($('#order-table tbody tr').length > 0) {
        $('#order-table').DataTable({
            "order": [[3, "desc"]], // Default sorting by order date (latest first)
            "columnDefs": [
                { "targets": [5], "orderable": false }
            ],
            "language": {
                "emptyTable": "No orders available",
                "zeroRecords": "No matching records found"
            }
        });;
    } else {
        // If no orders, show message instead of table
        $('#order-table').closest('.table-responsive').html('<div class="alert alert-info">No orders found.</div>');
    }

    // Trigger modal when "View Order" button is clicked
    $('.view_order2').click(function() {
        const orderId = $(this).attr('data-id');
        $.ajax({
            url: 'view_order2.php',
            type: 'GET',
            data: { id: orderId },
            success: function(response) {
                $('#order-details').html(response); // Load the order details into the modal
                $('#orderModal').modal('show'); // Show the modal
            },
            error: function() {
                alert("Error loading order details.");
            }
        });
    });
});

</script>