<?php
include 'db_connect.php';

$qry = $conn->prepare("SELECT * FROM system_settings LIMIT 1");
$qry->execute();

$meta = [];

if ($qry->rowCount() > 0) {
    $result = $qry->fetch(PDO::FETCH_ASSOC);
    foreach ($result as $k => $val) {
        $meta[$k] = $val;
    }
}
?>

<link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
<script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
<link href="https://cdn.jsdelivr.net/npm/summernote@0.8.18/dist/summernote-bs4.min.css" rel="stylesheet">
<script src="https://cdn.jsdelivr.net/npm/summernote@0.8.18/dist/summernote-bs4.min.js"></script><script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<div class="container-fluid">
    <div class="w3-main" style="margin-left:300px;margin-top:43px;">
        <header class="w3-container" style="padding-top:22px">
            <h5><b><i class="fa fa-dashboard"></i> SYSTEM SETTINGS</b></h5>
        </header>

        <div class="card col-lg-12">
            <div class="card-body">
                <form action="" id="manage-settings" method="POST" enctype="multipart/form-data">
                    <div class="form-group">
                        <label for="name" class="control-label">System Name</label>
                        <input type="text" class="form-control" id="name" name="name" value="<?php echo isset($meta['name']) ? $meta['name'] : '' ?>" required>
                    </div>
                    <div class="form-group">
                        <label for="email" class="control-label">Email</label>
                        <input type="email" class="form-control" id="email" name="email" value="<?php echo isset($meta['email']) ? $meta['email'] : '' ?>" required>
                    </div>
                    <div class="form-group">
                        <label for="contact" class="control-label">Contact</label>
                        <input type="text" class="form-control" id="contact" name="contact" value="<?php echo isset($meta['contact']) ? $meta['contact'] : '' ?>" required>
                    </div>
                    <div class="form-group">
                        <label for="about" class="control-label">About Content</label>
                        <textarea id="about" name="about" class="form-control"><?php echo isset($meta['about_content']) ? $meta['about_content'] : ''; ?></textarea>
                    </div>
                    <div class="form-group">
                        <label for="" class="control-label">Image</label>
                        <input type="file" class="form-control" name="images[]" multiple onchange="displayImg(this, $(this))">
                    </div>
                    <div class="form-group">
                        <img src="<?php echo isset($meta['cover_img']) ? '../assets/img/' . $meta['cover_img'] : '' ?>" alt="" id="cimg">
                    </div>
                    <center>
                        <button class="btn btn-info btn-primary btn-block col-md-2">Save</button>
                    </center>
                </form>
            </div>
        </div>
        <style>
            img#cimg {
                max-height: 10vh;
                max-width: 6vw;
            }
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

    <script>
    function displayImg(input, _this) {
        if (input.files && input.files[0]) {
            var reader = new FileReader();
            reader.onload = function(e) {
                $('#cimg').attr('src', e.target.result);
            }
            reader.readAsDataURL(input.files[0]);
        }
    }

    function showLoading() { $('#loading-overlay').show(); }
    function hideLoading() { $('#loading-overlay').hide(); }

    $(document).ready(function() {
        $('#about').summernote({
            height: 300,
            toolbar: [
                ['style', ['style']],
                ['font', ['bold', 'underline', 'clear']],
                ['fontname', ['fontname']],
                ['color', ['color']],
                ['para', ['ul', 'ol', 'paragraph']],
                ['table', ['table']],
                ['insert', ['link', 'picture', 'video']],
                ['view', ['fullscreen', 'codeview', 'help']]
            ]
        });
    });

    $('#manage-settings').submit(function(e) {
        e.preventDefault();
        showLoading();
        start_load();
        $.ajax({
            url: 'ajax.php?action=save_settings',
            data: new FormData($(this)[0]),
            cache: false,
            contentType: false,
            processData: false,
            dataType: 'text',
            method: 'POST',
            type: 'POST',
            error: function(err) {
                console.log(err);
                hideLoading();
                end_load();
                Swal.fire({
                    icon: 'error',
                    title: 'Error!',
                    text: "An error occurred, please try again",
                });
            },
            success: function(resp) {
                try {
                    resp = JSON.parse(resp.trim());
                    if (resp.success) {
                        Swal.fire({
                            icon: 'success',
                            title: 'Success!',
                            text: resp.success,
                        }).then((result) => {
                            if (result.isConfirmed) {
                                location.reload();
                            }
                        });
                    } else if (resp.error) {
                        Swal.fire({
                            icon: 'error',
                            title: 'Error!',
                            text: resp.error,
                        });
                    }
                } catch (e) {
                    console.error("JSON Parse Error:", e, resp);
                    Swal.fire({
                        icon: 'error',
                        title: 'Error!',
                        text: "An unexpected error occurred.",
                    });
                }
                hideLoading();
                end_load();
            }
        });
    });
</script>
    </div>
</div>