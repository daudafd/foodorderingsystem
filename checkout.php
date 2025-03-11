<style>
.toast {
    background-color: #fff;
    border: 1px solid #ccc;
    border-radius: 5px;
    padding: 20px;
    box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
    position: fixed; 
    top: 50%; 
    left: 50%; 
    transform: translate(-50%, -50%); 
}
</style>

<header class="masthead">
    <div class="container h-100">
        <div class="row h-100 align-items-center justify-content-center text-center">
            <div class="col-lg-10 align-self-end mb-4 page-title">
                <h3 class="text-white">Checkout</h3>
                <hr class="divider my-4" />
            </div>
        </div>
    </div>
</header>
<div class="container">
    <div class="card">
        <div class="card-body">
        <form id="checkout-frm">
                <h4 class="mb-4">Confirm Delivery Information</h4>
                
                <div class="form-group">
                    <label for="first_name">Firstname</label>
                    <input type="text" id="first_name" name="first_name" required class="form-control" 
                           value="<?php echo htmlspecialchars($_SESSION['login_first_name'] ?? '', ENT_QUOTES, 'UTF-8'); ?>">
                </div>
                
                <div class="form-group">
                    <label for="last_name">Last Name</label>
                    <input type="text" id="last_name" name="last_name" required class="form-control" 
                           value="<?php echo htmlspecialchars($_SESSION['login_last_name'] ?? '', ENT_QUOTES, 'UTF-8'); ?>">
                </div>
                
                <div class="form-group">
                    <label for="mobile">Contact</label>
                    <input type="text" id="mobile" name="mobile" required class="form-control" 
                           placeholder="Enter your number"
                           value="<?php echo htmlspecialchars($_SESSION['login_mobile'] ?? '', ENT_QUOTES, 'UTF-8'); ?>">
                </div>
                
                <div class="form-group">
                    <label for="address">Address</label>
                    <input type="text" id="address" name="address" required class="form-control"
                           placeholder="Enter your address"
                           value="<?php echo htmlspecialchars($_SESSION['login_address'] ?? '', ENT_QUOTES, 'UTF-8'); ?>">
                </div>
                
                <div class="form-group">
                    <label for="email">Email</label>
                    <input type="email" id="email" name="email" required class="form-control" 
                           value="<?php echo htmlspecialchars($_SESSION['login_email'] ?? '', ENT_QUOTES, 'UTF-8'); ?>">
                </div>
                
                <div class="form-group">
                    <label for="delivery_option">Delivery Option</label>
                    <select id="delivery_option" name="delivery_option" class="form-control">
                        <option value="0">Self Pickup (Free)</option>
                        <option value="1100">Alagbaka + Takeaway (+₦1100)</option>
                        <option value="1300">Ijoka + Takeaway (+₦1300)</option>
                        <option value="1100">Oba-Ile + Takeaway  (+₦1100)</option>
                        <option value="1500">Futa + Takeaway (+₦1500)</option>
                    </select>
                </div>
                
                <div class="form-group">
                    <label for="total_amount">Total Amount</label>
                    <input type="text" id="total_amount" readonly class="form-control" 
                           value="₦<?php echo number_format($_SESSION['total_amount'] ?? 0, 2); ?>">
                </div>
                <div class="row">
                    <div class="col text-center">
                        <button type="button" class="btn btn-block btn-primary" id="transfer">Make Transfer</button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>
<div class="modal fade" id="transferModal" tabindex="-1" role="dialog" aria-labelledby="transferModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="transferModalLabel">Bank Transfer</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <p>Please make your transfer to the following account details:</p>
                <ul>
                    <li><strong>Account Name:</strong> Fisayo Dauda</li>
                    <li><strong>Bank:</strong> Opay</li>
                    <li><strong>Account Number:</strong> 7062319778</li>
                </ul>
                <p><strong>Total Amount:</strong> ₦<span id="modalTotalAmount"></span></p>
                <p>After making the transfer, click the button below to confirm your payment.</p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                <button type="button" class="btn btn-primary" id="confirmTransfer">I have made payment</button>
            </div>
        </div>
    </div>
</div>
<script src="//cdn.jsdelivr.net/npm/sweetalert2@11"></script> 

<script>
document.addEventListener('DOMContentLoaded', function () {
    const deliveryOption = document.getElementById('delivery_option');
    const totalAmountField = document.getElementById('total_amount');
    const transferButton = document.getElementById('transfer');
    const transferModal = new bootstrap.Modal(document.getElementById('transferModal'));
    const modalTotalAmount = document.getElementById('modalTotalAmount');
    const confirmTransferButton = document.getElementById('confirmTransfer');
    let baseTotal = <?php echo $_SESSION['total_amount'] ?? 0; ?>;

    function updateTotal() {
        let deliveryCharge = parseFloat(deliveryOption.value) || 0;
        let newTotal = baseTotal + deliveryCharge;
        totalAmountField.value = '₦' + newTotal.toFixed(2);
        modalTotalAmount.textContent = newTotal.toFixed(2);
    }

    function handleTransfer() {
        transferModal.show();
    }

function confirmTransfer() {
    const orderData = {
        first_name: document.getElementById('first_name').value,
        last_name: document.getElementById('last_name').value,
        mobile: document.getElementById('mobile').value,
        address: document.getElementById('address').value,
        email: document.getElementById('email').value,
        delivery_charge: deliveryOption.value,
        payment_reference: 'transfer'
    };

    // Disable the button immediately
    confirmTransferButton.disabled = true;
    confirmTransferButton.innerHTML = "Processing..."; // Optional: Change button text

    $.ajax({
        url: 'admin/ajax.php?action=save_order',
        type: 'POST',
        data: orderData,
        success: function(response) {
            let res = JSON.parse(response);
            if (res.success) {
                Swal.fire({
                    icon: 'success',
                    title: 'Success!',
                    text: res.success,
                    showConfirmButton: false,
                    timer: 1500
                }).then(function() {
                    window.location.href = 'index.php?page=order';
                });
            } else if (res.error) {
                Swal.fire({
                    icon: 'error',
                    title: 'Error!',
                    text: res.error,
                }).then(function(){
                  confirmTransferButton.disabled = false;
                  confirmTransferButton.innerHTML = "I have made transfer";
                });
            }
        },
        error: function(xhr, status, error) {
            Swal.fire({
                icon: 'error',
                title: 'Error!',
                text: 'An unexpected error occurred. Please try again.',
            }).then(function(){
              confirmTransferButton.disabled = false;
              confirmTransferButton.innerHTML = "I have made transfer";
            });
            console.error("Error details:", xhr.responseText);
        },
        complete: function(){
          //add a complete function to reenable the button if there is a timeout or other error.
        }
    });

    transferModal.hide();
}

    deliveryOption.addEventListener('change', updateTotal);
    transferButton.addEventListener('click', handleTransfer);
    confirmTransferButton.addEventListener('click', confirmTransfer);

    updateTotal();
});
</script>