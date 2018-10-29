<?php

$basedir = dirname(__FILE__);
require_once $basedir . DIRECTORY_SEPARATOR . "slide_show_common.php";

#$csvFile = "test.csv";
$logoUrl = "http://screenshot.unixpin.com/image2video/uploads/1540752951c5b240475d90daeb5e552f96a92a4f4ef39d07aa/f92aa8e16e80d7d8e02c9c6a0b33948478650483.png";
$audioUrl = "http://screenshot.unixpin.com/image2video/uploads/1540752951c5b240475d90daeb5e552f96a92a4f4ef39d07aa/0f404cbc77773e8e6dfe93022f1e913d2daa583f.mp3";
$splashUrl = "http://screenshot.unixpin.com/image2video/uploads/1540752951c5b240475d90daeb5e552f96a92a4f4ef39d07aa/11a98349fb9e66f12a7a9cd0255ea9822d85ea5a.jpeg";

### read command line parameters
$shortopts  = "";
$longopts  = array(
  "csv:",     // Обязательное значение
  "logo:",    // Необязательное значение
  "audio:",    // Необязательное значение
  "splash:",    // Необязательное значение
);
$options = getopt($shortopts, $longopts);
$csvFile=isset($options['csv']) ? $options['csv'] : '';
$logoUrl = isset($options['logo']) ? $options['logo'] : $logoUrl;
$audioUrl = isset($options['audio']) ? $options['audio'] : $audioUrl;
$splashUrl = isset($options['splash']) ? $options['splash'] : $splashUrl;

if( !$csvFile ) {
  help("Need csv file with data. Can be defined with '--csv' option");
}




$mainiUrl = 'http://screenshot.unixpin.com/image2video';
$apiUrl = "$mainiUrl/api";
$apiKey = '315820';
$dt = date("U");

if (!file_exists($csvFile)) {
    echo "File '$csvFile' do not exists";
    exit(1);
}

$dataArray = getCsv($csvFile);
if (!$dataArray) {
    echo "Cannot read file '$csvFile' or file have incorrect format";
    exit(1);
}

########################################################################
# 1. start project
#
$step = 1;
echo "Step $step. Add new project\n";
$answer = addProject($apiUrl, $apiKey, $step, "new project $dt");
if (!$answer) {
    exit(1);
}
$project_id = $answer["project_id"];
#echo "New project added : $project_id\n";

$step++;
echo "Step $step. Adding logo\n";
$answer = addLogo($apiUrl, $apiKey, $project_id, $step, $logoUrl);
if (!$answer) {
    exit(1);
}

$step++;
echo "Step $step. Adding audio\n";
$answer = addAudio($apiUrl, $apiKey, $project_id, $step, $audioUrl);
if (!$answer) {
    exit(1);
}

$id = 1;
$foundEmptyLine = false;
foreach ($dataArray as $data) {
    // looking for empty first cell
    if (trim($data[0]) === "Title") {
        $title= $data[1];
        continue;
    }
    if (trim($data[0]) === "Main Image") {
      $mainImage= $data[1];
      continue;
    }

    if (trim($data[0]) === "") { // empty line
        $foundEmptyLine = true;
        $step++;
        echo "Step $step. Adding Main Image\n";
        $answer = addImage($apiUrl, $apiKey, $project_id, $step, $mainImage );
        if (!$answer) {
            exit(1);
        }
        $step++;
        echo "Step $step. Adding title\n";
        $answer = addText($apiUrl, $apiKey, $project_id, $id, 4, 'fade', $step, strip_tags($title), 'center', 50 );
        if (!$answer) {
            exit(1);
        }
        $id++;
        continue;
    }
    if (trim($data[0]) === "Unit caption 1") {
        // skip this line
        continue;
    }
    if (!$foundEmptyLine) {
        continue;
    }

    $row = array();
    $row["Caption1"] = $data[0];
    $row["Caption2"] = $data[1];
    $row["SubCaption1"] = $data[2];
    $row["SubCaption2"] = $data[3];
    $row["UnitImage1"] = $data[4];
    $row["UnitImageSource1"] = $data[5];
    $row["UnitImageSourceUrl1"] = $data[6];
    $row["UnitImage2"] = $data[7];
    $row["UnitImageSource2"] = $data[8];
    $row["UnitImageSourceUrl2"] = $data[9];

########################################################################

    if ($row["Caption1"]) {
        $step++;
        echo "Step $step. Adding image " . $row["Caption1"] . "\n";
        $answer = addImage($apiUrl, $apiKey, $project_id, $step, $row["UnitImage1"]);
        if (!$answer) {
            exit(1);
        }
        $step++;
        echo "Step $step. Adding text for " . $row["Caption1"] . "\n";
        $answer = addText($apiUrl, $apiKey, $project_id, $id, 4, 'fade', $step, strip_tags($row["Caption1"]), 'center', 50 );
        if (!$answer) {
            exit(1);
        }
        $id++;

        $step++;
        echo "Step $step. Adding image " . $row["Caption1"] . "\n";
        $answer = addImage($apiUrl, $apiKey, $project_id, $step, $row["UnitImage1"]);
        if (!$answer) {
            exit(1);
        }
        $step++;
        echo "Step $step. Adding text for " . $row["Caption1"] . "\n";
        $answer = addText($apiUrl, $apiKey, $project_id, $id, 8, 'concat', $step, strip_tags($row["SubCaption1"]));
        if (!$answer) {
            exit(1);
        }
        $id++;
    }
}

### add spalsh in the end
$step++;
echo "Step $step. Adding image Splash\n";
$answer = addImage($apiUrl, $apiKey, $project_id, $step, $splashUrl);
if (!$answer) {
    exit(1);
}
$step++;
echo "Step $step. Adding effects for Spalsh\n";
$answer = addText($apiUrl, $apiKey, $project_id, $id, 4, 'fade', $step, "");
if (!$answer) {
    exit(1);
}
$id++;

### prepare video command
$step++;
echo "Step $step. Prepare video command script\n";
$answer = prepareVideo($apiUrl, $apiKey, $project_id, $step);
if (!$answer) {
    exit(1);
}
$videoFileName = $answer["rows"][1]["name"];
$videoFileUrl = $answer["rows"][1]["url"];

### shedule video processing
$step++;
echo "Step $step. Shedule video task\n";
$answer = sheduleVideo($apiUrl, $apiKey, $project_id, $step);
if (!$answer) {
    exit(1);
}
$task_id = $answer["task_id"];

########################################################################
# 8. Waiting while video will be done
#
$step++;
echo "Step $step. Waiting while video will be done\n";
$waitFor = 240; // max time 2 hours
$sleepFor = 30;
for ($i = 0;; $i++) {
    $answer = getTaskStatus($apiUrl, $apiKey, $project_id, $task_id, $step);
    $status = $answer["rows"][0]["status"];

    if ("finished" === $status) {
        break;
    }

    if ("failed" === $status) {
        fwrite(STDERR, "Step $step. Error. Shedulled command for task $task_id failed\n");
        exit(1);
    }
    if ($i > $waitFor) {
        fwrite(STDERR, "Step $step. Error. The shedulled task $task_id do not finished during " . ($waitFor * $sleepFor) / 60 . " minutes. You can check this task status by hand\n");
        exit(1);
    }
    sleep($sleepFor);
    echo "Waiting while sheduled command finished. Status now: $status\n";
}

echo "Your video is ready.\n";
echo "Filename: $videoFileName\n";
echo "Url: $mainiUrl/$videoFileName\n";
exit(0);


function help($msg ) {
	$script=basename(__FILE__) ;
	fwrite(STDERR, 
	"$msg
	Usage: $script --csv file.csv [--logo http://logo] [--audio http://audio] [--splash http://splash]
	where:
	--csv file.csv - csv file with data
	--logo http://logo - url of logo image 
	--audio http://audio - url of audio file
	--splash http://splash - url of splash image

	Example: $script --csv data.csv --logo http://localhost/logo.png  
	\n");	
	exit(-1);
}