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
                        <option value="1200">Home Delivery + Include Takeaway Plastic (+₦1200)</option>
                    </select>
                </div>
                
                <div class="form-group">
                    <label for="total_amount">Total Amount</label>
                    <input type="text" id="total_amount" readonly class="form-control" 
                           value="₦<?php echo number_format($_SESSION['total_amount'] ?? 0, 2); ?>">
                </div>

                <div class="row">
                    <div class="col text-center">
                        <button type="button" class="btn btn-secondary" id="proceedToPayment">Card Payment</button>
                    </div>
                    <div class="col text-center">
                        <button type="button" class="btn btn-block btn-primary" id="transfer">Make Transfer</button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal for Bank Transfer -->
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

<script src="https://js.paystack.co/v1/inline.js"></script>
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


document.addEventListener('DOMContentLoaded', function () {
    const deliveryOption = document.getElementById('delivery_option');
    const totalAmountField = document.getElementById('total_amount');
    const proceedToPaymentButton = document.getElementById('proceedToPayment');
    const transferButton = document.getElementById('transfer');
    const transferModal = new bootstrap.Modal(document.getElementById('transferModal'));
    const modalTotalAmount = document.getElementById('modalTotalAmount');
    const confirmTransferButton = document.getElementById('confirmTransfer');
    const checkoutForm = document.getElementById('checkout-frm');

    let baseTotal = <?php echo $_SESSION['total_amount'] ?? 0; ?>;

    // Update total amount based on delivery option
    function updateTotal() {
        let deliveryCharge = parseFloat(deliveryOption.value) || 0;
        let newTotal = baseTotal + deliveryCharge;
        totalAmountField.value = newTotal.toFixed(2);
        modalTotalAmount.textContent = newTotal.toFixed(2); // Update modal with the total amount
    }

    // Validate form fields
    function isFormValid() {
        let isValid = true;
        const requiredFields = checkoutForm.querySelectorAll('input[required]');
        requiredFields.forEach(field => {
            if (!field.value.trim()) {
                isValid = false;
            }
        });
        return isValid;
    }

    // Toggle buttons based on form validation
    function toggleButtons() {
        const isValid = isFormValid();
        proceedToPaymentButton.disabled = !isValid;
        transferButton.disabled = !isValid;
    }

    // Handle Paystack payment
    function handlePayment() {
        if (!isFormValid()) {
            alert("Please fill out all fields before proceeding.");
            return;
        }

        const orderDetails = {
            email: "<?php echo $_SESSION['login_email']; ?>",
            amount: (baseTotal + parseFloat(deliveryOption.value || 0)) * 100, // Amount in kobo
            reference: "PS" + Math.floor(Math.random() * 1000000000),
            name: "<?php echo $_SESSION['login_first_name'] . ' ' . $_SESSION['login_last_name']; ?>",
            phone: "<?php echo $_SESSION['login_mobile']; ?>"
        };

        // console.log("Generated Payment Reference:", orderDetails.reference);

        // Validate amount
        if (orderDetails.amount <= 0) {
            alert("Total amount is invalid. Please check your order.");
            return;
        }

        // Initialize Paystack
        const handler = PaystackPop.setup({
            key: 'pk_test_2e511fd2fd5ccbf4f54a1d85b0217526c2ad6eff',
            email: orderDetails.email,
            amount: orderDetails.amount,
            currency: "NGN",
            ref: orderDetails.reference,
            metadata: {
                custom_fields: [
                    { display_name: "Name", variable_name: "name", value: orderDetails.name },
                    { display_name: "Phone Number", variable_name: "phone_number", value: orderDetails.phone }
                ]
            },
            callback: function(response) {
                // console.log("Payment Reference from Paystack:", response.reference);
                saveOrder(response.reference);
                toastr.success("Ordered successfully!");
            },
            onClose: function() {
                toastr.error("Transaction was not completed. Please try again..");
            }
        });

        handler.openIframe();
    }

    // Handle Bank Transfer
    function handleTransfer() {
        if (!isFormValid()) {
            alert("Please fill out all fields before proceeding.");
            return;
        }
        transferModal.show(); // Show the bank transfer modal
    }

    // Confirm Bank Transfer
    function confirmTransfer() {
        if (!isFormValid()) {
            alert("Please fill out all fields before confirming payment.");
            return;
        }

        const orderData = {
            first_name: document.getElementById('first_name').value,
            last_name: document.getElementById('last_name').value,
            mobile: document.getElementById('mobile').value,
            address: document.getElementById('address').value,
            email: document.getElementById('email').value,
            delivery_charge: deliveryOption.value,
            payment_reference: 'transfer' // Indicate bank transfer
        };

        console.log("Sending bank transfer order data:", orderData);

        // Make AJAX request to save the order
        $.ajax({
            url: 'admin/ajax.php?action=save_order',
            type: 'POST',
            data: orderData,
            success: function(response) {
                let res = JSON.parse(response);
                if (res.success) {
                    toastr.success("Ordered successfully!");
                    window.location.href = 'index.php?page=order';
                } else if (res.error) {
                    alert(res.error);
                }
            },
            error: function(xhr, status, error) {
                toastr.error("An unexpected error occurred. Please try again..");
                // console.error("Error details:", xhr.responseText);
            }
        });

        transferModal.hide(); // Hide modal after confirmation
    }

    // Event listeners
    deliveryOption.addEventListener('change', updateTotal);
    checkoutForm.addEventListener('input', toggleButtons); // Update buttons on input
    proceedToPaymentButton.addEventListener('click', function(e) {
        e.preventDefault(); // Prevent default button behavior
        handlePayment();
    });
    transferButton.addEventListener('click', handleTransfer);
    confirmTransferButton.addEventListener('click', confirmTransfer);

    // Initial setup
    updateTotal();
    toggleButtons(); // Disable buttons initially if fields are empty
});

</script>