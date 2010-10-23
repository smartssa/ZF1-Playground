<p>Edit the params with data for your account.</p>

<pre>
<?php

// Edit these.
$url       = 'http://www1.sexsearchcom.com/upgrade';
$userPass  = '662615';
$profileId = 52730;
$accountId = 47474;
$userEmail = 'test123@hotmail.com';


function generateAlk($userPass, $profileId, $accountId, $userEmail) {
	return "?altoken=" . $profileId. ':' . $accountId . ':' . md5(	
		'4d66ff8ff6a5745cd2ca3c437e248d15' . // salt from accounts api 
		(string)$userPass .
		(int)$accountId   .
		(string)$userEmail
	);
}

function maildispatchUrl($url, $extra = null) {
	if ($extra !== null) {
		$data = $extra . ":" . $url;
	} else {
		$data = $url;
	}
	return "http://md3.sexsearchcom.com/md3.php/type=3/t=code/u=" . base64_encode($data);	
}

$destiny = $url . generateAlk($userPass, $profileId, $accountId, $userEmail);
$md      = maildispatchUrl($destiny);


echo 'format: profile_id :  account id : magic key <br/>';

echo "\ntoken for: " . $userEmail;
echo "\n" . $destiny;

echo "\nMail dispatch url:";
echo "\n" . $md;

?>
</pre>
