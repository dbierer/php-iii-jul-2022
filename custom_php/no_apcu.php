<?php
echo '<a href="index.html">BACK</a>';
echo "<pre>\n";
$start = microtime(TRUE);
$data  = NULL;
echo "Generating prime numbers from 1 to 1000\n";
include __DIR__ . '/../async/src/App/Number/Prime.php';
$data = '';
foreach (\App\Number\Prime::generate(1000, 9999) as $num) $data .= $num . ' ';
$diff = microtime(TRUE) - $start;
printf("Elapsed Time: %8.12f seconds\n", $diff);
echo "Test Data\n";
echo $data . "\n";
echo "</pre>\n";
echo '<a href="index.html">BACK</a>';
