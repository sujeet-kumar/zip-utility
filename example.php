<?php
require_once './src/Zip.php';

Zip::compress('./dir', './zipfile.zip');

Zip::compress(array('./example.php','./test.php','./zipfile.zip'), './newfilename.zip');

Zip::addComment('./newfilename.zip', 'demo comment');

Zip::extract('./zipfile.zip');

print_r(Zip::getList('./zipfile.zip'));

print_r(Zip::getTree('./zipfile.zip'));

