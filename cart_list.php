 <!-- Masthead-->
 <header class="masthead">
     <div class="container h-100">
         <div class="row h-100 align-items-center justify-content-center text-center">
             <div class="col-lg-10 align-self-end mb-4 page-title">
                 <h3 class="text-white">Cart List</h3>
                 <hr class="divider my-4" />
             </div>

         </div>
     </div>
 </header>
<section class="page-section" id="menu">
    <div class="container">
        <div class="row">
            <div class="col-lg-8">
                <div class="sticky">
                    <div class="card">
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-8"><b>Item(s)</b></div>
                                <div class="col-md-4 text-right"><b>Total</b></div>
                            </div>
                        </div>
                    </div>
                </div>
                <?php
                if (isset($_SESSION['login_user_id'])) {
                    $data = "where c.user_id = '" . $_SESSION['login_user_id'] . "' ";
                } else {
                    $ip = isset($_SERVER['HTTP_CLIENT_IP']) ? $_SERVER['HTTP_CLIENT_IP'] : (isset($_SERVER['HTTP_X_FORWARDED_FOR']) ? $_SERVER['HTTP_X_FORWARDED_FOR'] : $_SERVER['REMOTE_ADDR']);
                    $data = "where c.client_ip = '" . $ip . "' ";
                }
                $total = 0;
                $get = $conn->query("SELECT *,c.id as cid, c.price as cprice, p.category_id as category_id FROM cart c inner join product_list p on p.id = c.product_id " . $data);
                while ($row = $get->fetch_assoc()):
                    $total += ($row['qty'] * $row['cprice']);
                    $_SESSION['total_amount'] = $total;
                ?>
                <div class="card">
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-6" style="text-align: -webkit-center">
                                <a href="javascript:void(0)" class="rem_cart btn btn-sm btn-outline-danger" data-id="<?php echo $row['cid'] ?>"><i class="fa fa-trash"></i></a>
                                <img src="assets/img/<?php echo $row['img_path'] ?>" alt="">
                            </div>
                            <div class="col-md-4">
                                <p><b><large>
                                    <?php
                                    echo $row['name'];
                                    // Conditional "with" for swallow foods
                                    $swallow_categories = [10]; // Replace with your swallow category IDs
                                    if (isset($row['category_id']) && in_array($row['category_id'], $swallow_categories)) {
                                        echo " with " . $row['soup'];
                                    } elseif (strpos(strtolower($row['name']), 'semo') !== false || strpos(strtolower($row['name']), 'poundo yam') !== false || strpos(strtolower($row['name']), 'eba') !== false){
                                        echo " with " . $row['soup'];
                                    }
                                    ?>
                                </large></b></p>
                                <p><b><small>Desc :<?php echo $row['description'] ?></small></b></p>
                                <p><b><small>
                                    <?php
                                    // Assuming you have a way to determine if it's a "protein" food
                                    // You might check a category ID, a specific name, or some other criteria
                                    $protein_categories = [1]; // Example category IDs for protein foods
                                
                                    // Example using category_id:
                                    if (isset($row['category_id']) && in_array($row['category_id'], $protein_categories)) {
                                        echo "Size: " . ucfirst($row['size']);
                                    }
                                    // Example using product name:
                                    elseif (strpos(strtolower($row['name']), 'chicken') !== false || strpos(strtolower($row['name']), 'beef') !== false || strpos(strtolower($row['name']), 'fish') !== false){
                                        echo "Size: " . ucfirst($row['size']);
                                    }
                                
                                    ?>
                                </small></b></p>
                                <p> <b><small>Unit Price : N<?php echo number_format($row['cprice'], 2) ?></small></b></p>
                                <p><small>QTY :</small></p>
                                <div class="input-group mb-3">
                                    <div class="input-group-prepend">
                                        <button class="btn btn-outline-secondary qty-minus" type="button" data-id="<?php echo $row['cid'] ?>"><span class="fa fa-minus"></span></button>
                                    </div>
                                    <input type="number" readonly value="<?php echo $row['qty'] ?>" min=1 class="form-control text-center" name="qty">
                                    <div class="input-group-prepend">
                                        <button class="btn btn-outline-secondary qty-plus" type="button" id="" data-id="<?php echo $row['cid'] ?>"><span class="fa fa-plus"></span></button>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-4 text-right">
                                <b><large><?php echo number_format($row['qty'] * $row['cprice'], 2) ?></large></b>
                            </div>
                        </div>
                    </div>
                </div>
                <?php endwhile; ?>
            </div>
            <div class="col-md-4">
                <div class="sticky">
                    <div class="card">
                        <div class="card-body">
                            <p style="display: flex; justify-content: space-between;">
                                <span>
                                    <large>Total Amount</large>
                                </span>
                                <span><b><?php echo number_format($total, 2) ?></b></span>
                            </p>
                            <hr>
                            <div class="text-center">
                                <button class="btn btn-block btn-outline-primary" type="button" id="checkout">Proceed to Checkout</button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
 <div class="modal fade" id="confirmDeleteModal" tabindex="-1" role="dialog" aria-labelledby="confirmDeleteModalLabel"
     aria-hidden="true">
     <div class="modal-dialog" role="document">
         <div class="modal-content">
             <div class="modal-header">
                 <h5 class="modal-title" id="confirmDeleteModalLabel">Confirm Delete</h5>
                 <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                     <span aria-hidden="true">&times;</span>
                 </button>
             </div>
             <div class="modal-body">
                 Are you sure you want to remove this item from your cart?
             </div>
             <div class="modal-footer">
                 <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                 <button type="button" class="btn btn-danger" id="confirmDeleteBtn">Delete</button>
             </div>
         </div>
     </div>
 </div>

 <div class="position-fixed bottom-0 right-0 p-3" style="z-index: 5; right: 0; bottom: 0;">
     <div id="liveToast" class="toast hide" role="alert" aria-live="assertive" aria-atomic="true" data-delay="3000">
         <div class="toast-header">
             <strong class="mr-auto">Success</strong>
             <button type="button" class="ml-2 mb-1 close" data-dismiss="toast" aria-label="Close">
                 <span aria-hidden="true">&times;</span>
             </button>
         </div>
         <div class="toast-body">
             Item removed from cart.
         </div>
     </div>
 </div>
 <style>
.card p {
    margin: unset
}

.card img {
    max-width: calc(100%);
    max-height: calc(59%);
}

div.sticky {
    position: -webkit-sticky;
    /* Safari */
    position: sticky;
    top: 4.7em;
    z-index: 10;
    background: white
}

.rem_cart {
    position: absolute;
    left: 0;
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
            "preventDuplicates": false,
            "showDuration": "3000",
            "hideDuration": "1000",
            "timeOut": "5000",
            "extendedTimeOut": "1000",
            "showEasing": "swing",
            "hideEasing": "linear",
            "showMethod": "slideDown",
            "hideMethod": "fadeOut"
        };
});

updateCartCount();


$('.view_prod').click(function() {
    uni_modal_right('Product', 'view_prod.php?id=' + $(this).attr('data-id'))
})

function start_load() {
    $('body').prepend('<div id="preloader">Loading...</div>'); // Example loader
}

function end_load() {
    $('#preloader').remove();
}

function updateCartCount() {
        $.ajax({
            url: 'admin/ajax.php?action=get_cart_count',
            method: 'GET',
            dataType: 'json',
            success: function(resp) {
                if (resp.success) {
                    $('.item_count').text(resp.count);
                    // No need for .html() here, .text() is sufficient for a number
                } else {
                    console.error("Error getting cart count:", resp.error);
                }
            },
            error: function(jqXHR, textStatus, errorThrown) {
                console.error("AJAX Error:", textStatus, errorThrown);
            }
        });
    }

$(document).ready(function() {
    updateCartCount(); // Call on page load
});

function update_qty(qty, id) {
        $.ajax({
            url: 'admin/ajax.php?action=update_cart_qty',
            method: "POST",
            data: {
                id: id,
                qty: qty
            },
            success: function(resp) {
                try {
                    let res = JSON.parse(resp);
                    if (res.success) {
                        $('#total_amount').text(res.total);
                        $(`#item_total_${id}`).text(res.item_total);
                        updateCartCount();
                        toastr.success("Cart updated successfully!");
                         location.reload(); // Reload the page if item is added
                    } else {
                        console.error("Error updating cart quantity:", res.error || "Unknown error");
                        toastr.error("Error updating cart. Please try again.");
                    }
                } catch (e) {
                    console.error("Invalid response:", resp);
                    toastr.error("An error occurred. Please try again later.");
                }
            },
            error: function(err) {
                console.error("AJAX Error:", err);
                toastr.error("A network error occurred. Please check your connection.");
            }
        });
    }

    $('.qty-minus').click(function() {
        var qty = $(this).parent().siblings('input[name="qty"]').val();
        if (qty > 1) {
            update_qty(parseInt(qty) - 1, $(this).attr('data-id'));
            $(this).parent().siblings('input[name="qty"]').val(parseInt(qty) - 1);
        }
        // No need for location.reload() here. The update_qty function updates the values.
    });

    $('.qty-plus').click(function() {
        var input = $(this).parent().siblings('input[name="qty"]');
        var qty = parseInt(input.val()) + 1;
        input.val(qty);
        update_qty(qty, $(this).attr('data-id'));
        // No need for location.reload() here. The update_qty function updates the values.
    });


function load_cart() {
    $.ajax({
        url: 'admin/ajax.php?action=get_cart_total',
        method: "GET",
        success: function(resp) {
            try {
                let res = JSON.parse(resp);
                if (res.success) {
                    // Update total amount
                    $('.card-body p.text-right b').text(res.total.toFixed(2));
                } else {
                    console.error("Error fetching cart total:", res.error || "Unknown error");
                }
            } catch (e) {
                console.error("Invalid response:", resp);
            }
        },
        error: function(err) {
            console.error("AJAX Error:", err);
        }
    });
}

$('.rem_cart').click(function() {
        var id = $(this).attr('data-id');

        $('#confirmDeleteModal').modal('show');

        $('#confirmDeleteBtn').off('click').on('click', function() {
            $('#confirmDeleteModal').modal('hide');
            $.ajax({
                url: 'admin/ajax.php?action=remove_from_cart',
                method: 'POST',
                data: {
                    id: id
                },
                dataType: 'json',
                success: function(resp) {
                if (resp.success) {
                    toastr.success("Item removed from cart.");

                    // ***DELAYED RELOAD***
                    setTimeout(function() {
                        location.reload();
                    }, 700); // Delay of 3 seconds (adjust as needed)

                } else {
                        console.error("Error removing item:", resp.error);
                        toastr.error("Error removing item. Please try again.");
                    }
                },
                error: function(jqXHR, textStatus, errorThrown) {
                    console.error("AJAX Error:", textStatus, errorThrown);
                    toastr.error("A network error occurred.");
                }
            });
        });
    });

    $('#checkout').click(function() {
        if ('<?php echo isset($_SESSION['login_user_id']) ?>' == 1) {
            location.replace("index.php?page=checkout");
        } else {
            uni_modal("Checkout", "login.php?page=checkout");
        }
    });
 </script>