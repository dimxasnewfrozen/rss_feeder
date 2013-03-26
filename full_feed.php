<?php

/* the namespace of rss "content" */
$content_ns = "http://purl.org/rss/1.0/modules/content/";

/* load the file */
$rss = file_get_contents("http://www.burlingtonfreepress.com/section/RSS");
/* create SimpleXML object */
$xml = new SimpleXMLElement($rss);
$root=$xml->channel; /* our root element */

foreach($root->item as $item) { /* loop over every item in the channel */
   // print "Description: <br>".$item->description."<br><br>";
	print "<div>";
    foreach($item->children($content_ns) as $content_node) {
        /* loop over all children in the "content" namespace */
        print $content_node."\n";
    }
    print "</div>";
}
	
?>