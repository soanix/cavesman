<!DOCTYPE html>
<html lang="en">
<head>
    <title>Dashboard | Dashboard</title>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="shortcut icon" href="images/icons/favicon.ico">
    <link rel="apple-touch-icon" href="images/icons/favicon.png">
    <link rel="apple-touch-icon" sizes="72x72" href="images/icons/favicon-72x72.png">
    <link rel="apple-touch-icon" sizes="114x114" href="images/icons/favicon-114x114.png">
    <!--Loading bootstrap css-->

    <link type="text/css" rel="stylesheet" href="http://fonts.googleapis.com/css?family=Open+Sans:400italic,400,300,700">
    <link type="text/css" rel="stylesheet" href="http://fonts.googleapis.com/css?family=Oswald:400,700,300">
    <link type="text/css" rel="stylesheet" href="{$css}/jquery-ui-1.10.4.custom.min.css">
    <link href="https://maxcdn.bootstrapcdn.com/font-awesome/4.5.0/css/font-awesome.min.css" rel="stylesheet" integrity="sha384-XdYbMnZ/QjLh6iI4ogqCTaIjrFk87ip+ekIjefZch0Y+PvJ8CDYtEs1ipDmPorQ+" crossorigin="anonymous">
    <link type="text/css" rel="stylesheet" href="{$css}/bootstrap.min.css">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/fancybox/2.1.5/jquery.fancybox.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/fancybox/2.1.5/helpers/jquery.fancybox-thumbs.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/fancybox/2.1.5/helpers/jquery.fancybox-buttons.css" rel="stylesheet">
    <link type="text/css" rel="stylesheet" href="{$css}/animate.css">
    <link type="text/css" rel="stylesheet" href="{$css}/all.css">
    <link type="text/css" rel="stylesheet" href="{$css}/main.css">
    <link type="text/css" rel="stylesheet" href="{$css}/style-responsive.css">
    <link type="text/css" rel="stylesheet" href="{$css}/zabuto_calendar.min.css">
    <link type="text/css" rel="stylesheet" href="{$css}/pace.css">
    <link type="text/css" rel="stylesheet" href="{$css}/jquery.news-ticker.css">
    <link type="text/css" rel="stylesheet" href="{$js}/datepicker/css/bootstrap-datepicker3.min.css">
    <link type="text/css" rel="stylesheet" href="/cdn/css/jquery.cropbox.css">
</head>
<body>
	<div class="loader-container">
		<div class="loader"></div>
	</div>
    <div>
        <!--BEGIN THEME SETTING-->
        {*{include "theme-settings.tpl"}*}
        <!--END THEME SETTING-->
        <!--BEGIN BACK TO TOP-->
        <a id="totop" href="#"><i class="fa fa-angle-up"></i></a>
        <!--END BACK TO TOP-->
        <!--BEGIN TOPBAR-->
         {include "top-bar.tpl"}
        <!--END TOPBAR-->
        <div id="wrapper">
            <!--BEGIN SIDEBAR MENU-->
            {include "sidebar.tpl"}
            <!--END SIDEBAR MENU-->
            <!--BEGIN CHAT FORM-->
            {include "chat-form.tpl"}
            <!--END CHAT FORM-->
            <!--BEGIN PAGE WRAPPER-->
            <div id="page-wrapper">
                <!--BEGIN TITLE & BREADCRUMB PAGE-->
                <div id="title-breadcrumb-option-demo" class="page-title-breadcrumb">
                    <div class="page-header pull-left">
                        <div class="page-title">
                            Dashboard</div>
                    </div>
                    <ol class="breadcrumb page-breadcrumb pull-right hidden">
                        <li><i class="fa fa-home"></i>&nbsp;<a href="dashboard.html">Home</a>&nbsp;&nbsp;<i class="fa fa-angle-right"></i>&nbsp;&nbsp;</li>
                        <li class="hidden"><a href="#">Dashboard</a>&nbsp;&nbsp;<i class="fa fa-angle-right"></i>&nbsp;&nbsp;</li>
                        <li class="active">Dashboard</li>
                    </ol>
                    <div class="clearfix">
                    </div>
                </div>
                <!--END TITLE & BREADCRUMB PAGE-->
                <!--BEGIN CONTENT-->
                {include "{$page}.tpl"}
                <!--END CONTENT-->
                <!--BEGIN FOOTER-->
                {include "footer.tpl"}
                <!--END FOOTER-->
            </div>
            <!--END PAGE WRAPPER-->
        </div>
    </div>
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/1.12.0/jquery.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery.form/3.51/jquery.form.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery-mousewheel/3.1.13/jquery.mousewheel.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/hammer.js/2.0.8/hammer.min.js"></script>
    <script src="{$js}/jquery-ui.js"></script>
    <script src="{$js}/bootstrap.min.js"></script>
    <script src="{$js}/bootstrap-hover-dropdown.js"></script>
    <script src="{$js}/html5shiv.js"></script>
    <script src="{$js}/respond.min.js"></script>
    <script src="{$js}/jquery.metisMenu.js"></script>
    <script src="{$js}/jquery.slimscroll.js"></script>
    <script src="{$js}/jquery.cookie.js"></script>
    <script src="{$js}/icheck.min.js"></script>
    <script src="{$js}/custom.min.js"></script>
    <script src="{$js}/jquery.news-ticker.js"></script>
    <script src="{$js}/jquery.menu.js"></script>
    <script src="{$js}/pace.min.js"></script>
    <script src="{$js}/holder.js"></script>
    <script src="{$js}/responsive-tabs.js"></script>
    <script src="{$js}/jquery.flot.js"></script>
    <script src="{$js}/jquery.flot.categories.js"></script>
	<script src="{$js}/bootbox.min.js"></script>
    <script src="{$js}/jquery.flot.pie.js"></script>
    <script src="{$js}/jquery.flot.tooltip.js"></script>
    <script src="{$js}/jquery.flot.resize.js"></script>
    <script src="{$js}/jquery.flot.fillbetween.js"></script>
    <script src="{$js}/jquery.flot.stack.js"></script>
	<script src="{$js}/autoresize.min.js"></script>
    <script src="{$js}/jquery.flot.spline.js"></script>
    <script src="{$js}/zabuto_calendar.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/tinymce/4.3.13/tinymce.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/tinymce/4.3.13/jquery.tinymce.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/tinymce/4.3.13/themes/modern/theme.min.js"></script>
    <script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/fancybox/2.1.5/jquery.fancybox.min.js"></script>
    <script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/fancybox/2.1.5/helpers/jquery.fancybox-thumbs.js"></script>
    <script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/fancybox/2.1.5/helpers/jquery.fancybox-media.js"></script>
    <script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/fancybox/2.1.5/helpers/jquery.fancybox-buttons.js"></script>

    <script src="{$js}/index.js"></script>

    <script src="/cdn/js/jquery.cropbox.js"></script>
    <!--LOADING SCRIPTS FOR CHARTS-->
    <!--<script src="{$js}/highcharts.js"></script>
    <script src="{$js}/data.js"></script>
    <script src="{$js}/drilldown.js"></script>
    <script src="{$js}/exporting.js"></script>
    <!-<script src="{$js}/highcharts-more.js"></script>
    <script src="{$js}/charts-highchart-pie.js"></script>
    <script src="{$js}/charts-highchart-more.js"></script> -->

    <!--CORE JAVASCRIPT-->
    <script src="{$js}/datepicker/js/bootstrap-datepicker.min.js"></script>
    <script src="{$js}/datepicker/locales/bootstrap-datepicker.es.min.js"></script>
    <script src="{$js}/main.js"></script>
    <script src="{$js}/admin.js"></script>

</body>
</html>
