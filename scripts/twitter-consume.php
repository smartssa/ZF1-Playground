<?php
/* common application things (db, etc) */
require_once 'cli-common.php';

/**
 * a groovy script to consume some twitter things.
 *
 * fairly procedural... uses elements of zend library and phirehose
 *
 */


// words to track
$words = array('summer');

// setup any data objects that may or may not be required.

/**
 * Example of using Phirehose to display a live filtered stream using track words
 */
class FilterTrackConsumer extends Phirehose_OauthPhirehose
{

	/**
	 * Enter description here ...
	 * @var Zend_Db_Adapter_Abstract
	 */
	protected $_db;

	public function __construct($username, $password, $method, $format)
	{
		$this->_db = Zend_Db_Table::getDefaultAdapter();
		parent::__construct($username, $password, $method, $format);
	}
	/**
	 * Enqueue each status
	 *
	 * @param string $status
	 */
	public function enqueueStatus($status)
	{

		$data = json_decode($status);
		if (is_object($data) && isset($data->user->screen_name)) {
			// check for images!
			if (strstr($data->text, 'http://')) {
				// save anything with a link, the processor will figure out the rest.
				// print $data->user->screen_name . ': ' . urldecode($data->text) . "\n";
				$tweet_id  = $data->id;
				$user_id   = $data->user->id;
				$text      = $data->text;
				$search_id = 0;
				$sql = "INSERT INTO tweet_raw (tweet_id, tweet_user_id, search_id, text, created)
                                VALUES(?, ?, ?, ?, NOW())";
				try {
					$this->_db->query($sql, array($tweet_id, $user_id, $search_id, $text));
					echo '.';
				} catch (Exception $e) {
					// probably a dupe.
					echo 'e';
				}
			}
		}
	}

	public function checkFilterPredicates() {
		// change the keywords, if needed.
	}
}

// The OAuth credentials you received when registering your app at Twitter
define("TWITTER_CONSUMER_KEY", "R7Qp8nsjhFDXeHQc4OF7hw");
define("TWITTER_CONSUMER_SECRET", "kax7wr8mS0yFdtp0nr1SevK1sRBGbaSpAbQk3MkU");


// The OAuth data for the twitter account
define("OAUTH_TOKEN", "15093334-3wgbITUApi7ARD6gMxGbAaVgpaNhBqZkaevh628ms");
define("OAUTH_SECRET", "x63sVqIwKvKATYJcXIPW7FkFdoQpltgbOZha2QCJE");

// Start streaming
$sc = new FilterTrackConsumer(OAUTH_TOKEN, OAUTH_SECRET, Phirehose_Phirehose::METHOD_FILTER, Phirehose_Phirehose::FORMAT_JSON);
$sc->setTrack($words);
$sc->consume();


