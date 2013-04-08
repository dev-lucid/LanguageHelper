<?php
global $output_path;

$out = lng::helloworld_variant_test1()."\n";
$out .= lng::helloworld_variant_test2();
file_put_contents($output_path,$out);

?>