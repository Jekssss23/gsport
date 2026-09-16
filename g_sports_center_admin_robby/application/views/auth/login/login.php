<!doctype html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta http-equiv="Content-Language" content="en">
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <title>G Sports Center Management</title>
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no, shrink-to-fit=no" />

    <!-- Disable tap highlight on IE -->
    <meta name="msapplication-tap-highlight" content="no">

    <link rel="stylesheet" href="<?php echo base_url('assets/user_template/dist/') ?>assets/css/base.min.css">

</head>

<body>
<div class="app-container app-theme-white body-tabs-shadow">
        <div class="app-container">
            <div class="h-100">
                <div class="h-100 no-gutters row">
                    <div class="d-none d-lg-block col-lg-4">
                        <div class="slider-light">
                            <div class="slick-slider">
                                <div>
                                    <div class="position-relative h-100 d-flex justify-content-center align-items-center bg-plum-plate" tabindex="-1">
                                        <div class="slide-img-bg" style="background-image: url('<?php echo base_url('assets/gambar') ?>/1.jpeg');"></div>
                                        <div class="slider-content"><h3>Perfect Balance</h3>
                                            <p>ArchitectUI is like a dream. Some think it's too good to be true! Extensive collection of unified React Boostrap Components and Elements.</p></div>
                                    </div>
                                </div>
                                <div>
                                    <div class="position-relative h-100 d-flex justify-content-center align-items-center bg-premium-dark" tabindex="-1">
                                        <div class="slide-img-bg" style="background-image: url('<?php echo base_url('assets/gambar') ?>/2.jpeg');"></div>
                                        <div class="slider-content"><h3>Scalable, Modular, Consistent</h3>
                                            <p>Easily exclude the components you don't require. Lightweight, consistent Bootstrap based styles across all elements and components</p></div>
                                    </div>
                                </div>
                                <div>
                                    <div class="position-relative h-100 d-flex justify-content-center align-items-center bg-sunny-morning" tabindex="-1">
                                        <div class="slide-img-bg" style="background-image: url('<?php echo base_url('assets/gambar') ?>/3.jpeg');"></div>
                                        <div class="slider-content"><h3>Complex, but lightweight</h3>
                                            <p>We've included a lot of components that cover almost all use cases for any type of application.</p></div>
                                    </div>
                                </div>
                                <div>
                                    <div class="position-relative h-100 d-flex justify-content-center align-items-center bg-premium-dark" tabindex="-1">
                                        <div class="slide-img-bg" style="background-image: url('<?php echo base_url('assets/gambar') ?>/4.jpeg');"></div>
                                        <div class="slider-content"><h3>Scalable, Modular, Consistent</h3>
                                            <p>Easily exclude the components you don't require. Lightweight, consistent Bootstrap based styles across all elements and components</p></div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="h-100 d-flex bg-white justify-content-center align-items-center col-md-12 col-lg-8">
                        <div class="mx-auto app-login-box col-sm-12 col-md-10 col-lg-9">
                            <img src="<?php echo base_url('assets/gambar/logo.png') ?>" width="200px"> 
                            
                                <h4 class="d-block">Selamat Datang di aplikasi G Sports Center management</h4>
                                <span>Please sign in to your account.</span>
                            <div class="divider row"></div>
                            <?php echo $this->session->flashdata('pesan') ?>
                            <div>
                                <form class="" action="<?php echo base_url('auth/login/proses_login') ?>" method="post">
                                    <div class="form-row">
                                        <div class="col-md-6">
                                            <div class="position-relative form-group"><label for="exampleEmail" class="">Username</label><input name="username"  required placeholder="Username..." type="text" class="form-control"></div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="position-relative form-group"><label for="examplePassword" class="">Password</label><input name="password" required placeholder="Password..." type="password" class="form-control"></div>
                                        </div>
                                    </div>
                                    <!-- <div class="position-relative form-check"><input name="check" id="exampleCheck" type="checkbox" class="form-check-input"><label for="exampleCheck" class="form-check-label">Keep me logged in</label></div> -->
                                    <div class="divider row"></div>
                                    <div class="d-flex align-items-center">
                                        <div class="ml-auto">
                                            <button class="btn btn-primary btn-lg">Login </button>
                                        </div>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
</div>


<!--SCRIPTS INCLUDES-->

<!--CORE-->
<script src="https://code.jquery.com/jquery-3.3.1.min.js" integrity="sha256-FgpCb/KJQlLNfOu91ta32o/NMZxltwRo8QtmkMRdAu8=" crossorigin="anonymous"></script>
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.2.1/js/bootstrap.bundle.min.js" crossorigin="anonymous"></script>
<script src="https://cdn.jsdelivr.net/npm/metismenu"></script>



<script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/2.4.0/Chart.min.js"></script>
<script src="<?php echo base_url('assets/user_template/dist/') ?>assets/js/scripts-init/charts/chartsjs-utils.js"></script>

<!--FORMS-->

<!--Clipboard-->
<script src="<?php echo base_url('assets/user_template/dist/') ?>assets/js/vendors/form-components/clipboard.js"></script>
<script src="<?php echo base_url('assets/user_template/dist/') ?>assets/js/scripts-init/form-components/clipboard.js"></script>

<!--Datepickers-->
<script src="<?php echo base_url('assets/user_template/dist/') ?>assets/js/vendors/form-components/datepicker.js"></script>
<script src="<?php echo base_url('assets/user_template/dist/') ?>assets/js/vendors/form-components/daterangepicker.js"></script>
<script src="<?php echo base_url('assets/user_template/dist/') ?>assets/js/vendors/form-components/moment.js"></script>
<script src="<?php echo base_url('assets/user_template/dist/') ?>assets/js/scripts-init/form-components/datepicker.js"></script>

<!--Multiselect-->
<script src="<?php echo base_url('assets/user_template/dist/') ?>assets/js/vendors/form-components/bootstrap-multiselect.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.6-rc.0/js/select2.min.js"></script>
<script src="<?php echo base_url('assets/user_template/dist/') ?>assets/js/scripts-init/form-components/input-select.js"></script>

<!--Form Validation-->
<script src="<?php echo base_url('assets/user_template/dist/') ?>assets/js/vendors/form-components/form-validation.js"></script>
<script src="<?php echo base_url('assets/user_template/dist/') ?>assets/js/scripts-init/form-components/form-validation.js"></script>

<!--Form Wizard-->
<script src="<?php echo base_url('assets/user_template/dist/') ?>assets/js/vendors/form-components/form-wizard.js"></script>
<script src="<?php echo base_url('assets/user_template/dist/') ?>assets/js/scripts-init/form-components/form-wizard.js"></script>

<!--Input Mask-->
<script src="<?php echo base_url('assets/user_template/dist/') ?>assets/js/vendors/form-components/input-mask.js"></script>
<script src="<?php echo base_url('assets/user_template/dist/') ?>assets/js/scripts-init/form-components/input-mask.js"></script>

<!--RangeSlider-->
<script src="<?php echo base_url('assets/user_template/dist/') ?>assets/js/vendors/form-components/wnumb.js"></script>
<script src="<?php echo base_url('assets/user_template/dist/') ?>assets/js/vendors/form-components/range-slider.js"></script>
<script src="<?php echo base_url('assets/user_template/dist/') ?>assets/js/scripts-init/form-components/range-slider.js"></script>

<!--Textarea Autosize-->
<script src="<?php echo base_url('assets/user_template/dist/') ?>assets/js/vendors/form-components/textarea-autosize.js"></script>
<script src="<?php echo base_url('assets/user_template/dist/') ?>assets/js/scripts-init/form-components/textarea-autosize.js"></script>

<!--Toggle Switch -->
<script src="<?php echo base_url('assets/user_template/dist/') ?>assets/js/vendors/form-components/toggle-switch.js"></script>


<!--COMPONENTS-->

<!--BlockUI -->
<script src="<?php echo base_url('assets/user_template/dist/') ?>assets/js/vendors/blockui.js"></script>
<script src="<?php echo base_url('assets/user_template/dist/') ?>assets/js/scripts-init/blockui.js"></script>

<!--Calendar -->
<script src="<?php echo base_url('assets/user_template/dist/') ?>assets/js/vendors/calendar.js"></script>
<script src="<?php echo base_url('assets/user_template/dist/') ?>assets/js/scripts-init/calendar.js"></script>

<!--Slick Carousel -->
<script src="<?php echo base_url('assets/user_template/dist/') ?>assets/js/vendors/carousel-slider.js"></script>
<script src="<?php echo base_url('assets/user_template/dist/') ?>assets/js/scripts-init/carousel-slider.js"></script>

<!--Circle Progress -->
<script src="<?php echo base_url('assets/user_template/dist/') ?>assets/js/vendors/circle-progress.js"></script>
<script src="<?php echo base_url('assets/user_template/dist/') ?>assets/js/scripts-init/circle-progress.js"></script>

<!--CountUp -->
<script src="<?php echo base_url('assets/user_template/dist/') ?>assets/js/vendors/count-up.js"></script>
<script src="<?php echo base_url('assets/user_template/dist/') ?>assets/js/scripts-init/count-up.js"></script>

<!--Cropper -->
<script src="<?php echo base_url('assets/user_template/dist/') ?>assets/js/vendors/cropper.js"></script>
<script src="<?php echo base_url('assets/user_template/dist/') ?>assets/js/vendors/jquery-cropper.js"></script>
<script src="<?php echo base_url('assets/user_template/dist/') ?>assets/js/scripts-init/image-crop.js"></script>

<!--Maps -->
<script src="<?php echo base_url('assets/user_template/dist/') ?>assets/js/vendors/gmaps.js"></script>
<script src="<?php echo base_url('assets/user_template/dist/') ?>assets/js/vendors/jvectormap.js"></script>
<script src="<?php echo base_url('assets/user_template/dist/') ?>assets/js/scripts-init/maps-word-map.js"></script>
<script src="<?php echo base_url('assets/user_template/dist/') ?>assets/js/scripts-init/maps.js"></script>

<!--Guided Tours -->
<script src="<?php echo base_url('assets/user_template/dist/') ?>assets/js/vendors/guided-tours.js"></script>
<script src="<?php echo base_url('assets/user_template/dist/') ?>assets/js/scripts-init/guided-tours.js"></script>

<!--Ladda Loading Buttons -->
<script src="<?php echo base_url('assets/user_template/dist/') ?>assets/js/vendors/ladda-loading.js"></script>
<script src="<?php echo base_url('assets/user_template/dist/') ?>assets/js/vendors/spin.js"></script>
<script src="<?php echo base_url('assets/user_template/dist/') ?>assets/js/scripts-init/ladda-loading.js"></script>

<!--Rating -->
<script src="<?php echo base_url('assets/user_template/dist/') ?>assets/js/vendors/rating.js"></script>
<script src="<?php echo base_url('assets/user_template/dist/') ?>assets/js/scripts-init/rating.js"></script>

<!--Perfect Scrollbar -->
<script src="<?php echo base_url('assets/user_template/dist/') ?>assets/js/vendors/scrollbar.js"></script>
<script src="<?php echo base_url('assets/user_template/dist/') ?>assets/js/scripts-init/scrollbar.js"></script>

<!--Toastr-->

<!--SweetAlert2-->
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@8"></script>

<!--Tree View -->


<!--TABLES -->
<!--DataTables-->
<script src="https://cdn.datatables.net/1.10.19/js/jquery.dataTables.min.js" crossorigin="anonymous"></script>
<script src="https://cdn.jsdelivr.net/npm/datatables.net-bs4@1.10.19/js/dataTables.bootstrap4.min.js" crossorigin="anonymous"></script>
<script src="https://cdn.datatables.net/responsive/2.2.3/js/dataTables.responsive.min.js" crossorigin="anonymous"></script>
<script src="https://cdn.datatables.net/responsive/2.2.3/js/responsive.bootstrap.min.js" crossorigin="anonymous"></script>

<!--Bootstrap Tables-->

<!--Tables Init-->

</body>
</html>
