<?php
include 'db_connect.php';
?>
<div class="w3-main" style="margin-left:300px;margin-top:43px;">
    <header class="w3-container" style="padding-top:22px">
        <h5><b><i class="fa fa-dashboard"></i> Meat Option</b></h5>
    </header>

    <div class="container-fluid">
        <div class="col-lg-12">
            <div class="row mb-4">
                <div class="col-md-12">
                    <button class="col-md-2 float-right btn btn-sm btn-primary" id="new_meat_option"><i class="fa fa-plus"></i> New Meat Option</button>
                </div>
            </div>
            <div class="row">
                <div class="col-md-12">
                    <div class="card">
                        <div class="card-body">
                            <table class="table table-bordered">
                                <thead>
                                    <tr>
                                        <th>Meat Type</th>
                                        <th>Size</th>
                                        <th>Price</th>
                                        <th>Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php
                                    include 'db_connect.php';
                                    $qry = $conn->query("SELECT * FROM meat_options ORDER BY meat_type ASC, size ASC");
                                    while ($row = $qry->fetch_assoc()):
                                    ?>
                                        <tr>
                                            <td><?php echo $row['meat_type'] ?></td>
                                            <td><?php echo $row['size'] ?></td>
                                            <td><?php echo number_format($row['price'], 2) ?></td>
                                            <td>
                                                <button class="btn btn-sm btn-primary edit_meat_option" data-id="<?php echo $row['id'] ?>">Edit</button>
                                                <button class="btn btn-sm btn-danger delete_meat_option" data-id="<?php echo $row['id'] ?>">Delete</button>
                                            </td>
                                        </tr>
                                    <?php endwhile; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        $('#new_meat_option').click(function() {
            uni_modal("New Meat Option", "manage_meat_option.php");
        });
        $('.edit_meat_option').click(function() {
            uni_modal("Edit Meat Option", "manage_meat_option.php?id=" + $(this).attr('data-id'));
        });

        $('.delete_meat_option').click(function() {
            Swal.fire({
                title: 'Are you sure?',
                text: "You won't be able to revert this!",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Yes, delete it!'
            }).then((result) => {
                if (result.isConfirmed) {
                    delete_meat_option($(this).attr('data-id'));
                }
            });
        });

        function delete_meat_option($id) {
            start_load();
            $.ajax({
                url: 'ajax.php?action=delete_meat_option',
                method: 'POST',
                data: {
                    id: $id
                },
                success: function(resp) {
                    if (resp == 1) {
                        Swal.fire({
                            icon: 'success',
                            title: 'Successfully deleted',
                            showConfirmButton: false,
                            timer: 1000
                        }).then(() => {
                            location.reload();
                        });
                    } else {
                        Swal.fire({
                            icon: 'error',
                            title: 'An error occurred',
                        });
                    }
                    end_load();
                },
                error: function(){
                    Swal.fire({
                            icon: 'error',
                            title: 'An error occurred',
                        });
                    end_load();
                }
            });
        }
    </script>
</div>