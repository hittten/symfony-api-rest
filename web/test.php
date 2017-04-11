<?php
$fp = fopen('data.txt', 'a');
fwrite($fp, print_r(['POST' => $_POST, 'GET' => $_GET, 'COOKIE' => $_COOKIE, 'SESSION' => $_SESSION, 'SERVER' => $_SERVER], true));
fclose($fp);
