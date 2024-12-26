<?php
$dirPath = (dirname(__DIR__));
echo $dirPath . "/assets/txt/webhooks/config.txt";
$file_put_content = file_put_contents($dirPath . "/assets/txt/webhooks/config.txt", 'abcd');
