<?php
// session_start()
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
?>
<style>
    /* Set fixed size for cards */
.card.menu-item {
  height: 350px; /* Fixed height */
  width: 100%; /* Adjust to column width */
  display: flex;
  flex-direction: column;
  justify-content: space-between;
}

/* Ensure the image fits */
.card.menu-item .card-img-top {
  height: 150px; /* Fixed height for images */
  object-fit: cover; /* Crop or scale the image to fit */
  border-radius: 0; /* Optional: Remove any rounded corners */
}

/* Truncate text */
.card.menu-item .truncate {
  white-space: nowrap; /* Prevent wrapping */
  overflow: hidden; /* Hide overflow */
  text-overflow: ellipsis; /* Add "..." for overflowing text */
}

/* Ensure buttons align */
.card.menu-item .text-center {
  margin-top: auto; /* Push buttons to the bottom */
}

</style>
 
 <!-- Masthead-->
        <header class="masthead">
            <div class="container h-100">
                <div class="row h-100 align-items-center justify-content-center text-center">
                    <div class="col-lg-10 align-self-end mb-4 page-title">
                    	<h3 class="text-white">Welcome to <?php echo $setting_name; ?></h3>
                        <hr class="divider my-4" />
                        <a class="btn btn-primary btn-xl js-scroll-trigger" href="#menu">Order Now</a>
                    </div>
                </div>
            </div>
        </header>
        <section class="page-section" id="menu">
    <div id="menu-field" class="card-deck">
        <?php
        include 'admin/db_connect.php';

        // Fetch available items (status = 1)
        $qry_available = $conn->prepare("SELECT * FROM product_list WHERE status = :status ORDER BY rand()");
        $qry_available->execute([':status' => 1]);

        while ($row_available = $qry_available->fetch(PDO::FETCH_ASSOC)):
            ?>
            <div class="col-lg-3">
                <div class="card menu-item ">
                    <img src="assets/img/<?php echo $row_available['img_path'] ?>" class="card-img-top" alt="...">
                    <div class="card-body">
                        <h5 class="card-title"><?php echo $row_available['name'] ?></h5>
                        <p class="card-text text-wrap" style="font-size: 12px;"><?php echo $row_available['description'] ?></p>
                        <p class="card-text truncate">N<?php echo $row_available['price'] ?></p>
                        <div class="text-center">
                            <button class="btn btn-sm btn-outline-primary view_prod btn-block" data-id=<?php echo $row_available['id'] ?>><i class="fa fa-eye"></i> View</button>
                        </div>
                    </div>
                </div>
            </div>
        <?php endwhile; ?>

        <?php
        // Fetch unavailable items (status = 0)
        $qry_unavailable = $conn->prepare("SELECT * FROM product_list WHERE status = :status ORDER BY rand()");
        $qry_unavailable->execute([':status' => 0]);

        if ($qry_unavailable->rowCount() > 0): // Only display "Not Available" if there are items
            while ($row_unavailable = $qry_unavailable->fetch(PDO::FETCH_ASSOC)):
                ?>
                <div class="col-lg-3">
                    <div class="card menu-item ">
                        <img src="assets/img/<?php echo $row_unavailable['img_path'] ?>" class="card-img-top" alt="...">
                        <div class="card-body">
                            <h5 class="card-title"><?php echo $row_unavailable['name'] ?></h5>
                            <p class="card-text text-wrap" style="font-size: 12px;"><?php echo $row_unavailable['description'] ?></p>
                            <p class="card-text truncate">#<?php echo $row_unavailable['price'] ?>.00</p>
                            <div class="text-center">
                                <button class="btn btn-sm btn-outline-secondary disabled btn-block"><i class="fa fa-ban"></i> Not Available</button>
                            </div>
                        </div>
                    </div>
                </div>
            <?php endwhile;
        endif; ?>
    </div>
</section>
    <script>
        
      $('.view_prod').click(function(){
        uni_modal_right('Product','view_prod.php?id='+$(this).attr('data-id'))
      })

    const images = <?php echo $banner_images_json; ?>; // Get images from PHP
    let currentImageIndex = 0;
    const header = document.querySelector('header.masthead');

    function changeBackground() {
        header.style.backgroundImage = `linear-gradient(to bottom, rgb(0 0 0 / 40%) 0%, rgb(245 242 240 / 45%) 100%), url(assets/img/${images[currentImageIndex]})`;
        currentImageIndex = (currentImageIndex + 1) % images.length;
    }

    changeBackground(); // Set initial background
    setInterval(changeBackground, 5000); // Change every 5 seconds
</script>