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
							<!--
                            <li><a href="#about">About</a></li>
                            <li><a href="#contact">Contact</a></li>
                            <li class="dropdown">
                                <a href="#" class="dropdown-toggle" data-toggle="dropdown">Dropdown <b class="caret"></b></a>
                                <ul class="dropdown-menu">
                                    <li><a href="#">Action</a></li>
                                    <li><a href="#">Another action</a></li>
                                    <li><a href="#">Something else here</a></li>
                                    <li class="divider"></li>
                                    <li class="nav-header">Nav header</li>
                                    <li><a href="#">Separated link</a></li>
                                    <li><a href="#">One more separated link</a></li>
                                </ul>
                            </li>
							-->
                        </ul>
						<!--
                        <form class="navbar-form pull-right">
                            <input class="span2" type="text" placeholder="Email">
                            <input class="span2" type="password" placeholder="Password">
                            <button type="submit" class="btn">Sign in</button>
                        </form>
						-->
                    </div><!--/.nav-collapse -->
					
					
					
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
					
					<div style="margin-bottom:10px; padding-bottom:10px; border-bottom:1px solid #EDEDED; ">
					<div class="btn-group ">
					  <a class="btn btn-small dropdown-toggle" data-toggle="dropdown" href="#">
						<i class='icon-filter'></i>
						<span class="caret"></span>
					  </a>
					  <ul class="dropdown-menu">
						  <li class="<?php echo ($type == 'all' || $type == '') ? 'active' : ''; ?>"><a href='index.php?t=all&u=<?php echo $link; ?>'>All</a></li>
						  <li class="<?php echo ($type == 'news') ? 'active' : ''; ?>"><a href='index.php?t=news&u=<?php echo $link; ?>'>News</a></li>
						  <li class=" <?php echo ($type == 'sports') ? 'active' : ''; ?>"><a href='index.php?t=sports&u=<?php echo $link; ?>'>Sports</a></li>
						  <li class="<?php echo ($type == 'tech') ? 'active' : ''; ?>"><a href='index.php?t=tech&u=<?php echo $link; ?>'>Tech</a></li>
					  </ul>
					</div>
					</div>

				<div class="sidebar" >
					<!--Sidebar content-->
					<i class="icon-spinner icon-spin"></i> Loading content...
				</div>
			</div>
			
			
			<div class="span10 main_content">
				  <!--Body content-->
				  <?php
					if (@$_GET['u']){
						?><iframe class='rss_content' src="<?php echo @$_GET['u']; ?>" style="width:100%; margin:-10px;" frameborder='0'></iframe><?php
					}
					else {
					
					
					?>
						<div class="row-fluid">
					<?php
							
								

								$sources = array(array("url" => "http://feeds.wired.com/wired/index", "type" => "tech"),
												 array("url" => "http://feeds.arstechnica.com/arstechnica/index", "type" => "tech"),
												 array("url" => "http://feeds.feedburner.com/hackaday/LgoM?format=xml", "type" => "tech"),
												 array("url" => "http://rss.cnn.com/rss/cnn_topstories.rss", "type" => "news"),
												 array("url" => "http://rss.cnn.com/rss/cnn_tech.rss", "type" => "tech"),
												 array("url" => "http://www.burlingtonfreepress.com/section/RSS", "type" => "news"),
												 array("url" => "http://sports.espn.go.com/espn/rss/news", "type" => "sports"));
									
								$selection_type = @$_GET['t'];


								if ($selection_type != '') {
									if ($selection_type != 'all') 
									{

										foreach ($sources as $source => $type) {
											
											if ($selection_type != $type['type']) 
											{
												unset($sources[$source]);
											}
										}
									}
								}

								foreach ($sources as $source => $type) 
								{

									$content = file_get_contents($type['url']);  
									$x = new SimpleXmlElement($content);  
									
									$rand = array('0', '1', '2', '3', '4', '5', '6', '7', '8', '9', 'a', 'b', 'c', 'd', 'e', 'f');
									$color = '#'.$rand[rand(0,15)].$rand[rand(0,15)].$rand[rand(0,15)].$rand[rand(0,15)].$rand[rand(0,15)].$rand[rand(0,15)];

									foreach($x->channel->item as $entry) {
											$title = $entry->title;
												?>
													<div style="margin:10px; float:left; width:250px; height:200px;">
													<h3><b style="color:<?php echo $color; ?>"><?php echo $x->channel->title; ?></b></h3>
													<p>
													<h4><a href='index.php?t=<?php echo $selection_type; ?>&u=<?php echo $entry->guid; ?>'><?php echo $entry->title; ?></a></h4>
													<i style='font-size:10px;'><?php echo str_replace("+0000", "", $entry->pubDate); ?></i> <br/>
													
													
													<a href='' class='share'><i class='icon-thumbs-up'></i>  </a>
													<a href='' class='share'><i class='icon-facebook'></i>   </a>
													<a href='' class='share'><i class='icon-twitter'></i> </a> 
													</p>
													
													</div>
												<?php
												
											
										
									}
								}
							?></div><?php
					}
				  ?>
			</div>
			
			
		  </div>
		</div>

        <script src="//ajax.googleapis.com/ajax/libs/jquery/1.8.3/jquery.js"></script>
        <script>window.jQuery || document.write('<script src="js/vendor/jquery-1.8.3.js"><\/script>')</script>
		<script src="js/jquery.mCustomScrollbar.js"></script>
		<script >
		$(function() {
			
			//$(".entry_link").click(function () {
				//var location = $(this).attr("link_location");
				//alert(location);
				//$(".rss_content").load("localhost/rss/index.php?u=http://www.google.com);
			//});
			
			var height = $(window).height();
		
			
			//$(".main_content").css('margin-left', sidebar_width + 40);
			
			$(".sidebar").mCustomScrollbar({
				scrollButtons:{
					enable:true
				}
			});

			
			var type = $(".type").val();
			$(".sidebar").load("source.php?t=" + type);
  
  
			setInterval(function() {
				
				$(".loading").show();
				
				$.get('source.php?t=' + type, function(data){ 
				  $(".sidebar").prepend(data);
				});
				
				$(".loading").hide();
			}, 15000);

			$('.rss_content').css('height', height * 5);

		});
		
		</script>

        <script src="js/vendor/bootstrap.min.js"></script>
    </body>
</html>
