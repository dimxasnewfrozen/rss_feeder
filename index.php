<?php

session_start();

$_SESSION['list'] = array();

?>
<!DOCTYPE html>
<!--[if lt IE 7]>      <html class="no-js lt-ie9 lt-ie8 lt-ie7"> <![endif]-->
<!--[if IE 7]>         <html class="no-js lt-ie9 lt-ie8"> <![endif]-->
<!--[if IE 8]>         <html class="no-js lt-ie9"> <![endif]-->
<!--[if gt IE 8]><!--> <html class="no-js"> <!--<![endif]-->
    <head>
        <meta charset="utf-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">
        <title></title>
        <meta name="description" content="">
        <meta name="viewport" content="width=device-width">

        <link rel="stylesheet" href="css/bootstrap.min.css">
        <style>
            body {
                padding-top: 60px;
                padding-bottom: 40px;
				
            }
			
			.sidebar{
				overflow:auto;
			}
		
			a.share {
				padding-right:5px;
				color:#4970B3;
				text-decoration:none;
				font-size:14px;
			}
			
			a.share:hover{
				color:#4086FF;
				font-size:15px;
			}

        </style>
        <link rel="stylesheet" href="css/bootstrap-responsive.min.css">
        <link rel="stylesheet" href="css/main.css">
        <link rel="stylesheet" href="css/font-awesome.css">
		<link href="css/jquery.mCustomScrollbar.css" rel="stylesheet" />
		 
        <script src="js/vendor/modernizr-2.6.2-respond-1.1.0.min.js"></script>
		        <script src="//ajax.googleapis.com/ajax/libs/jquery/1.8.3/jquery.js"></script>
        <script>window.jQuery || document.write('<script src="js/vendor/jquery-1.8.3.js"><\/script>')</script>
		<script src="js/jquery.mCustomScrollbar.js"></script>
		<script src="js/masonry.js"></script>
		<script >
		$(function() {

			var height = $(window).height();
		
			var $container = $('.main_content');
			
			var $type 	= $(".type").val();
			var $search = $('.search').val();
			var $view   = $('.view').val();
			
			$(".main_content").load('source.php?view=' + $view + '&s=' + encodeURIComponent($search) + '&t=' + $type, function() {
				$container.masonry({
				  itemSelector: '.container_item',
				  columnWidth: 140
				});
			
			});
		
			setInterval(function() {
				$(".loading").show();
				
				$.get('source.php?view=' + $view + '&s=' + encodeURIComponent($search) + '&t=' + $type, function(data){ 
				  $container.prepend( data ).masonry( 'reload' );
				});
				
				$(".loading").hide();
			}, 15000);
			
			
			$(window).scroll(function() {
			   if($(window).scrollTop() + $(window).height() == $(document).height()) {
					
				    $(".loading_bottom").removeClass("hide");
					
					$.get('source.php?view=' + $view + '&s=' + encodeURIComponent($search) + '&t=' + $type, function(data){ 
						$container.append( data ).masonry( 'reload' );
					});
					
					$(".loading_bottom").addClass("hide");
				}
			});
			
		});
		
		</script>

    </head>


    <body>
        <!--[if lt IE 7]>
            <p class="chromeframe">You are using an <strong>outdated</strong> browser. Please <a href="http://browsehappy.com/">upgrade your browser</a> or <a href="http://www.google.com/chromeframe/?redirect=true">activate Google Chrome Frame</a> to improve your experience.</p>
        <![endif]-->

        <!-- This code is taken from http://twitter.github.com/bootstrap/examples/hero.html -->

        <div class="navbar navbar-inverse navbar-fixed-top">
            <div class="navbar-inner">
			
                <div class="container">
				
                    <a class="btn btn-navbar" data-toggle="collapse" data-target=".nav-collapse">
                        <span class="icon-bar"></span>
                        <span class="icon-bar"></span>
                        <span class="icon-bar"></span>
                    </a>
					
                    <a class="brand" href="#">KFeed</a>
					
                    <div class="nav-collapse collapse">
					
                        <ul class="nav">
                            <li class="active"><a href="index.php">Home</a></li>
                        </ul>
						
                    </div><!--/.nav-collapse -->
					
					<form method='get' class='navbar-search pull-left' action="<?php echo $_SERVER['PHP_SELF']; ?>" >
						<input type='text' name='s' placeholder='Search for news by keywords...' value='<?php echo @$_GET['s']; ?>' style="width:300px; margin:0px;" />
						<input type='submit' style="margin:0px;" class='btn btn-inverse' value="Search" /> <br/>
					</form>
					
                </div>
            </div>
        </div>
		
		
		<div class="container-fluid" style="height:100%;">
		  <div class="row-fluid" style="height:100%;">

			<div class="span2" >
				<?php
					$link = (!@$_GET['u']) ? '' : $_GET['u'];
					$type = @$_GET['t'];
				  ?>
					<input type='hidden' class='type' value="<?php echo $type; ?>" />
					
					<input type='hidden' class='search' value="<?php echo urlencode(@$_GET['s']); ?>" />
					<input type='hidden' class='view' value="<?php echo urlencode(@$_GET['view']); ?>" />
					
					<div style="margin-bottom:10px; padding-bottom:10px; border-bottom:1px solid #EDEDED; ">
					<div class="btn-group ">
					  <a class="btn btn-small dropdown-toggle" data-toggle="dropdown" href="#">
						<i class='icon-filter'></i>
						<span class="caret"></span>
					  </a>
					  <ul class="dropdown-menu">
						  <li class="<?php echo ($type == 'all' || $type == '') ? 'active' : ''; ?>"><a href='index.php?t=all&u=<?php echo $link; ?>'>All</a></li>
						  <li class="<?php echo ($type == 'news') ? 'active' : ''; ?>"><a href='index.php?t=news&u=<?php echo $link; ?>'>News</a></li>
						  <li class="<?php echo ($type == 'sports') ? 'active' : ''; ?>"><a href='index.php?t=sports&u=<?php echo $link; ?>'>Sports</a></li>
						  <li class="<?php echo ($type == 'tech') ? 'active' : ''; ?>"><a href='index.php?t=tech&u=<?php echo $link; ?>'>Tech</a></li>
					  </ul>
					</div>
					</div>

				<div class="sidebar" >
					
				<?php
					
				$sources = array(array("url" => "http://feeds.wired.com/wired/index", "name" => "Wired"),
						 array("url" => "http://feeds.arstechnica.com/arstechnica/index", "name" => "Ars Technica"),
						 array("url" => "http://feeds.feedburner.com/hackaday/LgoM?format=xml", "name" => "Hackaday"),
						 array("url" => "http://rss.cnn.com/rss/cnn_topstories.rss", "name" => "CNN"),
						 array("url" => "http://feeds.feedburner.com/cnet/tcoc", "name" => "CNET"),
						 array("url" => "http://rss.cnn.com/rss/cnn_tech.rss", "name" => "CNN Tech"),
						 array("url" => "http://www.burlingtonfreepress.com/section/RSS", "name" => "Burlington Free Press"),
						 array("url" => "http://sports.espn.go.com/espn/rss/news", "name" => "ESPN"),
						 array("url" => "http://feeds.nbcnews.com/feeds/topstories", "name" => "NBC News"),
						 array("url" => "http://www.hardocp.com/RSS/all_hardocp.xml", "name" => "HardOCP")
				);
				
				?>
				<ul class="nav nav-pills nav-stacked">
				<?php
				foreach ($sources as $source => $type) {
					?>
						
						<li><a href='index.php?view=<?php echo $type['url']; ?>'> <?php echo $type['name']; ?></a></li>
					
					
					<?php
				}
				?>
				</ul>
				</div>
				
				
			</div>
			
			<div class="span10 main_content">
				
				<i class="icon-spinner icon-spin" style="font-size:20px;"></i> Loading content... 
				
			</div>
			
			<div style="width:100%; margin:0 auto; position:fixed; bottom:0px; height:30px;" class="loading_bottom hide">
				<center><i class="icon-spinner icon-spin" style="font-size:20px;"></i> Loading content... </center>
			</div>
		  </div>
		  
		  
		</div>
		

        <script src="js/vendor/bootstrap.min.js"></script>
    </body>
</html>
