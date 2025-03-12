<div class="container-fluid">
    <div class="row">
        <div class="col-lg-12">
        </div>
    </div>

    <div class="w3-main" style="margin-left:300px;margin-top:43px;">

        <header class="w3-container" style="padding-top:22px">
            <h5><b><i class="fa fa-dashboard"></i> Orders Dashboard</b></h5>
        </header>

        <div class="w3-row-padding w3-margin-bottom">
            <div class="w3-quarter">
                <a href="index.php?page=orders&s=0" style="text-decoration: none;">
                    <div class="w3-container w3-red w3-padding-16">
                        <div class="w3-left"><i class="fa fa-comment w3-xxxlarge"></i></div>
                        <div class="w3-right">
                            <h1><span id="pending_count">0</span></h1>
                        </div>
                        <div class="w3-clear"></div>
                        <h4>Pending</h4>
                    </div>
                </a>
            </div>

            <div class="w3-quarter">
                <a href="index.php?page=orders&s=1" style="text-decoration: none;">
                    <div class="w3-container w3-blue w3-padding-16">
                        <div class="w3-left"><i class="fa fa-eye w3-xxxlarge"></i></div>
                        <div class="w3-right">
                            <h3><span id="confirmed_count">0</span></h3>
                        </div>
                        <div class="w3-clear"></div>
                        <h4>Confirmed</h4>
                    </div>
                </a>
            </div>

            <div class="w3-quarter">
                <a href="index.php?page=orders&s=3" style="text-decoration: none;">
                    <div class="w3-container w3-orange w3-padding-16">
                        <div class="w3-left"><i class="fa fa-times w3-xxxlarge"></i></div>
                        <div class="w3-right">
                            <h3><span id="rejected_count">0</span></h3>
                        </div>
                        <div class="w3-clear"></div>
                        <h4>Rejected</h4>
                    </div>
                </a>
            </div>

            <div class="w3-quarter">
                <a href="index.php?page=orders" style="text-decoration: none;">
                    <div class="w3-container w3-teal w3-padding-16">
                        <div class="w3-left"><i class="fa fa-share-alt w3-xxxlarge"></i></div>
                        <div class="w3-right">
                            <h3><span id="total_orders">0</span></h3>
                        </div>
                        <div class="w3-clear"></div>
                        <h4>Total Orders</h4>
                    </div>
                </a>
            </div>
        </div>
    </div>
</div>

<script>
function fetchOrderCounts() {
    fetch('ajax.php?action=count_today_orders', { method: 'GET' })
        .then(response => response.text()) // Get raw response first
        .then(text => {
            console.log("Raw Response from PHP:", text); // Debugging
            
            try {
                let data = JSON.parse(text); // Convert to JSON
                console.log("Parsed Data:", data);

                // Ensure data is in the expected format
                if (typeof data === 'object' && data !== null) {
                    // Update the span elements with received values
                    document.getElementById('pending_count').textContent = data.pending ?? 0;
                    document.getElementById('confirmed_count').textContent = data.confirmed ?? 0;
                    document.getElementById('rejected_count').textContent = data.rejected ?? 0;
                    document.getElementById('total_orders').textContent = data.total ?? 0;
                } else {
                    console.error("Invalid data format received:", data);
                }
            } catch (error) {
                console.error("Error parsing JSON:", error);
            }
        })
        .catch(error => console.error("Fetch error:", error));
}

// Call the function when the page loads
document.addEventListener('DOMContentLoaded', fetchOrderCounts);


document.addEventListener("DOMContentLoaded", function() {
    setTimeout(fetchOrderCounts, 500); // Delay to ensure DOM is ready
    setInterval(fetchOrderCounts, 5000);
});

</script>