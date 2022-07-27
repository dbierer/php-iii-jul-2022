<?php
echo "<pre>\n";
$key = 'test';
$ttl = 300;	// 5 minutes
if (apcu_exists($key)) {
	echo "Key $key exists\n";
	$test = apcu_fetch($key);
} else {
	echo "Key $key does not exist\n";
	$test = date('Y-m-d');
	if (apcu_store($key, $test, $ttl)) {
		echo "Key $key stored in cache\n";
	} else {
		echo "Unable to store key $key stored in cache\n";
	}
}
echo "Current DateTime: " . date('Y-m-d H:i:s');
echo "\n";
echo "Cached DateTime : $test\n";
var_dump(apcu_cache_info());
echo "</pre>\n";

