<?php 
session_start();
include('includes/config.php');
error_reporting(0);
?>

<!DOCTYPE HTML>
<html lang="en">
<head>

<title>INTELLIGENT AGRICULTURAL ASSISTANCE PLATFORM</title>
<!--Bootstrap -->
<link rel="stylesheet" href="assets/css/bootstrap.min.css" type="text/css">
<link rel="stylesheet" href="assets/css/owl.carousel.css" type="text/css">
<link rel="stylesheet" href="assets/css/owl.transitions.css" type="text/css">
<link href="assets/css/slick.css" rel="stylesheet">
<link href="assets/css/bootstrap-slider.min.css" rel="stylesheet">
<link href="assets/css/font-awesome.min.css" rel="stylesheet">
		<link rel="stylesheet" id="switcher-css" type="text/css" href="assets/switcher/css/switcher.css" media="all" />
		<link rel="alternate stylesheet" type="text/css" href="assets/switcher/css/red.css" title="red" media="all" data-default-color="true" />
		<link rel="alternate stylesheet" type="text/css" href="assets/switcher/css/orange.css" title="orange" media="all" />
		<link rel="alternate stylesheet" type="text/css" href="assets/switcher/css/blue.css" title="blue" media="all" />
		<link rel="alternate stylesheet" type="text/css" href="assets/switcher/css/pink.css" title="pink" media="all" />
		<link rel="alternate stylesheet" type="text/css" href="assets/switcher/css/green.css" title="green" media="all" />
		<link rel="alternate stylesheet" type="text/css" href="assets/switcher/css/purple.css" title="purple" media="all" />
<link rel="apple-touch-icon-precomposed" sizes="144x144" href="assets/images/favicon-icon/apple-touch-icon-144-precomposed.png">
<link rel="shortcut icon" href="logo1.jpg">
<link href="https://fonts.googleapis.com/css?family=Lato:300,400,700,900" rel="stylesheet"> 
</head>
<body>
<!--Header-->
<?php include('includes/header.php');?>
<!-- /Header --> 
<!-- Video Section (80%) -->
<section class="video-section">
  <video autoplay muted loop playsinline class="background-video">
    <source src="assets/images/home.mp4" type="video/mp4">
    Your browser does not support the video tag.
  </video>
</section>

<!-- Banner Section (20%) -->
<section class="banner-section">
  <div class="banner-content">
    <div class="text-box">
      <h1><span>Guteza Imbere Abahinzi binyuze mu Kumenya Indwara z’Ibiribwa</span></h1>
      <p>Dufasha abahinzi bo mu cyaro kumenya indwara z’ibihingwa hakoreshejwe ikoranabuhanga ryo gusuzuma amashusho — twuzuza icyuho aho nta bajyanama b’ubuhinzi cyangwa ubufasha bwa FAO buhari.</p>
    </div>
  </div>
</section>

<!-- /Banners --> 

<!-- Resent Cat-->
<section class="section-padding gray-bg">
  <div class="container">
    <div class="section-header text-center">
           <div id="report-header">
                            <img src="../logo1.jpg" alt="Logo" style="width: 200px; height:50px;">
                            <h2 class="page-title">Diseases Affected Report</h2>
                            <?php if (!empty($from_date) && !empty($to_date)): ?>
                                <p><strong>Period:</strong> <?php echo date('F j, Y', strtotime($from_date)); ?> to <?php echo date('F j, Y', strtotime($to_date)); ?></p>
                            <?php endif; ?>
                            <?php if (!empty($selected_region)): ?>
                                <p><strong>Region:</strong> <?php echo $selected_region; ?></p>
                            <?php endif; ?>
                        </div>
    </div>
    <div class="row"> 
      
      <!-- Nav tabs -->
      <div class="recent-tab">
        <ul class="nav nav-tabs" role="tablist">
          <li role="presentation" class="active"><a href="#resentnewcar" role="tab" data-toggle="tab">Ibibazo mu buhinzi</a></li>
        </ul>
      </div>
      <!-- Recently Listed New Cars -->
      <div class="tab-content">
        <div role="tabpanel" class="tab-pane active" id="resentnewcar">


        <div class="container shadow p-4 mb-5 bg-white rounded" style="box-shadow: 0 4px 20px rgba(0,0,0,0.1);">
  <div class="row">
    <?php 
    $sql = "SELECT tbldeseases.deseaseTitle, tblcategory.categoryName, tbldeseases.desease_prevention, tbldeseases.id, tbldeseases.deseaseOverview, tbldeseases.Vimage1
            FROM tbldeseases
            JOIN tblcategory ON tblcategory.id = tbldeseases.deseaseCategory
            ORDER BY tblcategory.categoryName ASC";
    $query = $dbh->prepare($sql);
    $query->execute();
    $results = $query->fetchAll(PDO::FETCH_OBJ);
    
    if ($query->rowCount() > 0) {
        foreach ($results as $result) {
    ?>
      <div class="col-lg-4 col-md-4 col-sm-6 col-12 mb-4 d-flex">
        <div class="card shadow-sm h-100 d-flex flex-column" style="border-radius: 15px; overflow: hidden; width: 100%;">
          <!-- Image -->
          <div style="width: 100%; height: 200px; overflow: hidden;">
            <a href="desease-details.php?vhid=<?php echo htmlentities($result->id); ?>">
              <img src="admin/img/deseaseimages/<?php echo htmlentities($result->Vimage1); ?>"
                    alt="image"
                   class="img-fluid"
                   style="width: 100%; height: 100%; object-fit: cover;">
            </a>
          </div>
          
          <!-- Body -->
          <div class="card-body d-flex flex-column justify-content-between" style="padding: 1rem;">
            <div>
              <h6 class="card-title mb-1" style="font-weight: 600; color: #2d3436;">
                <a href="desease-details.php?vhid=<?php echo htmlentities($result->id); ?>"
                    style="text-decoration: none; color: inherit;">
                  <?php echo htmlentities($result->deseaseTitle); ?>
                </a>
              </h6>
              <!-- Category badge -->
              <span class="badge mb-2"
                     style="background-color: #00b894; color: white; font-size: 12px; padding: 5px 10px; border-radius: 12px;">
                <?php echo htmlentities($result->categoryName); ?>
              </span>
              
              <!-- Overview -->
              <p style="font-size: 13px; color: #636e72;">
                <?php echo substr($result->deseaseOverview, 0, 60); ?>...
              </p>
            </div>
            
            <!-- Button -->
            <a href="desease-details.php?vhid=<?php echo htmlentities($result->id); ?>"
                class="btn btn-sm mt-2 align-self-start"
                style="background-color: #168519ff; color: white; border-radius: 20px; padding: 5px 12px;">
              Gusoma byinshi
            </a>
          </div>
        </div>
      </div>
    <?php
        }
    } else {
        echo "<div class='col-12'><p class='text-center'>No diseases found.</p></div>";
    }
    ?>
  </div>
</div>

<!-- Additional CSS for better 3-card layout -->
<style>
/* Ensure consistent card heights in 3-card layout */
@media (min-width: 992px) {
  .col-lg-4 {
    max-width: 33.333333%;
  }
}

@media (min-width: 768px) {
  .col-md-4 {
    max-width: 33.333333%;
  }
}

/* Ensure cards fill the container properly */
.card {
  min-height: 350px;
}

/* Responsive adjustments for smaller screens */
@media (max-width: 767px) {
  .col-sm-6 {
    max-width: 50%;
  }
}

@media (max-width: 575px) {
  .col-12 {
    max-width: 50%;
  }
}

/* Hover effects for better interaction */
.card:hover {
  transform: translateY(-5px);
  box-shadow: 0 8px 25px rgba(0,0,0,0.15);
  transition: all 0.3s ease;
}

.card img:hover {
  transform: scale(1.05);
  transition: transform 0.3s ease;
}

.btn:hover {
  background-color: #111512ff !important;
  transform: translateY(-2px);
  transition: all 0.3s ease;
}

/* Ensure equal height cards */
.row {
  display: flex;
  flex-wrap: wrap;
}

.d-flex {
  display: flex !important;
}

.h-100 {
  height: 100% !important;
}

.flex-column {
  flex-direction: column !important;
}

.justify-content-between {
  justify-content: space-between !important;
}
</style>
      </div>
    </div>
  </div>
</section>
<!-- /Resent Cat --> 

<!--Footer -->
<?php include('includes/footer.php');?>
<!-- /Footer--> 

<!--Back to top-->
<div id="back-top" class="back-top"> <a href="#top"><i class="fa fa-angle-up" aria-hidden="true"></i> </a> </div>
<!--/Back to top--> 

<!--Login-Form -->
<?php include('includes/login.php');?>
<!--/Login-Form --> 

<!--Register-Form -->
<?php include('includes/registration.php');?>

<!--/Register-Form --> 

<!--Forgot-password-Form -->
<?php include('includes/forgotpassword.php');?>
<!--/Forgot-password-Form --> 

<!-- Scripts --> 
<script src="assets/js/jquery.min.js"></script>
<script src="assets/js/bootstrap.min.js"></script> 
<script src="assets/js/interface.js"></script> 
<!--Switcher-->
<script src="assets/switcher/js/switcher.js"></script>
<!--bootstrap-slider-JS--> 
<script src="assets/js/bootstrap-slider.min.js"></script> 
<!--Slider-JS--> 
<script src="assets/js/slick.min.js"></script> 
<script src="assets/js/owl.carousel.min.js"></script>
<style>
@charset "utf-8";

body {
	font-family: 'Lato', sans-serif;
	margin:0px;
	padding:0px !important;
	overflow-x:hidden;
	color:#555555;
}
/*----------------------
	1.1. Typography
------------------------------*/

h1, h2, h3, h4, h5, h6 {
	color:#111111;
	font-weight:900;
	margin:0 auto 15px;
}
h1 {
	font-size: 50px;
}
h2 {
	font-size: 40px;
}
h3 {
	font-size: 28px;
}
h4 {
	font-size:22px;
}
h5 {
	font-size: 20px;
	line-height:32px;
}
h6 {
	font-size: 18px;
}
h1 span, h2 span, h3 span, h4 span, h5 span, h6 span {
	font-weight:300	
}
p {
	font-size:16px;
	line-height:26px;
	font-weight:400;
	margin-bottom:15px;
}
a {
	transition-duration:0.5s;
	 -moz-transition-duration:0.5s;
	  -o-transition-duration:0.5s;
	   -webkit-transition-duration:0.5s;
	    -ms-transition-duration:0.5s;
	text-decoration:none;
}
a:hover, a:focus {
	text-decoration:none;
	outline:none;
}
ul, ol {
	margin:0 auto 20px;
}
ul li, ol li {
  font-size: 16px;
  line-height: 26px;
  margin: 0 auto 10px;
}
ul li i, ol li i {
	margin:0 12px 0 0;
}
ul.list_style_none, ol.list_style_none {
	list-style:none;
}
ul.list-with-icon {
	padding-left:10px;
	margin-bottom:30px;
}
ul.list-with-icon li {
  line-height: 20px;
  margin-bottom: 15px;
  padding-left: 23px;
  position: relative;
  list-style:none;
}
ul.list-with-icon li i {
  left: 0;
  position: absolute;
  top: 4px;
}

blockquote {
	background:#f5f5f5 ;	
}
.white-text, 
.white-text h1, 
.white-text h2, 
.white-text h3, 
.white-text h4, 
.white-text h5, 
.white-text h6, 
.white-text p {
	color:#fff;	
}
.uppercase {
	text-transform:uppercase;
}
.underline {
	text-decoration:underline;
}
.divider {
  border-bottom: 1px solid #dcd9d9;
  clear: both;
  margin: 40px auto;
}
/*-----------------
	1.2. Button
-------------------------*/
.btn {
  border: medium none;
  border-radius: 3px;
  color: #ffffff;
  font-size: 16px;
  font-weight: 800;
  line-height: 30px;
  margin: auto;
  padding: 7px 36px;
  transition: all 0.3s linear 0s;
   -moz-transition: all 0.3s linear 0s;
    -o-transition: all 0.3s linear 0s;
     -webkit-transition: all 0.3s linear 0s;
      -ms-transition: all 0.3s linear 0s;
}
.btn .fa {
  font-size: 20px;
  margin-left: 5px;
  vertical-align: middle;
}
.btn.btn-lg {
	font-size: 28px;
	line-height: 35px;
	padding: 25px 83px;
}
.btn.btn-lg:hover {
	background:#c51514
}
.btn:hover, .btn:focus {
	color: #ffffff;
	outline:none;
}
.btn-link {
  font-weight: 800;
}
.btn.outline {
	background:none;
	border-style:solid;
	border-width:1px;	
}
.btn.outline:hover, .btn.outline:focus {
	color:#fff;
}
.btn.btn-xs {
	font-size:12px;
	padding:0px 25px;	
}
.btn.btn-xs .fa {
	margin:0 5px;
	font-size:14px;
}

.angle_arrow {
  background: #fff none repeat scroll 0 0;
  border-radius: 50%;
  display: inline-block;
  height: 22px;
  line-height: 20px;
  margin-left: 4px;
  text-align: center;
  vertical-align: text-top;
  width: 22px;
}
.btn .angle_arrow .fa {
  display: block;
  font-size: 18px;
  line-height: 22px;
  margin: 0;
  vertical-align: middle;
}
.btn.outline.active-btn {
	border-color:#089901;
	color:#089901;
}
.btn.outline.active-btn:hover {
	background:#089901;
	color:#fff;
}
a, 
.btn-link, 
.car-title-m h6 a:hover, 
.featured-car-content > h6 a:hover, 
.footer-top ul li a:hover, 
.get-intouch a:hover, 
.blog-content h5 a:hover, 
.blog-info-box li a:hover, 
.control-label span, 
.angle_arrow i, 
.contact_detail li a:hover, 
.team_more_info p a:hover, 
.error_text_m h2, 
.search_btn, 
.popular_post_title a:hover,
.categories_list ul li a:hover,
.categories_list ul li a:hover:after, 
.article_meta ul li a:hover, 
.articale_header h2 a:hover, 
.btn.outline, 
.share_article ul li, 
.contact-info a:hover, 
.social-follow a:hover, 
.radio input[type=radio]:checked + label:before, 
.checkbox input[type=checkbox]:checked + label:before, 
.product-listing-content h5 a:hover, 
.pricing_info .price, 
.text-primary, 
.footer_widget ul li a:hover, 
.header_search button:hover, 
.header_widgets a:hover, 
.navbar-default .navbar-nav > li.active a, 
.navbar-default .navbar-nav > li:focus a, 
.navbar-default .navbar-nav > li:hover a,  
.navbar-default .navbar-nav > .active > a:hover, 
.navbar-default .navbar-nav > .open > a, 
.navbar-default .navbar-nav > .open > a:focus, 
.navbar-default .navbar-nav > .open > a:hover,
.my_vehicles_list ul.vehicle_listing li a:hover, 
.dealer_contact_info a:hover, 
.widget_heading i, 
.dealers_listing .dealer_info h5 a:hover, 
.main_features ul li p, 
.listing_detail_head .price_info p, 
.listing_other_info button:hover, 
.compare_info table td i, .compare_info table th i,  
#accessories i, 
.price, 
.inventory_info_list ul li i, 
.services_info h4 a:hover, 
.about_info .icon_box, 
.header_style2 .navbar-nav > li > .dropdown-menu a:hover, 
.header_style2 .navbar-default .navbar-nav li:hover .dropdown-menu li a:hover, 
.header_style2 .dropdown-menu > .active > a, 
.header_style2 .dropdown-menu > .active > a:focus, 
.header_style2 .dropdown-menu > .active > a:hover, 
.header_style2 .dropdown-menu > li > a:focus, 
.header_style2 .dropdown-menu > li > a:hover {
	color:#fa2837;
	fill: #071507ff;
}

a:hover, a:focus, .btn-link:hover {
	color:#ff0012;
	fill: #0c0e0cff;
}

.btn, 
.nav-tabs > li.active > a, 
.nav-tabs > li.active > a:focus, 
.nav-tabs > li.active > a:hover, 
.recent-tab .nav.nav-tabs li.active a, 
.fun-facts-m, .featured-icon, 
.owl-pagination .owl-page.active,
#testimonial-slider .owl-pagination .owl-page.active, 
.social-follow.footer-social a:hover, 
.back-top a, 
.team_more_info ul li a:hover, 
.tag_list ul li a:hover, 
.pagination ul li.current, 
.pagination ul li:hover,
.btn.outline:hover, 
.btn.outline:focus, 
.share_article ul li:hover, 
.nav-tabs > li a:hover, 
.nav-tabs > li a:focus, 
.label-icon, 
.navbar-default .navbar-toggle .icon-bar, 
.navbar-default .navbar-toggle:focus, .navbar-default .navbar-toggle:hover, 
.label_icon, 
.navbar-nav > li > .dropdown-menu, 
.add_compare .checkbox, 
.search_other, 
.vs, 
.td_divider, 
.search_other_inventory, 
#other_info, 
.main_bg, 
.slider .slider-handle, .slider .slider-selection {
  background: #070707ff none repeat scroll 0 0;
  fill: #161b17ff;
}
.btn:hover, .btn:focus, 
.search_other:hover, 
#other_info:hover {
	background-color: #f2f5f2ff;
	fill: #e7f1e8ff;
}

.nav-tabs > li.active > a, 
.nav-tabs > li.active > a:focus, 
.nav-tabs > li.active > a:hover, 
.social-follow.footer-social a:hover, 
.page-header, 
.tag_list ul li a:hover, 
.btn.outline, 
.share_article ul li, 
blockquote, 
.social-follow a:hover, 
.radio label:before,  
.navbar-default .navbar-toggle, 
.owl-buttons div, 
.about_info .icon_box {
	border-color: #f1f6f2ff;
}

.recent-tab .nav.nav-tabs li.active::after {
	border-color: #f3f6f3ff rgba(0, 0, 0, 0) rgba(0, 0, 0, 0);
}
.td_divider:after {
	border-color: rgba(0, 0, 0, 0) rgba(0, 0, 0, 0 ) rgba(0, 0, 0, 0 ) #f0faf1ff ;
}

.navbar-nav > li > .dropdown-menu li {
  border-bottom: 1px solid #f4f8f5ff;
}

@media (max-width:767px) {
.navbar-default .navbar-nav .open .dropdown-menu > li > a:focus, .navbar-default .navbar-nav .open .dropdown-menu > li > a:hover {
	color:#fa2837;	
}
}
/*-------------------------
	1.3. dark-overlay
---------------------------------*/
.div_zindex {
  position: relative;
  z-index: 1;
}
.dark-overlay {
  background: rgba(0, 0, 0, 0.75) none repeat scroll 0 0;
  content: "";
  height: 100%;
  left: 0;
  position: absolute;
  top: 0;
  width: 100%;
}
/*-------------------------------------
	1.4. Space margins and padding
------------------------------------------------*/
.padding_none {
	padding:0px;
}
.padding {
	padding:20px 0;
}
.padding_50px {
	padding:50px 0;
}
.padding_4x4_30 {
	padding:30px;	
}
.padding_4x4_40 {
	padding:40px;	
}
.space-20 {
    width:100%;
    height: 20px;
	clear:both;
}
.space-30 {
    width:100%;
    height: 30px;
	clear:both;
}
.space-40 {
    width:100%;
    height: 40px;
	clear:both;
}
.space-60 {
    width:100%;
    height: 60px;
	clear:both;
}
.space-80 {
    height: 80px;
    width:100%;
}
.margin-btm-20 {
    margin-bottom: 20px;
	clear:both;
}
.margin-none {
	margin:0px;
}
/*------------------------
	1.10. Modal
--------------------------------*/
.modal-dialog {
  width: 650px;
}
.modal-content {
  padding: 0 32px 22px;
}
.modal-header {
	padding:15px 0;
	margin-bottom:25px;
}
.modal-body {
	padding:10px 0;
}
.modal .modal-header .close {
  background: #141212ff none repeat scroll 0 0;
  border-radius: 100%;
  color: #fff;
  font-size: 17px;
  height: 61px;
  line-height: 30px;
  margin-top: 8px;
  opacity: 1;
  text-align: center;
  text-shadow: none;
  width: 31px;
}


.nav-stacked.affix {
  background: #100f0fff none repeat scroll 0 0;
  /* top: 0; */
  width: 100%;
  z-index: 11;
  padding: 10px 0;
}

.navbar-default .navbar-toggle:focus .icon-bar, .navbar-default .navbar-toggle:hover .icon-bar {
	background:#000;
}
.header_info {
  float: right;
  text-align: right;
}
.social-follow {
  display: inline-block;
  margin-left: 20px;
  margin-top: 0;
  vertical-align: middle;
}
.social-follow ul {
	padding:0px;
	margin:0px;
}
.social-follow ul li {
	display:inline-block;
	vertical-align:middle;
	list-style:none;
	margin:0px;
}
.social-follow ul li a {
	display:block;
	font-size:25px;
	color:#555;
}
.social-follow li i {
  margin-right: 5px;
}
.login_btn {
  display: inline-block;
  margin-left: 10px;
  vertical-align: middle;
}
.circle_icon {
  border: 1px solid #000;
  border-radius: 50%;
  color: #555;
  float: left;
  font-size: 17px;
  height: 40px;
  line-height: 35px;
  margin-right: 10px;
  text-align: center;
  width: 40px;
}
.uppercase_text {
  color: #111;
  font-size: 15px;
  font-weight: 900;
  line-height: 17px;
  margin: 0;
  text-transform: uppercase;
}
.header_widgets {
  display: inline-block;
  width: 260px;
  text-align:left;
}
.header_widgets a {
	color:#555;
}
.header_wrap {
  float: right;
}
/* Make sure your header container uses Flexbox */
.header {
  display: flex;
  align-items: center;
  justify-content: space-between; /* Optional, for spacing */
  flex-wrap: nowrap; /* Prevent wrapping to next line */
}

/* Align search section properly */
.header_search {
  display: flex;
  align-items: center;
  padding: 0;
  position: relative;
  margin-left: auto; /* Push it to the right if needed */
}

/* Hide the triangle pseudo-element unless you want it visible */
#header-search-form::after {
  content: "";
  display: none;
  position: absolute;
  top: -13px;
  right: 9px;
  border-style: solid;
  border-width: 7px;
  border-color: transparent transparent #070707ff transparent;
}

.header_search input {
  background: #8ad153ff none repeat scroll 0 0;
  border: 0 none;
  height: 38px;
  padding-right: 45px;
  width: 227px;
}
.header_search button {
  background: rgba(0, 0, 0, 0) none repeat scroll 0 0;
  border: 0 none;
  color: #0e0d0dff;
  font-size: 14px;
  position: absolute;
  right: 8px;
  top: 50%;
  transform: translateY(-50%);
  -moz-transform: translateY(-50%);
  -o-transform: translateY(-50%);
  -webkit-transform: translateY(-50%);
  -ms-transform: translateY(-50%);
}
#search_toggle {
  border: 1px solid rgba(249, 244, 244, 0.9);
  border-radius: 2px;
  cursor: pointer;
  display:none;
  margin-top: 1px;
  padding: 5px 10px;
}
.user_login {
  border: 1px solid rgba(254, 248, 248, 0.9);
  border-radius: 3px;
  float: left;
  margin: 18px 10px 17px 0;
}
.user_login ul {
	padding:0px;
	margin:0px;
}
.user_login ul li {
  line-height: 17px;
  list-style: outside none none;
  margin: 0;
  padding: 4px 15px 7px;
}
.user_login > ul > li a {
	color:#fff;
	font-size:12px;
	font-weight:900;
	text-transform:uppercase;
}
.user_login > ul > li a i {
	margin:0 2px 0;
}
.user_login ul.dropdown-menu {
  background: #111111 none repeat scroll 0 0;
  border-radius: 0;
  padding: 15px 0;
  top: 107%;
}
/*----------------------------------
	Enhanced Agriculture Theme - Fixed Images & Cool Design
------------------------------------------*/

/* Global image fixes - prevent zoom issues */
img {
  max-width: 100%;
  height: auto;
  object-fit: cover;
  object-position: center;
  transform: scale(1) !important; /* Reset any inherited zoom */
  transition: all 0.3s ease;
}

/* Specific image container fixes */
.image-container, .car-image, .product-image, .gallery-image {
  overflow: hidden;
  border-radius: 15px;
  position: relative;
  background: #f8f9fa;
}

.image-container img, .car-image img, .product-image img, .gallery-image img {
  width: 100%;
  height: 100%;
  object-fit: cover; /* Covers container while maintaining aspect ratio */
  object-position: center;
  transform: scale(1);
  transition: transform 0.4s cubic-bezier(0.25, 0.46, 0.45, 0.94);
  filter: brightness(1) contrast(1.05) saturate(1.1);
}

/* Cool hover effects for images */
.image-container:hover img, .car-image:hover img, .product-image:hover img {
  transform: scale(1.08) rotate(1deg);
  filter: brightness(1.1) contrast(1.15) saturate(1.25);
  box-shadow: 0 15px 35px rgba(45, 80, 22, 0.2);
}

/* Alternative: Show full image without cropping */
.image-container.fit-full img {
  object-fit: contain; /* Shows entire image, may add letterboxing */
  background: rgba(6, 7, 6, 0.5);
}

/*----------------------------------
	Enhanced Navigation Bar
------------------------------------------*/
#navigation_bar {
  background: linear-gradient(135deg, 
    rgba(6, 4, 4, 0.95) 0%, 
    rgba(8, 9, 10, 0.9) 50%, 
    rgba(20, 16, 16, 0.95) 100%);
  border-radius: 0;
  padding: 20px 0;
  width: 100vw;
  max-width: none;
  margin: 0;
  box-shadow: 
    0 8px 32px rgba(0, 0, 0, 0.12), 
    0 0 40px rgba(144, 238, 144, 0.1) inset,
    0 1px 0 rgba(5, 4, 4, 0.8) inset;
  backdrop-filter: blur(20px) saturate(180%);
  border-bottom: 3px solid rgba(8, 8, 8, 0.4);
  position: fixed !important;
  top: 0 !important;
  left: 0 !important;
  right: 0 !important;
  z-index: 9999 !important;
  overflow: hidden;
  height: 100px;
  transition: all 0.3s ease;
}

/* Parallax floating elements */
#navigation_bar::before {
  content: '🌱 🌾 🚜 🌿 🌽 🥕 🍃';
  position: absolute;
  top: 50%;
  left: -50px;
  transform: translateY(-50%);
  font-size: 20px;
  opacity: 0.08;
  animation: float-elements 25s linear infinite;
  z-index: 0;
  white-space: nowrap;
}

/* Dynamic gradient wave */
#navigation_bar::after {
  content: '';
  position: absolute;
  top: 0;
  left: -100%;
  width: 100%;
  height: 100%;
  background: linear-gradient(90deg, 
    transparent, 
    rgba(144, 238, 144, 0.15), 
    rgba(106, 168, 79, 0.1),
    transparent);
  animation: wave-shimmer 6s infinite;
  z-index: 0;
}

@keyframes float-elements {
  0% { left: -200px; }
  100% { left: calc(100% + 200px); }
}

@keyframes wave-shimmer {
  0% { left: -100%; opacity: 0; }
  50% { opacity: 1; }
  100% { left: 100%; opacity: 0; }
}

.navbar-default .navbar-nav > li {
  margin: 0 15px 0 0;
  padding: 0;
  border-radius: 20px;
  transition: all 0.4s cubic-bezier(0.25, 0.46, 0.45, 0.94);
  position: relative;
  overflow: hidden;
  background-color: #000; /* solid black background */
  color: #fff; /* white text */
}

.navbar-default .navbar-nav > li a {
  color: white !important; /* ensure link text is white */
  font-size:100;
  padding: 10px 20px;
  display: block;
  text-decoration: none;
  font-weight: 500;
  letter-spacing: 0.5px;
}

.navbar-default .navbar-nav > li::before {
  content: '';
  position: absolute;
  top: 0;
  left: -100%;
  width: 100%;
  height: 100%;
  background: linear-gradient(90deg, 
    transparent, 
    rgba(255, 255, 255, 0.15), 
    rgba(255, 255, 255, 0.05),
    transparent);
  transition: left 0.8s cubic-bezier(0.25, 0.46, 0.45, 0.94);
  z-index: 0;
}

.navbar-default .navbar-nav > li:hover::before {
  left: 100%;
}

.navbar-default .navbar-nav > li:hover {
  transform: translateY(-5px) scale(1.05);
  box-shadow: 
    0 12px 35px rgba(0, 0, 0, 0.4),
    0 0 30px rgba(255, 255, 255, 0.1) inset;
  background: linear-gradient(145deg, 
    rgba(255, 255, 255, 0.05), 
    rgba(0, 0, 0, 0.5));
  border: 2px solid rgba(255, 255, 255, 0.1);
}

/*----------------------------------
	Enhanced Video Section
------------------------------------------*/
.video-section {
  height: 60vh;
  width: 100%;
  overflow: hidden;
  position: relative;
  transform: translateZ(0);
  will-change: transform;
}

/* Animated agriculture overlay */
.video-section::before {
  content: '';
  position: absolute;
  top: 0;
  left: 0;
  right: 0;
  bottom: 0;
  background: 
    radial-gradient(circle at 20% 30%, rgba(144, 238, 144, 0.1) 0%, transparent 50%),
    radial-gradient(circle at 80% 70%, rgba(34, 139, 34, 0.08) 0%, transparent 50%),
    linear-gradient(45deg, rgba(106, 168, 79, 0.05), transparent 70%);
  z-index: 1;
  mix-blend-mode: overlay;
  animation: overlay-pulse 8s ease-in-out infinite;
}

@keyframes overlay-pulse {
  0%, 100% { opacity: 0.8; }
  50% { opacity: 1; }
}

/* Floating farm elements */
.video-section::after {
  content: '🌾';
  position: absolute;
  top: 15%;
  right: 10%;
  font-size: 60px;
  opacity: 0.15;
  animation: crop-sway 12s ease-in-out infinite;
  z-index: 2;
  pointer-events: none;
  filter: drop-shadow(0 0 20px rgba(144, 238, 144, 0.3));
}

@keyframes crop-sway {
  0%, 100% { 
    transform: translateY(0) rotate(-3deg) scale(1); 
    opacity: 0.15; 
  }
  25% { 
    transform: translateY(-30px) rotate(3deg) scale(1.1); 
    opacity: 0.25; 
  }
  75% { 
    transform: translateY(-15px) rotate(-2deg) scale(0.95); 
    opacity: 0.2; 
  }
}

/* Ultra-crisp video styling */
.background-video {
  width: 100%;
  height: 100%;
  object-fit: cover;
  object-position: center;
  display: block;
  
  /* Premium video enhancement */
  filter: contrast(1.25) brightness(1.1) saturate(1.3) hue-rotate(2deg) blur(0px);
  
  /* Crisp rendering */
  transform: translateZ(0) scale(1.0);
  backface-visibility: hidden;
  will-change: transform, filter;
  image-rendering: -webkit-optimize-contrast;
  image-rendering: optimize-contrast;
  image-rendering: crisp-edges;
  
  transition: all 0.5s cubic-bezier(0.25, 0.46, 0.45, 0.94);
  
  /* High-quality rendering */
  -webkit-font-smoothing: antialiased;
  -moz-osx-font-smoothing: grayscale;
  color-interpolation: sRGB;
  color-rendering: optimizeQuality;
}

.video-section:hover .background-video {
  transform: translateZ(0) scale(1.03);
  filter: contrast(1.35) brightness(1.15) saturate(1.4) hue-rotate(3deg) blur(0px);
}

/*----------------------------------
	Enhanced Banner Section
------------------------------------------*/
.banner-section {
  height: 45vh;
  background: linear-gradient(135deg, 
    #fff 0%, 
   );
  display: flex;
  align-items: center;
  justify-content: center;
  position: relative;
  overflow: hidden;
  margin-top: 100px; /* Account for fixed header */
}

/* Animated farm landscape */
.banner-section::before {
  content: '';
  position: absolute;
  bottom: 0;
  left: 0;
  right: 0;
  height: 60%;
  background: 
    repeating-linear-gradient(
      90deg,
      rgba(12, 14, 12, 0.15) 0px,
      transparent 3px,
      transparent 30px,
      rgba(34, 139, 34, 0.1) 33px,
      rgba(34, 139, 34, 0.1) 60px
    );
  animation: field-breeze 12s ease-in-out infinite;
  z-index: 0;
}

@keyframes field-breeze {
  0%, 100% { 
    transform: translateX(0) scaleY(1); 
    opacity: 0.8; 
  }
  50% { 
    transform: translateX(15px) scaleY(1.05); 
    opacity: 1; 
  }
}

.text-box {
  background: linear-gradient(145deg, 
    rgba(255, 255, 255, 0.95), 
    rgba(248, 255, 248, 0.9));
  padding: 40px;
  border-radius: 25px;
  max-width: 700px;
  color: #2d5016;
  text-align: center;
  box-shadow: 
    0 25px 50px rgba(45, 80, 22, 0.2),
    0 0 40px rgba(144, 238, 144, 0.3) inset,
    0 1px 0 rgba(255, 255, 255, 0.8) inset;
  backdrop-filter: blur(20px) saturate(180%);
  border: 3px solid rgba(0, 1, 0, 0.4);
  position: relative;
  z-index: 1;
  transform: translateY(0) scale(1);
  transition: all 0.5s cubic-bezier(0.25, 0.46, 0.45, 0.94);
}

/* Floating agriculture icons around text box */
.text-box::before {
  content: '🌿';
  position: absolute;
  top: -15px;
  right: -15px;
  font-size: 40px;
  opacity: 0.4;
  animation: leaf-dance 4s ease-in-out infinite;
  filter: drop-shadow(0 0 10px rgba(144, 238, 144, 0.5));
}

.text-box::after {
  content: '🚜';
  position: absolute;
  bottom: -15px;
  left: -15px;
  font-size: 35px;
  opacity: 0.3;
  animation: tractor-move 6s ease-in-out infinite;
  filter: drop-shadow(0 0 10px rgba(34, 139, 34, 0.4));
}

@keyframes leaf-dance {
  0%, 100% { transform: rotate(-8deg) translateY(0); }
  50% { transform: rotate(8deg) translateY(-10px); }
}

@keyframes tractor-move {
  0%, 100% { transform: translateX(0) rotate(-2deg); }
  50% { transform: translateX(10px) rotate(2deg); }
}

.text-box:hover {
  transform: translateY(-12px) scale(1.02);
  box-shadow: 
    0 35px 70px rgba(45, 80, 22, 0.25),
    0 0 50px rgba(144, 238, 144, 0.4) inset,
    0 1px 0 rgba(255, 255, 255, 0.9) inset;
  border-color: rgba(144, 238, 144, 0.6);
}

.text-box h1 {
  font-size: 42px;
  font-weight: 800;
  margin: 0 0 25px 0;
  background: linear-gradient(45deg, #2d5016, #4a7c59, #6aa84f, #8bc34a);
  -webkit-background-clip: text;
  -webkit-text-fill-color: transparent;
  background-clip: text;
  position: relative;
  text-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
  animation: text-shimmer 3s ease-in-out infinite;
}

@keyframes text-shimmer {
  0%, 100% { filter: brightness(1); }
  50% { filter: brightness(1.2); }
}

.text-box h1::after {
  content: '';
  position: absolute;
  bottom: -8px;
  left: 50%;
  transform: translateX(-50%);
  width: 80px;
  height: 4px;
  background: linear-gradient(90deg, #6aa84f, #4a7c59, #6aa84f);
  border-radius: 2px;
  animation: underline-glow 2s ease-in-out infinite;
}

@keyframes underline-glow {
  0%, 100% { box-shadow: 0 0 5px rgba(238, 240, 237, 0.93); }
  50% { box-shadow: 0 0 15px rgba(245, 248, 244, 0.87); }
}

.text-box p {
  font-size: 18px;
  margin-top: 20px;
  line-height: 1.8;
  color: #4a7c59;
  font-weight: 500;
  text-shadow: 0 1px 2px rgba(255, 255, 255, 0.8);
}

/*----------------------------------
	Cool Card Enhancements
------------------------------------------*/
.card, .recent-car-list, .product-card {
  border: none;
  border-radius: 20px;
  background: linear-gradient(145deg, #ffffff 0%, #f8fff8 100%);
  box-shadow: 
    0 8px 30px rgba(45, 80, 22, 0.1),
    0 0 20px rgba(144, 238, 144, 0.05) inset;
  overflow: hidden;
  transition: all 0.4s cubic-bezier(0.25, 0.46, 0.45, 0.94);
  position: relative;
}

.card::before {
  content: '';
  position: absolute;
  top: 0;
  left: 0;
  right: 0;
  height: 2px;
  background: linear-gradient(90deg, 
    transparent, 
    rgba(4, 4, 4, 0.8), 
    transparent);
  z-index: 1;
}

.card:hover {
  transform: translateY(-15px) scale(1.03);
  box-shadow: 
    0 25px 60px rgba(45, 80, 22, 0.2),
    0 0 30px rgba(144, 238, 144, 0.15) inset;
  background: linear-gradient(145deg, #ffffff 0%, #f0fff0 100%);
}

/*----------------------------------
	Responsive Design
------------------------------------------*/
@media (max-width: 768px) {
  body {
    padding-top: 80px !important;
  }
  
  #navigation_bar {
    padding: 10px 0;
    height: 70px;
  }
  
  .banner-section {
    height: 40vh;
    margin-top: 70px;
  }
  
  .text-box {
    padding: 30px;
    margin: 0 20px;
  }
  
  .text-box h1 {
    font-size: 32px;
  }
  
  .text-box p {
    font-size: 16px;
  }
}

@media (max-width: 480px) {
  .text-box {
    padding: 25px;
    margin: 0 15px;
  }
  
  .text-box h1 {
    font-size: 28px;
  }
  
  .video-section::after {
    font-size: 40px;
    top: 10%;
    right: 5%;
  }
}
/*----------------------------- 
	3.4. Recently-Listed-Cars 
-----------------------------------*/
.recent-tab {
  margin: 0 auto;
  text-align: center;
}

.recent-tab ul.nav-tabs {
  background: #ffffff none repeat scroll 0 0;
  border-radius: 30px;
  box-shadow: 0 0 10px rgba(0, 0, 0, 0.2);
  margin: 0 auto;
  display: inline-block;
}

.recent-tab .nav.nav-tabs a {
  background: rgba(0, 0, 0, 0) none repeat scroll 0 0;
  border: medium none;
  border-radius: 30px;
  color: #222222;
  font-size: 15px;
  line-height: 26px;
  margin: 0 auto;
  padding: 6px 32px;
  position: relative;
}

.recent-tab .nav.nav-tabs li.active a {
  border: medium none;
  color: #ffffff;
}

.recent-tab .nav.nav-tabs li.active::after {
  border-style: solid;
  border-width: 12px;
  bottom: -20px;
  content: "";
  height: 8px;
  left: 0;
  margin: 0 auto;
  position: absolute;
  right: 0;
  width: 12px;
}

.col-list-3 {
  float: left;
  padding: 0 15px;
  width: 33%;
}

.col-list-3:nth-child(3n) {
  clear: right;
}

.col-list-3:nth-child(3n+1) {
  clear: left;
}

.recent-car-list {
  background: #ffffff none repeat scroll 0 0;
  box-shadow: 0 0 10px rgba(0, 0, 0, 0.2);
  margin-top: 40px;
  overflow: hidden; /* Prevent image overflow */
}

/* Fixed image container */
.car-info-box {
  position: relative;
  overflow: hidden; /* Prevent image overflow */
  height: 250px; /* Set consistent height */
  background: #f8f9fa; /* Fallback background */
}

/* Image styling for proper display */
.car-info-box img {
  width: 100%;
  height: 100%;
  object-fit: cover; /* Maintain aspect ratio while covering container */
  object-position: center; /* Center the image */
  display: block;
  transition: transform 0.3s ease;
  transform: scale(1); /* Reset any zoom */
}

/* Hover effect for images */
.car-info-box:hover img {
  transform: scale(1.05); /* Slight zoom on hover */
}

/* Alternative: If you want to fit entire image without cropping */
.car-info-box.fit-image img {
  object-fit: contain; /* Show entire image with possible letterboxing */
  background: #ffffff; /* Background color for letterboxed areas */
}

.car-info-box ul {
  background: rgba(0, 0, 0, 0.6) none repeat scroll 0 0;
  bottom: 0;
  margin: 0 auto;
  padding: 0 15px;
  position: absolute;
  width: 100%;
  z-index: 2; /* Ensure overlay stays on top */
}

.car-info-box li {
  color: #ffffff;
  display: inline-block;
  font-size: 13px;
  line-height: 50px;
  list-style: outside none none;
  margin: 0 15px 0 auto;
}

.car-info-box li .fa {
  margin-right: 8px;
}

.car-info-box li:nth-last-child(1) {
  margin-right: 0;
}

.car-title-m {
  overflow: hidden;
  padding: 20px;
}

.car-title-m h6 {
  float: left;
  margin: 0 auto;
  max-width: 245px;
}

.car-title-m h6 a {
  color: #111111;
}

.car-title-m .price {
  color: #555555;
  float: right;
  font-size: 16px;
  font-weight: 800;
}

/* Responsive adjustments */
@media (max-width: 768px) {
  .col-list-3 {
    width: 50%;
  }
  
  .car-info-box {
    height: 200px;
  }
}

@media (max-width: 480px) {
  .col-list-3 {
    width: 100%;
    margin-bottom: 20px;
  }
  
  .car-info-box {
    height: 250px;
  }
}

/* Additional image container variants */
.car-info-box.tall {
  height: 300px; /* Taller variant */
}

.car-info-box.wide {
  height: 200px; /* Shorter, wider variant */
}

/* Image loading placeholder */
.car-info-box::before {
  content: '';
  position: absolute;
  top: 0;
  left: 0;
  right: 0;
  bottom: 0;
  background: linear-gradient(45deg, #f0f0f0 25%, transparent 25%), 
              linear-gradient(-45deg, #f0f0f0 25%, transparent 25%), 
              linear-gradient(45deg, transparent 75%, #f0f0f0 75%), 
              linear-gradient(-45deg, transparent 75%, #f0f0f0 75%);
  background-size: 20px 20px;
  background-position: 0 0, 0 10px, 10px -10px, -10px 0px;
  opacity: 0;
  z-index: 1;
  transition: opacity 0.3s ease;
}

/* Show placeholder when image is loading */
.car-info-box.loading::before {
  opacity: 1;
}

.car-info-box.loading img {
  opacity: 0;
}
/*-----------------
	3.12. Footer
------------------------*/
.footer-top {
  background: #4bd45bff none repeat scroll 0 0;
  color: #9d9d9d;
  padding: 80px 0;
}
.footer-top h6 {
  color: #ffffff;
  font-size:15px;
  text-transform:uppercase;
  margin-bottom: 40px;
}
.footer-top ul {
  overflow: hidden;
  padding: 0;
}
.footer-top ul li {
  font-size: 14px;
  line-height: 23px;
  list-style: outside none none;
  margin-bottom: 16px;
  padding-left: 12px;
  position: relative;
}
.footer-top ul li a::after {
  content: "";
  font-family: fontawesome;
  left: 0;
  position: absolute;
  vertical-align: middle;
}
.footer-top ul li a {
  color: #fff;
}

.footer-bottom {
  background: #191919 none repeat scroll 0 0;
  padding: 22px 0;
}
.footer_widget {
  color: #ffffff;
  display: inline-block;
  margin: 6px 0 0 30px;
}
.footer_widget p {
	display:inline-block;
	vertical-align:middle;
	margin:0px;
}
.footer_widget ul  {
	display:inline-block;
	padding:0px;
	vertical-align:middle;
	margin:0px 0 0 8px;
}
.footer_widget ul li {
	display:inline-block;
	vertical-align:middle;
	list-style:none;
	margin:0 auto;
}
.footer_widget ul li a {
	color:#fff;
	display:block;
	font-size:18px;
	margin:0 4px;
}
.footer_widget ul li a i {
	margin:0px;
}
.copy-right {
  color: #ffffff;
  font-size: 15px;
  line-height: 40px;
  margin: 0 auto;
}

</style>
</body>
</html>