<?php
session_start();

if ($_SESSION['list'] == '')
	$new = "<b><font style='color:green;'> New! </b>";
else
	$new = "";

$sources = array("http://feeds.wired.com/wired/index", 
				"http://feeds.arstechnica.com/arstechnica/index", 
				"http://feeds.feedburner.com/hackaday/LgoM?format=xml",
				"http://rss.cnn.com/rss/cnn_topstories.rss",
				"http://rss.cnn.com/rss/cnn_tech.rss",
				"http://www.burlingtonfreepress.com/section/RSS");
				
foreach ($sources as $source) 
{

	$content = file_get_contents($source);  
	$x = new SimpleXmlElement($content);  
	
	$rand = array('0', '1', '2', '3', '4', '5', '6', '7', '8', '9', 'a', 'b', 'c', 'd', 'e', 'f');
	$color = '#'.$rand[rand(0,15)].$rand[rand(0,15)].$rand[rand(0,15)].$rand[rand(0,15)].$rand[rand(0,15)].$rand[rand(0,15)];
	
	$c = 0;
	
	foreach($x->channel->item as $entry) {  
		
		if ($c < 1) {
		

		if (in_array((string)$entry->title, $_SESSION['list'])) 
		{
		}
		else 
		{
			$title = $entry->title;
			
			$_SESSION['list'][] =  (string)$title;
				
				?>
					<?php echo $new; ?><b style="color:<?php echo $color; ?>"><?php echo $x->channel->title; ?></b> <br/>
					<a href='index.php?u=<?php echo $entry->guid; ?>'><?php echo $entry->title; ?></a><br/>
					<i style='font-size:10px;'><?php echo str_replace("+0000", "", $entry->pubDate); ?></i>
					<hr>
				<?php
				
			}
		}
		$c++;
	}
}
?>