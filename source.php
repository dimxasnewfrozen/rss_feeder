<?php
session_start();

if (empty($_SESSION['list']))
	$new = "";
else
	$new = "<i class='icon-star icon-spin' style='color:#63B82A;'></i>";
	

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

$array_size = sizeof($sources);
$lt = generateListNum($array_size);

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
				
				?>
					<?php echo $new; ?> <b style="color:<?php echo $color; ?>"><?php echo $x->channel->title; ?></b> <br/>
					<a href='index.php?t=<?php echo $selection_type; ?>&u=<?php echo $entry->guid; ?>'><?php echo $entry->title; ?></a><br/>
					<i style='font-size:10px;'><?php echo str_replace("+0000", "", $entry->pubDate); ?></i> <br/>
					
					
					<a href='' class='share'><i class='icon-thumbs-up'></i>  </a>
					<a href='' class='share'><i class='icon-facebook'></i>   </a>
					<a href='' class='share'><i class='icon-twitter'></i> </a> 

					
					<hr style="margin-top:5px;">
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