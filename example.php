<?php
require_once 'Zip.php';

Zip::compress('./dir', './dirc1.zip');

Zip::compress(array('./example.php','./zip.php','./dirc1.zip'), './newfilename.zip');

Zip::extract('./dirc1.zip');

print_r(Zip::getList('./dirc1.zip'));

print_r(Zip::getTree('./dirc1.zip'));

