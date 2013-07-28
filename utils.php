<?php
	function isAValidPhNo($phno) {
		return preg_match("/^[+]?\d{10,12}$/", $phno);
	}

	function isAValidEmail($email) {
		return preg_match("/^[\w\.-]{1,}\@([\da-zA-Z-]{1,}\.){1,}[\da-zA-Z-]+$/", $email);
	}

	function str_rand($length = 20, $seeds = 'alphanum') {
		$seedings['alphanum'] = 'abcdefghijklmnopqrstuvwqyz0123456789';

		// Choose seed
		if (isset($seedings[$seeds])) {
			$seeds = $seedings[$seeds];
		}

		// Seed generator
		list($usec, $sec) = explode(' ', microtime());
		$seed = (float) $sec + ((float) $usec * 100000);
		mt_srand($seed);

		// Generate
		$str = '';
		$seeds_count = strlen($seeds);

		for ($i = 0; $length > $i; $i++) {
			$str .= $seeds{mt_rand(0, $seeds_count - 1)};
		}
		return $str;
	}
?>