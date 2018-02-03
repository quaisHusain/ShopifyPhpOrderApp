<!doctype html>
<html class="no-js" lang="en">
<head>
        <meta charset="utf-8">
        <meta http-equiv="x-ua-compatible" content="ie=edge">
        <title>Rituraj</title>
        <meta name="description" content="">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <link rel="apple-touch-icon" href="apple-touch-icon.html">
        <!-- Place favicon.ico in the root directory -->
        <link rel="stylesheet" href="<?php echo base_url();?>theme_assets/css/vendor.css">
        <link rel="stylesheet" href="<?php echo base_url();?>theme_assets/css/app.css">
        <link rel="stylesheet" id="theme-style" href="<?php echo base_url();?>theme_assets/css/app.css">
         <link rel="stylesheet" id="theme-style" href="<?php echo base_url();?>theme_assets/jquery.dataTables.css">
         <link href="<?php echo site_url(); ?>assets/css/seaff.css" type="text/css" rel="stylesheet">
        <script src="https://cdn.shopify.com/s/assets/external/app.js"></script>
        <script type="text/javascript">
            ShopifyApp.init({
                apiKey : '<?php echo $this->config->item('shopify_api_key'); ?>',
                shopOrigin : '<?php echo 'https://'  . $this->session->userdata('shop'); ?>' 
            });

        </script>
        <?php
        $shop =$this->session->userdata('shop');
        $access_token=$this->session->userdata('access_token');
        ?>  
        <script type="text/javascript">
         var shop='<?php echo $shop; ?>';
         var access_token='<?php echo $access_token; ?>';
        </script>
        <script type="text/javascript">
            ShopifyApp.ready(function(){
                ShopifyApp.Bar.initialize({
                buttons: {
                    primary: {
                    label: 'Save',
                    message: 'unicorn_form_submit',
                    loading: true
                    }
                }
                });
            });
    </script>
      <script src="<?php echo base_url();?>theme_assets/jquery.js"></script>
      
    <style type="text/css">
          .loading {
    display: none;
  position: fixed;
  z-index: 999;
  height: 2em;
  width: 2em;
  overflow: show;
  margin: auto;
  top: 0;
  left: 0;
  bottom: 0;
  right: 0;
}

/* Transparent Overlay */
.loading:before {
  content: '';
  display: block;
  position: fixed;
  top: 0;
  left: 0;
  width: 100%;
  height: 100%;
  background-color: rgba(0,0,0,0.3);
}

/* :not(:required) hides these rules from IE9 and below */
.loading:not(:required) {
  /* hide "loading..." text */
  font: 0/0 a;
  color: transparent;
  text-shadow: none;
  background-color: transparent;
  border: 0;
}

.loading:not(:required):after {
  content: '';
  display: block;
  font-size: 10px;
  width: 1em;
  height: 1em;
  margin-top: -0.5em;
  -webkit-animation: spinner 1500ms infinite linear;
  -moz-animation: spinner 1500ms infinite linear;
  -ms-animation: spinner 1500ms infinite linear;
  -o-animation: spinner 1500ms infinite linear;
  animation: spinner 1500ms infinite linear;
  border-radius: 0.5em;
  -webkit-box-shadow: rgba(0, 0, 0, 0.75) 1.5em 0 0 0, rgba(0, 0, 0, 0.75) 1.1em 1.1em 0 0, rgba(0, 0, 0, 0.75) 0 1.5em 0 0, rgba(0, 0, 0, 0.75) -1.1em 1.1em 0 0, rgba(0, 0, 0, 0.5) -1.5em 0 0 0, rgba(0, 0, 0, 0.5) -1.1em -1.1em 0 0, rgba(0, 0, 0, 0.75) 0 -1.5em 0 0, rgba(0, 0, 0, 0.75) 1.1em -1.1em 0 0;
  box-shadow: rgba(0, 0, 0, 0.75) 1.5em 0 0 0, rgba(0, 0, 0, 0.75) 1.1em 1.1em 0 0, rgba(0, 0, 0, 0.75) 0 1.5em 0 0, rgba(0, 0, 0, 0.75) -1.1em 1.1em 0 0, rgba(0, 0, 0, 0.75) -1.5em 0 0 0, rgba(0, 0, 0, 0.75) -1.1em -1.1em 0 0, rgba(0, 0, 0, 0.75) 0 -1.5em 0 0, rgba(0, 0, 0, 0.75) 1.1em -1.1em 0 0;
}

/* Animation */

@-webkit-keyframes spinner {
  0% {
    -webkit-transform: rotate(0deg);
    -moz-transform: rotate(0deg);
    -ms-transform: rotate(0deg);
    -o-transform: rotate(0deg);
    transform: rotate(0deg);
  }
  100% {
    -webkit-transform: rotate(360deg);
    -moz-transform: rotate(360deg);
    -ms-transform: rotate(360deg);
    -o-transform: rotate(360deg);
    transform: rotate(360deg);
  }
}
@-moz-keyframes spinner {
  0% {
    -webkit-transform: rotate(0deg);
    -moz-transform: rotate(0deg);
    -ms-transform: rotate(0deg);
    -o-transform: rotate(0deg);
    transform: rotate(0deg);
  }
  100% {
    -webkit-transform: rotate(360deg);
    -moz-transform: rotate(360deg);
    -ms-transform: rotate(360deg);
    -o-transform: rotate(360deg);
    transform: rotate(360deg);
  }
}
@-o-keyframes spinner {
  0% {
    -webkit-transform: rotate(0deg);
    -moz-transform: rotate(0deg);
    -ms-transform: rotate(0deg);
    -o-transform: rotate(0deg);
    transform: rotate(0deg);
  }
  100% {
    -webkit-transform: rotate(360deg);
    -moz-transform: rotate(360deg);
    -ms-transform: rotate(360deg);
    -o-transform: rotate(360deg);
    transform: rotate(360deg);
  }
}
@keyframes spinner {
  0% {
    -webkit-transform: rotate(0deg);
    -moz-transform: rotate(0deg);
    -ms-transform: rotate(0deg);
    -o-transform: rotate(0deg);
    transform: rotate(0deg);
  }
  100% {
    -webkit-transform: rotate(360deg);
    -moz-transform: rotate(360deg);
    -ms-transform: rotate(360deg);
    -o-transform: rotate(360deg);
    transform: rotate(360deg);
  }
}



    </style>
    <style type="text/css">
  
  table, tr, th, td {
    text-align: center;
    /*border-color: transparent;*/
   border-bottom:1px;
   border-bottom-style: solid;
    line-height: 250%;

    
    
  
}

table, td{
  border-bottom-color: gray;
}
.table-css{
    background-color: white;
    margin: 50px;
    padding: 20px;
    border-radius: 10px;

}


table, th{
  text-align: center;
    border-collapse: collapse;
    font-family: Arial;
}

.circle_red {
  border-radius: 50%;
  width: 20px;
  height: 20px; 
    background-color: red;
    margin: auto;
    /*border-radius: transperant; */
  /* width and height can be anything, as long as they're equal */
}

.circle_green {
  border-radius: 50%;
  width: 20px;
  height: 20px; 
    background-color: green;
    border-radius: transperant; 
    margin: auto;
  /* width and height can be anything, as long as they're equal */
}

.circle_yellow {
  border-radius: 50%;
  width: 20px;
  height: 20px; 
    background-color: yellow;
    border-radius: transperant; 
    margin: auto;
  /* width and height can be anything, as long as they're equal */
}

.circle_blue {
  border-radius: 50%;
  width: 20px;
  height: 20px; 
    background-color: blue;
    border-radius: transperant; 
    margin: auto;
  /* width and height can be anything, as long as they're equal */
}
.dataTables_filter input { width: 50px }
.width35{width: 350px}
</style>
    </head>
    <div class="loading">Loading&#8230;</div>
    <body>
        <div class="main-wrapper">
            <div class="" id="app">
             