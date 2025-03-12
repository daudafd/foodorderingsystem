<?php 
include('db_connect.php');
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Admin Menu</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
<style>
    img#cimg,
    .cimg {
        max-height: 10vh;
        max-width: 6vw;
    }

    td {
        vertical-align: middle !important;
    }

    td p {
        margin: unset !important;
    }

    .custom-switch,
    .custom-control-input,
    .custom-control-label {
        cursor: pointer;
    }

    b.truncate {
        overflow: hidden;
        text-overflow: ellipsis;
        display: -webkit-box;
        -webkit-line-clamp: 3;
        -webkit-box-orient: vertical;
        font-size: small;
        color: #000000cf;
        font-style: italic;
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
</head>
<body>
<div class="container-fluid">
  <div class="w3-main" style="margin-left:300px;margin-top:43px;">
    <!-- Header -->
    <header class="w3-container" style="padding-top:22px">
      <h5><b><i class="fa fa-dashboard"></i> Menu List</b></h5>
    </header>
    
    <div class="col-lg-12">
      <div class="row">
        <!-- FORM Panel -->
        <div class="col-md-4">
          <form action="" id="manage-menu" enctype="multipart/form-data">
            <div class="card">
              <div class="card-header">Menu Form</div>
              <div class="card-body">
                <input type="hidden" name="id">
                <div class="form-group">
                  <label class="control-label">Menu Name</label>
                  <input type="text" class="form-control" name="name" required>
                </div>
                <div class="form-group">
                  <label class="control-label">Menu Description</label>
                  <textarea cols="30" rows="3" class="form-control" name="description"></textarea>
                </div>
                <div class="form-group">
                  <div class="custom-control custom-switch">
                    <input type="checkbox" name="status" class="custom-control-input" id="availability" checked>
                    <label class="custom-control-label" for="availability">Available</label>
                  </div>
                </div>
                <div class="form-group">
                  <label class="control-label">Category</label>
                  <select name="category_id" class="custom-select browser-default">
    <?php
    $cat = $conn->prepare("SELECT * FROM category_list ORDER BY name ASC");
    $cat->execute();
    while ($row = $cat->fetch(PDO::FETCH_ASSOC)) :
    ?>
        <option value="<?php echo $row['id'] ?>"><?php echo $row['name'] ?></option>
    <?php endwhile; ?>
</select>
</div>
<div class="form-group">
    <label class="control-label">Base Price</label>
    <input type="number" class="form-control text-right" name="price" step="any" required>
</div>
<div class="form-group">
    <label for="hasSizeOptions" class="control-label">Has Size Options? (For Meat items only)</label>
    <input type="checkbox" id="hasSizeOptions" name="has_size_options">
</div>
<div id="sizeOptionsFields" style="display:none;">
    <div class="form-group">
        <label class="control-label">Small Price</label>
        <input type="number" class="form-control text-right" name="price_small" step="any">
    </div>
    <div class="form-group">
        <label class="control-label">Medium Price</label>
        <input type="number" class="form-control text-right" name="price_medium" step="any">
    </div>
    <div class="form-group">
        <label class="control-label">Large Price</label>
        <input type="number" class="form-control text-right" name="price_large" step="any">
    </div>
</div>
<div class="form-group">
    <label class="control-label">Image</label>
    <input type="file" class="form-control" name="img" onchange="displayImg(this, $(this))">
</div>
<div class="form-group">
    <img src="" alt="" id="cimg">
</div>
</div>
<div class="card-footer">
    <div class="row">
        <div class="col-md-12">
            <button class="btn btn-sm btn-primary col-sm-3 offset-md-3"> Save</button>
            <button class="btn btn-sm btn-default col-sm-3" type="button" onclick="$('#manage-menu').get(0).reset()"> Cancel</button>
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
                    <th class="text-center">Img</th>
                    <th class="text-center">Item</th>
                    <th class="text-center">Action</th>
                </tr>
            </thead>
            <tbody>
                <?php 
                $i = 1;
                $cats = $conn->prepare("SELECT * FROM product_list ORDER BY id ASC");
                $cats->execute();
                while ($row = $cats->fetch(PDO::FETCH_ASSOC)) :
                ?>
                <tr>
                    <td class="text-center"><?php echo $i++ ?></td>
                    <td class="text-center">
                        <img src="<?php echo isset($row['img_path']) ? '../assets/img/'.$row['img_path'] : '' ?>" alt="" id="cimg">
                    </td>
                    <td>
                        <p>Name : <b><?php echo $row['name'] ?></b></p>
                        <p>Description : <b class="truncate"><?php echo $row['description'] ?></b></p>
                        <p>Base price : <b><?php echo "N".number_format($row['price'], 2) ?></b></p>
                        <p>Small price : <b><?php echo isset($row['price_small']) && $row['price_small'] !== null ? "N".number_format($row['price_small'], 2) : '-' ; ?></b></p>
                        <p>Medium price : <b><?php echo isset($row['price_medium']) && $row['price_medium'] !== null ? "N".number_format($row['price_medium'], 2) : '-' ; ?></b></p>
                        <p>Large price : <b><?php echo isset($row['price_large']) && $row['price_large'] !== null ? "N".number_format($row['price_large'], 2) : '-' ; ?></b></p>
                    </td>
                    <td class="text-center">
                        <button class="btn btn-sm btn-primary edit_menu" type="button" 
                            data-id="<?php echo $row['id'] ?>"
                            data-name="<?php echo $row['name'] ?>"
                            data-status="<?php echo $row['status'] ?>"
                            data-description="<?php echo $row['description'] ?>"
                            data-price="<?php echo $row['price'] ?>"
                            data-has_size_options="<?php echo isset($row['price_small']) ? 1 : 0; ?>"
                            data-price_small="<?php echo isset($row['price_small']) ? $row['price_small'] : ''; ?>"
                            data-price_medium="<?php echo isset($row['price_medium']) ? $row['price_medium'] : ''; ?>"
                            data-price_large="<?php echo isset($row['price_large']) ? $row['price_large'] : ''; ?>"
                            data-img_path="<?php echo $row['img_path'] ?>"
                        >Edit</button>
                        <button class="btn btn-sm btn-danger delete_menu" type="button" data-id="<?php echo $row['id'] ?>">Delete</button>
                    </td>
                </tr>
                <?php endwhile; ?>
            </tbody>
                </table>
            </div>
          </div>
        </div>
        <!-- Table Panel -->
      </div>
    </div>  
  </div>
</div>
  
<!-- Loading Overlay -->
<div id="loading-overlay" style="display: none; position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(0,0,0,0.5); z-index: 9999; justify-content: center; align-items: center;">
  <div class="spinner-border text-light" role="status">
    <span class="visually-hidden">Loading...</span>
  </div>
</div>
  
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
  function displayImg(input, _this) {
      if(input.files && input.files[0]){
          var reader = new FileReader();
          reader.onload = function(e){
              $('#cimg').attr('src', e.target.result);
          }
          reader.readAsDataURL(input.files[0]);
      }
  }
  
  function showLoading() { $('#loading-overlay').show(); }
  function hideLoading() { $('#loading-overlay').hide(); }
  
  $('#manage-menu').submit(function(e){
      e.preventDefault();
      var saveButton = $(this).find('button[type="submit"]');
      saveButton.prop('disabled', true);
      showLoading();
      $.ajax({
          url: 'ajax.php?action=save_menu',
          data: new FormData($(this)[0]),
          cache: false,
          contentType: false,
          processData: false,
          method: 'POST',
          type: 'POST',
          success: function(resp){
              try {
                  var response = JSON.parse(resp);
                  if(response.success){
                      Swal.fire({
                          icon: 'success',
                          title: 'Success!',
                          text: response.message,
                          confirmButtonText: 'OK'
                      }).then(function(){ location.reload(); });
                  } else {
                      Swal.fire({
                          icon: 'error',
                          title: 'Error!',
                          text: response.message,
                          confirmButtonText: 'OK'
                      }).then(function(){ saveButton.prop('disabled', false); });
                  }
              } catch(e){
                  Swal.fire({
                      icon: 'error',
                      title: 'Error!',
                      text: 'Unexpected error occurred. Please try again.'
                  }).then(function(){ saveButton.prop('disabled', false); });
              }
              hideLoading();
          },
          error: function(){
              Swal.fire({
                  icon: 'error',
                  title: 'Error!',
                  text: 'An error occurred while saving the menu. Please try again.'
              }).then(function(){ saveButton.prop('disabled', false); });
              hideLoading();
          }
      });
  });
  
  $('.edit_menu').click(function(){
      showLoading();
      var form = $('#manage-menu');
      form.get(0).reset();
      form.find("[name='id']").val($(this).data('id'));
      form.find("[name='name']").val($(this).data('name'));
      form.find("[name='description']").val($(this).data('description'));
      form.find("[name='price']").val($(this).data('price'));
      if($(this).data('status') == 1){
          $('#availability').prop('checked', true);
      } else {
          $('#availability').prop('checked', false);
      }
      if($(this).data('has_size_options') == 1){
          $('#hasSizeOptions').prop('checked', true);
          $("#sizeOptionsFields").show();
          form.find("[name='price_small']").val($(this).data('price_small'));
          form.find("[name='price_medium']").val($(this).data('price_medium'));
          form.find("[name='price_large']").val($(this).data('price_large'));
      } else {
          $('#hasSizeOptions').prop('checked', false);
          $("#sizeOptionsFields").hide();
      }
      form.find("#cimg").attr('src', '../assets/img/'+$(this).data('img_path'));
      hideLoading();
  });
  
  $('.delete_menu').click(function(){
      Swal.fire({
          title: 'Are you sure?',
          text: "You won't be able to revert this!",
          icon: 'warning',
          showCancelButton: true,
          confirmButtonColor: '#3085d6',
          cancelButtonColor: '#d33',
          confirmButtonText: 'Yes, delete it!'
      }).then((result)=>{
          if(result.isConfirmed){
              delete_menu($(this).data('id'));
          }
      });
  });
  
  function delete_menu(id){
      showLoading();
      $.ajax({
          url: 'ajax.php?action=delete_menu',
          method: 'POST',
          data: { id: id },
          dataType: 'json',
          success: function(resp){
              if(resp.success){
                  Swal.fire('Deleted!', resp.message, 'success').then(function(){ location.reload(); });
              } else {
                  Swal.fire({ icon: 'error', title: 'Oops...', text: resp.message });
              }
              hideLoading();
          },
          error: function(){
              Swal.fire({ icon: 'error', title: 'Oops...', text: 'An error occurred. Please try again.' });
              hideLoading();
          }
      });
  }
  
  $(document).ready(function(){
      $("#hasSizeOptions").on("change", function(){
          if($(this).is(":checked")){
              $("#sizeOptionsFields").show();
          } else {
              $("#sizeOptionsFields").hide();
          }
      });
  });
</script>
</body>
</html>
