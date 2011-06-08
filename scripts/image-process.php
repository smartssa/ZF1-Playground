<?php
require_once 'cli-common.php';
require_once "Embedly/Embedly.php";

// where to store originals
$original_path    = APPLICATION_PATH . 'public/images/o/';

// where to put processed files
$destination_path = APPLICATION_PATH . 'public/images/i/';

// get the default adapter from the common
$db = Zend_Db_Table::getDefaultAdapter();


/**
 * @var Embedly
 */
$embedly = new Embedly\Embedly(array('user_agent' => 'Mozilla/5.0 (compatible; embedly/example-app; support@embed.ly)'));

/**
 * @var Zend_Db_Statement_Pdo
 */
$result = null;
$client = new Zend_Http_Client();
$max    = 20; // maximum wait time

while (true) {
	// read from tweet_raw
	$sql = 'SELECT id, tweet_id, tweet_user_id, text, created FROM tweet_raw WHERE processed = 0 LIMIT ' . $max . ';';
	// process any images, download them locally

	$result = $db->query($sql);

	$rowset = $result->fetchAll();
	$total  = 0;
	$ids    = array();

	foreach ($rowset as $key => $row) {
		$total++;
		/** expand any short urls inline **/
		$urlRegex = '@https?://([-\w\.]+)+(:\d+)?(/([\w/_\.]*(\?\S+)?)?)?@i';
		preg_match($urlRegex, $row['text'], $matches);
		if (isset($matches[0])) {
			$testurl = $matches[0];
			// unshorten madness
			// http://unshort.me/api.html
			$url = sprintf('http://api.unshort.me/?r=%s&t=json', urlencode($testurl));
			$client->setUri($url);
			try {
				$result = $client->request();
			} catch (Exception $e) {
				// url request failed, we'll try again next loop.
				continue;
			}
			$body   = json_decode($result->getBody());

			if ($body->success == 'true') {
				// destroy url
				$cleanText = str_replace($testurl, '', $row['text']);
				$url 	   = $body->resolvedURL;
			} else {
				$url 	   = $testurl;
			}

			// usernames /[@]+[A-Za-z0-9-_]+/g
			$cleanText = preg_replace('/[@]+[A-Za-z0-9-_]+/i', '', $cleanText);
			// hashtags /[#]+[A-Za-z0-9-_]+/g
			$cleanText = preg_replace('/[#]+[A-Za-z0-9-_]+/i', '', $cleanText);
			// strip any other URLs
			$cleanText = preg_replace($urlRegex, '', $cleanText);
			// strip any retarded spaces
			$cleanText = trim($cleanText);

			/** service specific **/
			// detect which image service we're using
			// twitpic stuff http://twitpic.com/show/full/58aiug
			// yfrog : http://yfrog.com/0fh5rvw4j:iphone
			// lockerz : http://api.plixi.com/api/tpapi.svc/photos/73832124 - api request to get url for image
			// twitgoo : http://twitgoo.com/2bumhy/img
			// flickr : http://flic.kr/p/{base58-photo-id} (more: http://www.flickr.com/groups/api/discuss/72157616713786392/)
			// img.ly : ??
			// instagr.am : http://api.instagram.com/oembed?url=http://instagr.am/p/FWJ-V/ - json oembed
			// ... uh, hey I just found embed.ly! that takes care of them all.
			try {
				$oembed    = $embedly->oembed($url);
			} catch (Exception $e) {
				// useless.
			}
			if (isset($oembed->url)) {
				$imageName = $oembed->url;
				/** common **/
				// create unique awesome name that no one can decode for local storage
				// download image locally
				// run them through a fancy imagemagick filter and title them with words from the tweet
				// strip out hashtags, users and urls from text
				// apply graphic filters to image
				// apply leftover text to the image
				// insert into the stream table
				// enable them in the stream table
				try {
					$sql = 'INSERT INTO image_stream (text, imagename, enabled, modified, created) VALUES (?, ?, 1, NOW(), NOW())';
					$bind = array($cleanText, $imageName);
					$db->query($sql, $bind);
					echo '.';
				} catch (Exception $e) {
					echo 'e';
				}
			} else {
				// throw away, no image url
				echo 't';
			}
		} else {
			// There was NO Url in the text.
			echo 'o';
		}
		$ids[] = $row['id'];
	}
	// delete the raw data
	if (count($ids) > 0) {
		echo 'x';
		$sql = 'UPDATE tweet_raw SET processed = 1 WHERE id IN (' . implode(',', $ids) . ')';
		$db->query($sql);
	}

	sleep($max - $total + 1);
}