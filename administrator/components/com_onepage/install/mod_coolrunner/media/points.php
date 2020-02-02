<?php
header('Content-Type: application/json');
echo file_get_contents('http://coolrunner.dk/media/droppoints/all_droppoints.json'); 