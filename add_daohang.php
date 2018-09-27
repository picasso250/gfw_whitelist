<?php

/**
 * 将导航网站中的所有网址加入白名单
 * added:
 * 265, 360, 2345, hao123, sougou
 */

// parse file
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

// fetch html
foreach(glob("*.html") as $html_file) {
    echo "$html_file\n";
    $html = file_get_contents($html_file);
    // echo "$html\n";
    if (!preg_match_all('/href="(http[^"]+)"/', $html, $m)) {
        echo "\tno content\n";
        continue;
    }
    foreach($m[1] as $href){
        $a = parse_url($href);
        if(!isset($a['host'])) {
            echo "$href has no host\n";
            continue;
        }
        $host = $a['host'];
        $a = explode(".", $host);
        $y = $a[count($a)-2];
        $z = $a[count($a)-1];
        if($z=='cn') continue;
        if(!isset($j[$z][$y])) {
            echo "\tadd $y.$z\n";
            $j[$z][$y]=1;
            ksort($j[$z], SORT_STRING);
        }
    }
}

// save back
$j_=json_encode($j, JSON_PRETTY_PRINT);
$c = preg_replace($pat, "var white_domains = $j_;", $c, 1);
file_put_contents($file, $c);