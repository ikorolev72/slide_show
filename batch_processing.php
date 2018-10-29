<?php

$basedir = dirname(__FILE__);
require_once $basedir . DIRECTORY_SEPARATOR . "slide_show_common.php";

#$csvFile = "test.csv";
$csv_processing = "php $basedir" . DIRECTORY_SEPARATOR . "csv_processing.php";
$logoUrl = "http://screenshot.unixpin.com/image2video/uploads/1540752951c5b240475d90daeb5e552f96a92a4f4ef39d07aa/f92aa8e16e80d7d8e02c9c6a0b33948478650483.png";
$audioUrl = "http://screenshot.unixpin.com/image2video/uploads/1540752951c5b240475d90daeb5e552f96a92a4f4ef39d07aa/0f404cbc77773e8e6dfe93022f1e913d2daa583f.mp3";
$splashUrl = "http://screenshot.unixpin.com/image2video/uploads/1540752951c5b240475d90daeb5e552f96a92a4f4ef39d07aa/11a98349fb9e66f12a7a9cd0255ea9822d85ea5a.jpeg";

### read command line parameters
$shortopts = "";
$longopts = array(
    "csv:", // Обязательное значение
    "logo:", // Необязательное значение
    "audio:", // Необязательное значение
    "splash:", // Необязательное значение
);
$options = getopt($shortopts, $longopts);

$csvFile = isset($options['csv']) ? $options['csv'] : '';
$logoUrl = isset($options['logo']) ? $options['logo'] : $logoUrl;
$audioUrl = isset($options['audio']) ? $options['audio'] : $audioUrl;
$splashUrl = isset($options['splash']) ? $options['splash'] : $splashUrl;

if (gettype($csvFile) === "array") {
    foreach ($csvFile as $csv) {
        $cmd = "$csv_processing --csv $csv --logo $logoUrl --audio $audioUrl --splash $splashUrl";
        system($cmd);
    }
}

if (gettype($csvFile) === "string") {
    $csv = $csvFile;
    $cmd = "$csv_processing --csv $csv --logo $logoUrl --audio $audioUrl --splash $splashUrl";
    #echo $cmd;
    system($cmd);
}

if (!$csvFile) {
    help("Need csv file with data. Can be defined with '--csv' option");
}

function help($msg)
{
    $script = basename(__FILE__);
    fwrite(STDERR,
        "$msg
	Usage: $script --csv file.csv [--csv file1.csv [--csv file2.csv...]] [--logo http://logo] [--audio http://audio] [--splash http://splash]
	where:
	--csv file.csv - csv file with data. You can use many csv files with --csv options
	--logo http://logo - url of logo image
	--audio http://audio - url of audio file
	--splash http://splash - url of splash image

	Example: $script --csv data.csv --csv data1.csv --csv data2.csv --logo http://localhost/logo.png
	\n");
    exit(-1);
}
