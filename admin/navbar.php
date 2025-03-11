<link rel="stylesheet" href="https://www.w3schools.com/w3css/4/w3.css">
<link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Raleway">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
<style>
    html,body,h1,h2,h3,h4,h5 {font-family: "Raleway", sans-serif}
</style>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
<link rel="stylesheet" href="https://cdn.rawgit.com/jqte/jqte/gh-pages/dist/jqte.css">
<div class="w3-bar w3-top w3-black w3-large" style="z-index:4">
    <button class="w3-bar-item w3-button w3-hide-large w3-hover-none w3-hover-text-light-grey" onclick="w3_open();"><i class="fa fa-bars"></i> Menu</button>
    <span class="w3-bar-item w3-right">FIFI's Cuisine</span>
</div>
<nav class="w3-sidebar w3-collapse w3-white w3-animate-left" style="z-index:3;width:300px;" id="mySidebar"><br>
    <div class="w3-container w3-row">
        <div class="w3-col s4">
        </div>
        <p></p>
        <div class="w3-col s8 w3-bar">
            <span><strong><?php echo "Welcome back ".$_SESSION['login']."!" ?></strong></span>
            <span><strong><a href="ajax.php?action=logout" class="w3-bar-item w3-button"><i class="fa fa-power-off"></i> Logout</a></strong></span>
        </div>
    </div>
    <hr>
    <div class="w3-bar-block">
        <a href="index.php?page=home" class="w3-bar-item w3-button w3-padding w3-blue"><i class="fa fa-home fa-fw"></i> Home</a>
        <a href="index.php?page=orders" class="w3-bar-item w3-button w3-padding"><i class="fa fa-list fa-fw"></i> Orders</a>
        <a href="index.php?page=menu" class="w3-bar-item w3-button w3-padding"><i class="fa fa-cutlery fa-fw"></i> Menu</a>
        <a href="index.php?page=meat_options" class="w3-bar-item w3-button w3-padding"><i class="fa fa-list-ol fa-fw"></i> Meat Options</a>
        <a href="index.php?page=soup_options" class="w3-bar-item w3-button w3-padding"><i class="fa fa-list-ol fa-fw"></i> Soup Options</a>
        <a href="index.php?page=categories" class="w3-bar-item w3-button w3-padding"><i class="fa fa-list-ol fa-fw"></i> Category List</a>
        <?php if($_SESSION['login_type'] == 1): ?>
            <a href="index.php?page=user2" class="w3-bar-item w3-button w3-padding"><i class="fa fa-user fa-fw"></i> Users</a>
            <a href="index.php?page=site_settings" class="w3-bar-item w3-button w3-padding"><i class="fa fa-cog fa-fw"></i> Site Settings</a>
        <?php endif; ?>
        <a href="#" class="w3-bar-item w3-button w3-padding-16 w3-hide-large w3-dark-grey w3-hover-black" onclick="w3_close()" title="close menu"><i class="fa fa-remove fa-fw"></i> Close Menu</a>
    </div>
</nav>