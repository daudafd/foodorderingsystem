<?php
include 'admin/db_connect.php';

if (!isset($_GET['id']) || empty($_GET['id'])) {
    die("Error: No product ID provided.");
}

$id = intval($_GET['id']);
$qry = $conn->prepare("SELECT * FROM product_list WHERE id = :id");
$qry->execute([':id' => $id]);
$product = $qry->fetch(PDO::FETCH_ASSOC);

if (!$product) {
    die("Error: Product not found.");
}

// Fetch soup options if category_id is 10
$soups = []; // Initialize as an empty array
if ($product['category_id'] == 10) {
    $soups_stmt = $conn->prepare("SELECT * FROM soup_options");
    $soups_stmt->execute();
    $soups = $soups_stmt->fetchAll(PDO::FETCH_ASSOC); // Fetch all results into an array
}

// Check if the product has size options (meat options)
$hasSizeOptions_stmt = $conn->prepare("SELECT 1 FROM meat_options WHERE meat_type = :meat_type LIMIT 1");
$hasSizeOptions_stmt->execute([':meat_type' => $product['name']]);
$hasSizeOptions = $hasSizeOptions_stmt->fetchColumn(); // Use fetchColumn to get a single value

// Fetch size options with prices
$sizeOptions = [];
if ($hasSizeOptions) {
    $sizeResults_stmt = $conn->prepare("SELECT size, price FROM meat_options WHERE meat_type = :meat_type");
    $sizeResults_stmt->execute([':meat_type' => $product['name']]);
    while ($row = $sizeResults_stmt->fetch(PDO::FETCH_ASSOC)) {
        $sizeOptions[$row['size']] = $row['price'];
    }
}

$isMeatProduct = ($product['category_id'] == 1);

?>

<link href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.css" rel="stylesheet">
<script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.js"></script>

<div class="container-fluid">
    <div class="card">
        <img src="assets/img/<?php echo htmlspecialchars($product['img_path']); ?>" class="card-img-top" alt="Product Image">
        <div class="card-body">
            <h5 class="card-title"><?php echo htmlspecialchars($product['name']); ?></h5>
            <p class="card-text truncate"><?php echo htmlspecialchars($product['description']); ?></p>

            <?php if ($hasSizeOptions): ?>
                <div class="form-group">
                    <label for="sizeSelect">Select Size:</label>
                    <select id="sizeSelect" class="form-control">
                        <?php foreach ($sizeOptions as $size => $price): ?>
                            <option value="<?php echo htmlspecialchars($size); ?>">
                                <?php echo ucfirst(htmlspecialchars($size)); ?> - #<?php echo number_format($price, 2); ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                    <!-- <p id="sizePrice" class="card-text truncate"></p> -->
                </div>
            <?php else: ?>
                <p class="card-text">#<?php echo number_format($product['price'], 2); ?></p>
            <?php endif; ?>

            <?php if ($product['category_id'] == 10 && !empty($soups)): ?>
                <div class="form-group">
                    <label>Choose Soup:</label>
                    <select id="soupSelect" class="form-control">
                        <?php foreach ($soups as $soup): ?>
                            <option value="<?php echo htmlspecialchars($soup['soup_type']); ?>">
                                <?php echo htmlspecialchars($soup['soup_type']); ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
            <?php endif; ?>

            <div class="row">
                <div class="col-md-2"><label class="control-label">Qty</label></div>
                <div class="input-group mb-3">
                    <div class="input-group-prepend">
                        <button class="btn btn-outline-secondary" type="button" id="qty-minus"><span class="fa fa-minus"></span></button>
                    </div>
                    <input type="number" readonly value="1" min=1 class="form-control text-center" name="qty">
                    <div class="input-group-append">
                        <button class="btn btn-outline-secondary" type="button" id="qty-plus"><span class="fa fa-plus"></span></button>
                    </div>
                </div>
            </div>

            <div class="container-fluid">
                <div class="card">
                    <div class="card-body">
                        <div class="text-center">
                            <button class="btn btn-outline-primary btn-sm btn-block" id="add_to_cart_modal"><i class="fa fa-cart-plus"></i> Add to Cart</button>
                            <button class="btn btn-primary btn-sm btn-block" data-dismiss="modal">Close</button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
    #uni_modal_right .modal-footer {
        display: none;
    }

    .container-fluid .card-title {
        font-size: 14px;
    }

    .container-fluid .card-text {
        font-size: 12px;
    }

    .container-fluid label {
        font-size: 12px;
    }

    .container-fluid .form-control, .btn {
        font-size: 12px;
    }

    .container-fluid .card-img-top {
        max-width: auto;
        height: 200px;
    }
</style>

<script>
    $(document).ready(function() {
        toastr.options = {
            "closeButton": true,
            "debug": true,
            "newestOnTop": true,
            "progressBar": true,
            "positionClass": "toast-top-full-width",
            "preventDuplicates": true,
            "showDuration": "3000",
            "hideDuration": "1000",
            "timeOut": "5000",
            "extendedTimeOut": "1000",
            "showEasing": "swing",
            "hideEasing": "linear",
            "showMethod": "slideDown",
            "hideMethod": "fadeOut"
        };

        $('#qty-minus').click(function() {
            var qty = $('input[name="qty"]').val();
            if (qty == 1) {
                return false;
            } else {
                $('input[name="qty"]').val(parseInt(qty) - 1);
            }
        });

        $('#qty-plus').click(function() {
            var qty = $('input[name="qty"]').val();
            $('input[name="qty"]').val(parseInt(qty) + 1);
        });

        $('#sizeSelect').change(function() {
            updatePrice();
        });

        function updatePrice() {
            let size = $('#sizeSelect').val();
            let productName = '<?php echo $product['name'] ?>';
            let isMeatProduct = <?php echo $isMeatProduct ? 'true' : 'false'; ?>; // Pass PHP value to JavaScript

            if (isMeatProduct) { // Only make AJAX call if it's a meat product
                $.ajax({
                    url: 'admin/ajax.php?action=get_meat_price',
                    method: 'POST',
                    data: {
                        meat_type: productName,
                        size: size
                    },
                    dataType: 'json',
                    beforeSend: function() {
                        $('#sizePrice').text('Loading...');
                    },
                    success: function(response) {
                        if (response && response.status === 'success') {
                            $('#sizePrice').text('#' + response.price);
                        } else {
                            $('#sizePrice').text('Price not found.');
                            console.error("Server-side error:", response);
                        }
                    },
                    error: function(xhr, status, error) {
                        $('#sizePrice').text('Error fetching price.');
                        console.error("AJAX error:", status, error, xhr);
                    }
                });
            } else {
                // If it's not a meat product, clear the price display
                $('#sizePrice').text('');
            }
        }

        $('#add_to_cart_modal').click(function() {
            start_load();

            let data = {
                pid: '<?php echo $product['id']; ?>',
                qty: $('input[name="qty"]').val(),
                size: $('#sizeSelect').val()
            };

            <?php if ($product['category_id'] == 10): ?>
                data.soup_choice = $('#soupSelect').val();
            <?php endif; ?>

            $.ajax({
                url: 'admin/ajax.php?action=add_to_cart',
                method: 'POST',
                data: data,
                dataType: 'json',
                success: function(response) {
                    if (response.status === 'success') {
                        window.parent.postMessage({
                            type: 'toast',
                            message: response.message,
                            status: 'success'
                        }, '*');
                        window.parent.$('.item_count').html(parseInt(window.parent.$('.item_count').html()) + parseInt($('[name="qty"]').val()));
                    } else {
                        window.parent.postMessage({
                            type: 'toast',
                            message: response.message,
                            status: 'error'
                        }, '*');
                    }
                    $('.modal').modal('hide');
                    end_load();
                },
                error: function(xhr, status, error) {
                    window.parent.postMessage({
                        type: 'toast',
                        message: 'An error occurred while adding to cart: ' + status + ' - ' + error,
                        status: 'error'
                    }, '*');
                    console.error("AJAX error:", status, error, xhr);
                    $('.modal').modal('hide');
                    end_load();
                }
            });
        });
        updatePrice();
    });
</script>