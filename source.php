<?php
session_start();

if (empty($_SESSION['list']))
	$new = "";
else
	$new = "<i class='icon-star icon-spin' style='color:#63B82A;'></i>";
	
if (@$_GET['s']) {
	$sources = array(array("url" => "http://news.google.com/news?q=" . $_GET['s'] . "&output=rss", "type" => "tech"));
}
else {
	$sources = array(array("url" => "http://feeds.wired.com/wired/index", "type" => "tech"),
				 array("url" => "http://feeds.arstechnica.com/arstechnica/index", "type" => "tech"),
				 array("url" => "http://feeds.feedburner.com/hackaday/LgoM?format=xml", "type" => "tech"),
				 array("url" => "http://rss.cnn.com/rss/cnn_topstories.rss", "type" => "news"),
				 array("url" => "http://feeds.feedburner.com/cnet/tcoc", "type" => "tech"),
				 array("url" => "http://rss.cnn.com/rss/cnn_tech.rss", "type" => "tech"),
				 array("url" => "http://www.burlingtonfreepress.com/section/RSS", "type" => "news"),
				 array("url" => "http://sports.espn.go.com/espn/rss/news", "type" => "sports"),
				 array("url" => "http://feeds.nbcnews.com/feeds/topstories", "type" => "news"),
				 array("url" => "http://www.hardocp.com/RSS/all_hardocp.xml", "type" => "tech")
		);
}

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

$array_size = sizeof($sources);

$lt = generateListNum($array_size);

if (@$_GET['s'])
	$lt = 6;
else
	$lt = 2;


foreach ($sources as $source => $type) 
{

	$content = file_get_contents($type['url']);  
	$x = new SimpleXmlElement($content);  
	
	$rand = array('0', '1', '2', '3', '4', '5', '6', '7', '8', '9', 'a', 'b', 'c', 'd', 'e', 'f');
	$color = '#'.$rand[rand(0,15)].$rand[rand(0,15)].$rand[rand(0,15)].$rand[rand(0,15)].$rand[rand(0,15)].$rand[rand(0,15)];

	$c = 0;
	
	foreach($x->channel->item as $entry) {  
		
		if ($c < $lt) {
		

		if (in_array((string)$entry->title, $_SESSION['list'])) 
		{
		}
		else 
		{
			$title = $entry->title;
			
			$_SESSION['list'][] = (string)$title;
			
			
				if (@$_GET['s']) {
				?>
                	<div class='container_item' style="width:44%; margin: 5px; margin-bottom:10px; float:left; background-color:#F2F2F2; border:1px solid #E0E0E0; padding:12px;">
                <?php
				}
				else {
				?>
                	<div class='container_item' style="width:240px; margin: 5px; float:left; background-color:#F2F2F2; border:1px solid #E0E0E0; padding:12px;">
                <?php
				}

				    echo $new; ?> <b style="color:<?php echo $color; ?>"><?php echo $x->channel->title; ?>
					</b> <br/>
					<?php
						/*
						$html = file_get_contents($entry->guid);  
						$doc = new DOMDocument();
						@$doc->loadHTML($html);

						$tags = $doc->getElementsByTagName('img');

						foreach ($tags as $tag) {
								
							   echo "<img src='" . $tag->getAttribute('src') . "' /><br/>";
						}
						*/
					?>
					<a href='<?php echo $entry->guid; ?>' target='_blank'><?php echo $entry->title; ?></a><br/>
					<?php echo $entry->description; ?><br/>
					
					<i style='font-size:10px;'><?php echo str_replace("+0000", "", $entry->pubDate); ?></i> <br/>
					
					
					<?php
					
						$twitterLink = "http://twitter.com/share?text=" . $entry->title . "&url=" .urlencode($entry->guid);
						$facebookLink = "http://www.facebook.com/sharer.php?u=" . urlencode($entry->guid);
					?>
					
					<a href="" class='share' target="_blank"><i class='icon-thumbs-up'></i>  </a>
					<a href='<?php echo $facebookLink; ?>' class='share' target="_blank"><i class='icon-facebook'></i>   </a> 
					<a href="<?php echo $twitterLink; ?>" class='share' target="_blank"><i class='icon-twitter'></i>  </a>
				</div>
				<?php
				
			}
		}
		$c++;
	}
}


function generateListNum($size) 
{
	switch($size) 
	{
		case 1:
			return 4;
		break;
		case 2:
			return 3;
		break;
		case 3:
			return 2;
		break;
		case 4:
			return 2;
		break;
		case 5:
			return 1;
		break;
		default:
			return 1;
		break;
		
	
	}

}

?>