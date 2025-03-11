<?php
include 'db_connect.php';
if (isset($_GET['id'])) {
    $qry = $conn->query("SELECT * FROM meat_options WHERE id = " . $_GET['id']);
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
    <form action="" id="manage-meat-option">
        <input type="hidden" name="id" value="<?php echo isset($id) ? $id : '' ?>">
        <div class="form-group">
            <label for="meat_type" class="control-label">Meat Type</label>
            <input type="text" name="meat_type" id="meat_type" class="form-control" value="<?php echo isset($meat_type) ? $meat_type : '' ?>" required>
        </div>
        <div class="form-group">
            <label for="size" class="control-label">Size</label>
            <input type="text" name="size" id="size" class="form-control" value="<?php echo isset($size) ? $size : '' ?>" required>
        </div>
        <div class="form-group">
            <label for="price" class="control-label">Price</label>
            <input type="number" name="price" id="price" class="form-control" value="<?php echo isset($price) ? $price : '' ?>" required>
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

    $('#manage-meat-option').submit(function (e) {
        e.preventDefault();
        var $submitBtn = $(this).find('button[type="submit"]');
        var originalHTML = $submitBtn.html();

        showLoading();
        $.ajax({
            url: 'ajax.php?action=save_meat_option',
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
                        $('#meatOptionModal').modal('hide'); // Close modal
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