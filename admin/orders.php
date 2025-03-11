<?php
session_start();
?>
<div class="w3-main" style="margin-left:300px;margin-top:43px;">
    <header class="w3-container" style="padding-top:22px">
        <h5><b><i class="fa fa-dashboard"></i> Orders</b></h5>
    </header>

    <div class="container-fluid">
        <div class="card">
            <div class="card-body">
                <div class="table-responsive">
                    <table id="order-table" class="table table-bordered table-striped">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>Ref. No</th>
                                <th>Name</th>
                                <th>Location</th>
                                <th>Mobile</th>
                                <th>Order Time</th>
                                 <th>Confirmed Time</th>
                                <!--<th>Payment Type</th>-->
                                <th>Status</th>
                                <th></th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            $i = 1;
                            include 'db_connect.php';
                            $qry = $conn->query("SELECT * FROM orders ORDER BY created_at DESC");
                            while ($row = $qry->fetch_assoc()) :
                            ?>
                                <tr>
                                    <td><?php echo $i++ ?></td>
                                    <td><?php echo $row['reference_id'] ?></td>
                                    <td><?php echo $row['name'] ?></td>
                                    <td>
                                        <?php
                                        if ($row['delivery_charge'] == 1500) {
                                            echo "Futa";
                                        } elseif ($row['delivery_charge'] == 1100) {
                                            echo "Alagbaka / Oba-Ile";
                                        } elseif ($row['delivery_charge'] == 1300) {
                                            echo "Ijoka";
                                        } else {
                                            echo "Self Pickup";
                                        }
                                        ?>
                                    </td>
                                    <td><?php echo $row['mobile'] ?></td>
                                    <td><?php echo $row['created_at'] ?></td>
                                    <td><?php echo $row['confirmed_at'] ?></td>
                                    <!--<td><?php echo $row['transaction_reference'] ?></td>-->
                                    <?php if ($row['payment_status'] == 0) : ?>
                                        <td class="text-center"><span class="badge bg-secondary">For Verification</span></td>
                                    <?php elseif ($row['payment_status'] == 3) : ?>
                                        <td class="text-center"><span class="badge bg-danger">Cancelled</span></td>
                                    <?php else : ?>
                                        <td class="text-center"><span class="badge bg-success">Confirmed</span></td>
                                    <?php endif; ?>
                                    <td>
                                        <button class="btn btn-sm btn-primary view_order" data-id="<?php echo $row['id'] ?>">View Order</button>
                                    </td>
                                </tr>
                            <?php endwhile; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
        <style>
    #loading-overlay {
        display: none;
        position: fixed;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        background: rgba(0, 0, 0, 0.5);
        z-index: 9999;
        display: flex;
        justify-content: center;
        align-items: center;
    }

    #loading-overlay > div { /* Target only the spinner div */
        /* Add any specific styles for the spinner div here */
    }
</style>
        <div id="loading-overlay" style="display: none; position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(0, 0, 0, 0.5); z-index: 9999; justify-content: center; align-items: center;">
            <div class="spinner-border text-light" role="status">
                <span class="visually-hidden">Loading...</span>
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
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>

    <link href="https://cdn.datatables.net/v/bs5/jq-3.7.0/dt-2.1.8/af-2.7.0/b-3.2.0/b-colvis-3.2.0/date-1.5.4/r-3.0.3/sc-2.4.3/sb-1.8.1/datatables.min.css" rel="stylesheet">
    <script src="https://cdn.datatables.net/v/bs5/jq-3.7.0/dt-2.1.8/af-2.7.0/b-3.2.0/b-colvis-3.2.0/date-1.5.4/r-3.0.3/sc-2.4.3/sb-1.8.1/datatables.min.js"></script>

    <script>
        function showLoading() {
            $('#loading-overlay').show();
        }

        function hideLoading() {
            $('#loading-overlay').hide();
        }

        $(document).ready(function() {
            if ($('#order-table tbody tr').length > 0) {
                $('#order-table').DataTable({
                    "order": [[5, "desc"]],
                    "columnDefs": [{
                        "targets": [5],
                        "orderable": false
                    }],
                    "language": {
                        "emptyTable": "No orders available",
                        "zeroRecords": "No matching records found"
                    }
                });
            } else {
                $('#order-table').closest('.table-responsive').html('<div class="alert alert-info">No orders found.</div>');
            }

            $('.view_order').click(function() {
                showLoading();
                const orderId = $(this).attr('data-id');
                $.ajax({
                    url: 'view_order.php',
                    type: 'GET',
                    data: { id: orderId },
                    success: function(response) {
                        $('#order-details').html(response);
                        $('#orderModal').modal('show');
                        hideLoading();
                    },
                    error: function() {
                        alert("Error loading order details.");
                        hideLoading();
                    }
                });
            });

            $('#orderModal').on('hidden.bs.modal', function() {
                $(this).find(':focus').blur();
                $('#order-details').empty();
            });
        });
    </script>
</div>