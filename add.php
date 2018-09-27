<?php

// add site

if(!isset($argv[1])) {
    echo "Usage: $argv[0] <site_url>\n";
    exit(1);
}
$site_url = $argv[1];
$a = parse_url($site_url);
if(!isset($a['host'])) {
    echo "$site_url has no host\n";
    exit(1);
}
$host = $a['host'];
$a = explode(".", $host);
$y = $a[count($a)-2];
$z = $a[count($a)-1];
echo "add $y.$z\n";

$lines = [];
$file = "whitelist.pac";
if(!is_file($file)) {
    echo "no file $file\n";
    exit(1);
}
$c = file_get_contents($file);
$pat = '/var white_domains = (\{.+?\});/s';
if (!preg_match($pat, $c, $m)) {
    echo "not good file\n";
    exit(1);
}
$j = json_decode($m[1], true);
if (json_last_error()) {
    echo json_last_error_msg(),PHP_EOL;
    exit(1);
}
$j[$z][$y]=1;
ksort($j[$z], SORT_STRING);
$j_=json_encode($j, JSON_PRETTY_PRINT);
$c = preg_replace($pat, "var white_domains = $j_;", $c, 1);
file_put_contents($file, $c);