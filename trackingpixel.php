<?php
/*
 * Tracks current pageview to database and displays empty pixel
 * FIXME:
 *   - Check referer
 */
require_once('../../../wp-load.php');

/*
 * send empty image file
 */
function show_pixelstats_image() {
	$empty = dirname(__FILE__)."/empty.gif";
	header("Content-Type: image/gif");
	header("Content-Length: ".filesize($empty));
	$fp = fopen($empty, "r");
	fpassthru($fp);
	fclose($fp);
	
}

/*
 * Write stats to database
 */
function write_pixelstats() {
	global $wpdb;
	$token = substr(md5(sha1(crc32(md5(base64_decode(microtime())).microtime()))), 0, 32);
	if (!empty($_REQUEST)) {
		$wpdb->query("INSERT INTO ".$wpdb->prefix."pixelstats VALUES (\"".$_REQUEST['post_id']."\", now(), \"".get_visitor_id()."\")");
	}	
}

/*
 * Get visitor id from cookie or generate new and write to cookie
 */
function get_visitor_id() {
	
	// FIXME: Why does it generate a different ID for the first view?
	if(empty($_COOKIE['pixelstats_visitor_id'])) {
		$visitor_id = substr(md5(sha1(crc32(md5(base64_decode(microtime())).microtime()))), 0, 32);
	} else {
		$visitor_id = $_COOKIE['pixelstats_visitor_id'];
	}
	
	return $visitor_id;
}

// This might help a bit to keep robots away
// Deactivated because it also keeps google readers away
//if(isset($_SERVER['HTTP_REFERER']) && preg_match("/".$_SERVER['HTTP_HOST']."/", $_SERVER['HTTP_REFERER']) != 0) write_pixelstats();
write_pixelstats();
//if(isset($_SERVER['HTTP_REFERER'])) write_pixelstats();
show_pixelstats_image();


?>