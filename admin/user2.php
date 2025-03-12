<?php include('db_connect.php'); ?>

<div class="container-fluid">

    <div class="w3-main" style="margin-left:300px;margin-top:43px;">

        <header class="w3-container" style="padding-top:22px">
            <h5><b><i class="fa fa-dashboard"></i> Users</b></h5>
        </header>

        <div class="col-lg-12">
            <div class="row">
                <div class="col-md-4">
                    <form action="" id="manage-user">
                        <div class="card">
                            <div class="card-body">
                                <input type="hidden" name="id">
                                <div class="form-group">
                                    <label class="control-label">Name</label>
                                    <input type="text" class="form-control" placeholder="input your name" required="" name="name">
                                </div>
                                <div class="form-group">
                                    <label class="control-label">Username</label>
                                    <input type="text" class="form-control" placeholder="input your username" required="" name="username">
                                </div>
                                <div class="form-group">
                                    <label for="" class="control-label">Password</label>
                                    <input type="password" name="password" placeholder="Leave blank to keep current password" class="form-control">
                                </div>
                                <div class="form-group">
                                    <select name="type" required>
                                        <option value="2">Staff</option>
                                    </select>
                                </div>
                            </div>
                            <div class="card-footer">
                                <div class="row">
                                    <div class="col-md-12">
                                        <button class="btn btn-sm btn-primary col-sm-3 offset-md-3"> Save</button>
                                        <button class="btn btn-sm btn-default col-sm-3" type="button" onclick="$('#manage-user').get(0).reset()"> Cancel</button>
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
                                        <th class="text-center">Username</th>
                                        <th class="text-center">Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php
                                    $i = 1;
                                    $cats = $conn->prepare("SELECT * FROM users WHERE type = 2 ORDER BY name ASC");
                                    $cats->execute();
                                    while ($row = $cats->fetch(PDO::FETCH_ASSOC)) :
                                    ?>
                                        <tr>
                                            <td class="text-center"><?php echo $i++ ?></td>
                                            <td class="">
                                                <?php echo $row['name'] ?>
                                            </td>
                                            <td class="">
                                                <?php echo $row['username'] ?>
                                            </td>
                                            <td class="text-center">
                                                <button class="btn btn-sm btn-primary edit_user" type="button" data-id="<?php echo $row['id'] ?>" data-name="<?php echo $row['name'] ?>" data-username="<?php echo $row['username'] ?>">Edit</button>
                                                <button class="btn btn-sm btn-danger del_user" type="button" data-id="<?php echo $row['id'] ?>">Delete</button>
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

    <!-- Loading Overlay -->
    <div id="loading-overlay" style="display: none; position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(0,0,0,0.5); z-index: 9999; justify-content: center; align-items: center;">
        <div class="spinner-border text-light" role="status">
            <span class="visually-hidden">Loading...</span>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
  function showLoading() { $('#loading-overlay').show(); }
function hideLoading() { $('#loading-overlay').hide(); }

$('#manage-user').submit(function(e) {
    e.preventDefault();
    var saveButton = $(this).find('button[type="submit"]');
    saveButton.prop('disabled', true);
    showLoading();
    start_load();
    $.ajax({
        url: 'ajax.php?action=save_user',
        data: new FormData($(this)[0]),
        cache: false,
        contentType: false,
        processData: false,
        method: 'POST',
        success: function(resp) {
            var response = JSON.parse(resp);

            if (response.success) {
                Swal.fire({
                    icon: 'success',
                    title: 'Success!',
                    text: response.success,
                }).then((result) => {
                    if (result.isConfirmed) {
                        location.reload();
                    }
                });
            } else if (response.error) {
                Swal.fire({
                    icon: 'error',
                    title: 'Error!',
                    text: response.error,
                });
            }
            hideLoading();
            end_load();
        },
        error: function() {
            Swal.fire({
                icon: 'error',
                title: 'Error!',
                text: "An error occurred, please try again",
            });
            hideLoading();
            end_load();
        }
    });
});

$('.edit_user').click(function() {
    start_load();
    var cat = $('#manage-user');
    cat.get(0).reset();
    cat.find("[name='id']").val($(this).attr('data-id'));
    cat.find("[name='name']").val($(this).attr('data-name'));
    cat.find("[name='username']").val($(this).attr('data-username'));
    end_load();
});

$('.del_user').click(function() {
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
            del_user($(this).attr('data-id'));
        }
    });
});

function del_user(id) {
    showLoading();
    start_load();
    $.ajax({
        url: 'ajax.php?action=delete_user',
        method: 'POST',
        // dataType: 'json',
        data: {
            id: id
        },
        success: function(resp) {
            console.log(resp);
            var response = JSON.parse(resp);
            if (response.status === 'success') {
                Swal.fire({
                    icon: 'success',
                    title: 'Deleted!',
                    text: response.message,
                }).then((result) => {
                    if (result.isConfirmed) {
                        location.reload();
                    }
                });
            } else {
                Swal.fire({
                    icon: 'error',
                    title: 'Error!',
                    text: response.message,
                });
            }
            hideLoading();
            end_load();
        },
        error: function() {
            Swal.fire({
                icon: 'error',
                title: 'Error!',
                text: "An error occurred, please try again",
            });
            hideLoading();
            end_load();
        }
    });
}
    </script>
</div>