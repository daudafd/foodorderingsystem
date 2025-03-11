<?php include('db_connect.php'); ?>

<div class="container-fluid">

    <div class="w3-main" style="margin-left:300px;margin-top:43px;">

        <header class="w3-container" style="padding-top:22px">
            <h5><b><i class="fa fa-dashboard"></i> Menu Category</b></h5>
        </header>

        <div class="col-lg-12">
            <div class="row">
                <div class="col-md-4">
                    <form action="" id="manage-category">
                        <div class="card">
                            <div class="card-header">
                                Category Form
                            </div>
                            <div class="card-body">
                                <input type="hidden" name="id">
                                <div class="form-group">
                                    <label class="control-label">Category</label>
                                    <input type="text" class="form-control" name="name">
                                </div>

                            </div>

                            <div class="card-footer">
                                <div class="row">
                                    <div class="col-md-12">
                                        <button class="btn btn-sm btn-primary col-sm-3 offset-md-3">Save</button>
                                        <button class="btn btn-sm btn-default col-sm-3" type="button" onclick="$('#manage-category').get(0).reset()">Cancel</button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
                <div class="col-md-8">
                    <div class="card">
                        <div class="card-body">
                            <table class="table table-bordered table-hover">
                                <thead>
                                    <tr>
                                        <th class="text-center">#</th>
                                        <th class="text-center">Name</th>
                                        <th class="text-center">Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php
                                    $i = 1;
                                    $cats = $conn->query("SELECT * FROM category_list order by id asc");
                                    while ($row = $cats->fetch_assoc()) :
                                        ?>
                                        <tr>
                                            <td class="text-center"><?php echo $i++ ?></td>
                                            <td class="">
                                                <?php echo $row['name'] ?>
                                            </td>
                                            <td class="text-center">
                                                <button class="btn btn-sm btn-primary edit_cat" type="button" data-id="<?php echo $row['id'] ?>" data-name="<?php echo $row['name'] ?>">Edit</button>
                                                <button class="btn btn-sm btn-danger delete_cat" type="button" data-id="<?php echo $row['id'] ?>">Delete</button>
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
    <style>

        td {
            vertical-align: middle !important;
        }

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

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
    function showLoading() {
        $('#loading-overlay').show();
    }

    function hideLoading() {
        $('#loading-overlay').hide();
    }

    $('#manage-category').submit(function (e) {
        e.preventDefault();
        let saveButton = $(this).find('button[type="submit"]');
        saveButton.prop('disabled', true);
        showLoading();
        $.ajax({
            url: 'ajax.php?action=save_category',
            data: new FormData($(this)[0]),
            cache: false,
            contentType: false,
            processData: false,
            method: 'POST',
            success: function (resp) {
                var response = JSON.parse(resp);
                if (response.success) {
                    Swal.fire({
                        icon: 'success',
                        title: 'Success!',
                        text: response.success,
                        confirmButtonText: 'OK'
                    }).then(() => {
                        location.reload();
                    });
                } else if (response.error) {
                    Swal.fire({
                        icon: 'error',
                        title: 'Error!',
                        text: response.error,
                        confirmButtonText: 'OK'
                    }).then(() => {
                        saveButton.prop('disabled', false);
                    });
                }
                hideLoading();
            },
            error: function () {
                Swal.fire({
                    icon: 'error',
                    title: 'Error!',
                    text: "An error occurred, please try again",
                    confirmButtonText: 'OK'
                }).then(() => {
                    saveButton.prop('disabled', false);
                });
                hideLoading();
            }
        });
    });

    $('.edit_cat').click(function () {
        showLoading();
        var cat = $('#manage-category');
        cat.get(0).reset();
        cat.find("[name='id']").val($(this).attr('data-id'));
        cat.find("[name='name']").val($(this).attr('data-name'));
        hideLoading();
    });

    $('.delete_cat').click(function () {
        _conf("Are you sure you want to delete this category?", "delete_cat", [$(this).attr('data-id')]);
    });

    function delete_cat(id) {
        showLoading();
        $.ajax({
            url: 'ajax.php?action=delete_category',
            method: 'POST',
            data: { id: id },
            success: function (resp) {
                var response = JSON.parse(resp);
                if (response.success) {
                    Swal.fire({
                        icon: 'success',
                        title: 'Success!',
                        text: response.success,
                        confirmButtonText: 'OK'
                    }).then(() => {
                        location.reload();
                    });
                } else if (response.error) {
                    Swal.fire({
                        icon: 'error',
                        title: 'Error!',
                        text: response.error,
                        confirmButtonText: 'OK'
                    });
                }
                hideLoading();
            },
            error: function () {
                Swal.fire({
                    icon: 'error',
                    title: 'Error!',
                    text: "An error occurred, please try again",
                    confirmButtonText: 'OK'
                });
                hideLoading();
            }
        });
    }

    function _conf(msg, func, params = []) {
        Swal.fire({
            title: 'Confirmation',
            text: msg,
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            confirmButtonText: 'Yes, delete it!'
        }).then((result) => {
            if (result.isConfirmed) {
                window[func].apply(this, params);
            }
        });
    }
</script>