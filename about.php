<header class="masthead">
    <div class="container h-100">
        <div class="row h-100 align-items-center justify-content-center text-center">
            <div class="col-lg-10 align-self-end mb-4" style="background: #0000002e;">
                <h1 class="text-uppercase text-white font-weight-bold">About Us</h1>
                <hr class="divider my-4" />
            </div>
        </div>
    </div>
</header>

<?php
include('admin/db_connect.php');

// Ensure database connection uses UTF-8
$conn->exec("SET NAMES 'utf8mb4'");

// Get system settings
$qry = $conn->prepare("SELECT * FROM system_settings LIMIT 1");
$qry->execute();
$meta = [];
$result = $qry->fetch(PDO::FETCH_ASSOC);

if ($result) {
    $setting_name = $result['name'];
    $setting_content = $result['about_content'];
}
?>

<section class="page-section">
    <div class="container">
        <?php echo $setting_content; ?>
    </div>
</section>