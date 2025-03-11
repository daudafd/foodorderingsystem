<?php
include 'db_connect.php';
if (isset($_GET['id'])) {
    $qry = $conn->query("SELECT * FROM soup_options WHERE id = " . $_GET['id']);
    foreach ($qry->fetch_array() as $k => $v) {
        $$k = $v;
    }
}
?>
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
<div class="container-fluid">
    <form action="" id="manage-soup-option">
        <input type="hidden" name="id" value="<?php echo isset($id) ? $id : '' ?>">
        <div class="form-group">
            <label for="soup_type" class="control-label">Soup Type</label>
            <input type="text" name="soup_type" id="soup_type" class="form-control" value="<?php echo isset($soup_type) ? $soup_type : '' ?>" required>
        </div>
    </form>
    <div id="loading-overlay" style="display: none; position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(0, 0, 0, 0.5); z-index: 9999; justify-content: center; align-items: center;">
        <div class="spinner-border text-light" role="status">
            <span class="visually-hidden">Loading...</span>
        </div>
    </div>
</div>

<script>
    function showLoading() {
        $('#loading-overlay').show();
    }

    function hideLoading() {
        $('#loading-overlay').hide();
    }

    $('#manage-soup-option').submit(function (e) {
        e.preventDefault();
        var $submitBtn = $(this).find('button[type="submit"]');
        var originalHTML = $submitBtn.html();

        showLoading();
        $.ajax({
            url: 'ajax.php?action=save_soup_option',
            method: 'POST',
            data: $(this).serialize(),
            success: function (resp) {
                hideLoading();
                $submitBtn.html(originalHTML);
                if (resp == 1) {
                    Swal.fire({
                        icon: 'success',
                        title: 'Successfully saved',
                        showConfirmButton: false,
                        timer: 1000
                    }).then(function () {
                        $('#soupOptionModal').modal('hide'); // Close modal
                        location.reload();
                    });
                } else {
                    Swal.fire({
                        icon: 'error',
                        title: 'An error occurred',
                    });
                }
            },
            error: function(){
                hideLoading();
                $submitBtn.html(originalHTML);
                Swal.fire({
                    icon: 'error',
                    title: 'An error occurred',
                });
            }
        });
    });
</script>