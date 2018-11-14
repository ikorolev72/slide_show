<?php

$basedir = dirname(__FILE__);
require_once $basedir . DIRECTORY_SEPARATOR . "slide_show_common.php";

$logoUrl = "http://ec2-54-212-58-90.us-west-2.compute.amazonaws.com/image2video/uploads/15417092127723a243f0e122a400c54eaf6a9bda7c65095387/f92aa8e16e80d7d8e02c9c6a0b33948478650483.png";
$audioUrl = "http://www.tldw.io/image2video/uploads/1541601782d3fa63b793871f791c8db512207f4312b716b389/b36dc81055cb51f52d71c198613cb811304dc971.mp3";
$splashUrl = "http://ec2-54-212-58-90.us-west-2.compute.amazonaws.com/image2video/uploads/15417092127723a243f0e122a400c54eaf6a9bda7c65095387/11a98349fb9e66f12a7a9cd0255ea9822d85ea5a.jpeg";
$mainiUrl = 'http://ec2-54-212-58-90.us-west-2.compute.amazonaws.com';
$debug = '';

### read command line parameters
$shortopts = "";
$longopts = array(
    "csv:", // Обязательное значение
    "logo:", // Необязательное значение
    "audio:", // Необязательное значение
    "splash:", // Необязательное значение
    "url:", // Необязательное значение
    "debug::", // Необязательное значение
);
$options = getopt($shortopts, $longopts);
$csvFile = isset($options['csv']) ? $options['csv'] : '';
$logoUrl = isset($options['logo']) ? $options['logo'] : $logoUrl;
$audioUrl = isset($options['audio']) ? $options['audio'] : $audioUrl;
$splashUrl = isset($options['splash']) ? $options['splash'] : $splashUrl;
$mainiUrl = isset($options['url']) ? $options['url'] : $mainiUrl;
$debug = isset($options['debug']) ? 1 : 0;



if (!$csvFile) {
    help("Need csv file with data. Can be defined with '--csv' option");
}

$apiUrl = "$mainiUrl/image2video/api";
$apiKey = '315820';
$dt = date("U");

$csvArray = array();
if (gettype($csvFile) === "array") {
    $csvArray = $csvFile;
}
if (gettype($csvFile) === "string") {
    $csvArray[] = $csvFile;
}

foreach ($csvArray as $csv) {
    if (!file_exists($csv)) {
        echo "File '$csv' do not exists\n";
        continue;
    }    
    $dataArray = getCsv($csv);
    if (!$dataArray) {
        echo "Cannot read file '$csv' or file have incorrect format\n";
        exit(1);
    }

    $path_parts = pathinfo($csv);
    $baseName=$path_parts['filename'];
    $baseName=preg_replace('/\W/','_', $baseName) ;
    $baseName="$baseName.mp4" ;
    if ($debug) {    
        echo "Used basename for output file: $baseName\n";
    }

########################################################################
    # 1. start project
    #
    echo "Start processing '$csv' file\n";
    $step = 1;
    if ($debug) {
        echo "Step $step. Add new project\n";
    }
    $answer = addProject($apiUrl, $apiKey, $step, "new project $dt");
    if (!$answer) {
        exit(1);
    }
    $project_id = $answer["project_id"];
#echo "New project added : $project_id\n";

    $step++;
    if ($debug) {
        echo "Step $step. Adding logo\n";
    }
    $answer = addLogo($apiUrl, $apiKey, $project_id, $step, $logoUrl);
    if (!$answer) {
        exit(1);
    }

    $step++;
    if ($debug) {
        echo "Step $step. Adding audio\n";
    }
    $answer = addAudio($apiUrl, $apiKey, $project_id, $step, $audioUrl);
    if (!$answer) {
        exit(1);
    }

    $id = 1;
    $foundEmptyLine = false;
    foreach ($dataArray as $data) {
        // looking for empty first cell
        if (trim($data[0]) === "Title") {
            $title = $data[1];
            continue;
        }
        if (trim($data[0]) === "Main Image") {
            $mainImage = $data[1];
            continue;
        }

        if (trim($data[0]) === "") { // empty line
            $foundEmptyLine = true;
            $step++;
            if ($debug) {
                echo "Step $step. Adding Main Image\n";
            }
            $answer = addImage($apiUrl, $apiKey, $project_id, $step, $mainImage);
            if (!$answer) {
                exit(1);
            }
            $step++;
            if ($debug) {
                echo "Step $step. Adding title\n";
            }
            $answer = addText($apiUrl, $apiKey, $project_id, $id, 4, 'fade', $step, strip_tags($title), 'center', 50);
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
            if ($debug) {
                echo "Step $step. Adding image " . $row["Caption1"] . "\n";
            }
            $answer = addImage($apiUrl, $apiKey, $project_id, $step, $row["UnitImage1"]);
            if (!$answer) {
                // exit(1);
                // do not stop processing if any upload images problems, go to next step
                continue;
            }
            $step++;
            if ($debug) {
                echo "Step $step. Adding text for " . $row["Caption1"] . "\n";
            }
            $answer = addText($apiUrl, $apiKey, $project_id, $id, 4, 'fade', $step, strip_tags($row["Caption1"]), 'center', 50);
            if (!$answer) {
                // exit(1);
                // do not stop processing if any upload images problems, go to next step
                continue;
            }
            $id++;

            $step++;
            if ($debug) {
                echo "Step $step. Adding image " . $row["Caption1"] . "\n";
            }
            $answer = addImage($apiUrl, $apiKey, $project_id, $step, $row["UnitImage1"]);
            if (!$answer) {
                // exit(1);
                // do not stop processing if any upload images problems, go to next step
                continue;
            }
            $step++;
            if ($debug) {
                echo "Step $step. Adding text for " . $row["Caption1"] . "\n";
            }
            $answer = addText($apiUrl, $apiKey, $project_id, $id, 8, 'concat', $step, strip_tags($row["SubCaption1"]));
            if (!$answer) {
                // exit(1);
                // do not stop processing if any upload images problems, go to next step
                continue;
            }
            $id++;
        }
    }

### add spalsh in the end
    $step++;
    if ($debug) {
        echo "Step $step. Adding image Splash\n";
    }
    $answer = addImage($apiUrl, $apiKey, $project_id, $step, $splashUrl);
    if (!$answer) {
        // exit(1);
        // do not stop processing if any upload images problems, go to next step
        // continue;
    }
    $step++;
    if ($debug) {
        echo "Step $step. Adding effects for Spalsh\n";
    }
    $answer = addText($apiUrl, $apiKey, $project_id, $id, 6, 'fade', $step, "");
    if (!$answer) {
        // exit(1);
        // do not stop processing if any upload images problems, go to next step
        // continue;
    }
    $id++;

### prepare video command
    $step++;
    if ($debug) {
        echo "Step $step. Prepare video command script\n";
    }
    $answer = prepareVideo($apiUrl, $apiKey, $project_id, $baseName, $step);
    if (!$answer) {
        exit(1);
    }
    $videoFileName = $answer["rows"][1]["name"];
    $videoFileUrl = $answer["rows"][1]["url"];

### shedule video processing
    $step++;
    if ($debug) {
        echo "Step $step. Shedule video task\n";
    }
    $answer = sheduleVideo($apiUrl, $apiKey, $project_id, $step);
    if (!$answer) {
        exit(1);
    }
    $task_id = $answer["task_id"];

########################################################################
    # 8. Waiting while video will be done
    #
    $step++;
    if ($debug) {echo "Step $step. Waiting while video will be done\n";}
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
        if ($debug) {
            echo "Waiting while sheduled command finished. Status now: $status\n";
        }
    }
    if ($debug) {
        echo "Your video is ready.\n";
        echo "Filename: $videoFileName\n";
    }

    echo "Url: $mainiUrl/$videoFileUrl\n";
}
exit(0);

function help($msg)
{
    $script = basename(__FILE__);
    fwrite(STDERR,
        "$msg
	Usage: php $script --csv file.csv [--csv file1.csv [--csv file2.csv...]] [--logo http://logo] [--audio http://audio] [--splash http://splash] [--url http://example.com/image2video] [--debug]
	where:
	--csv file.csv - csv file with data
	--logo http://logo - url of logo image
	--audio http://audio - url of audio file
	--splash http://splash - url of splash image
    --url http://example.com - main url of your site
    --debug  show additional debug info

	Example: $script --csv data.csv --logo http://localhost/logo.png --url http://ec2-54-212-58-90.us-west-2.compute.amazonaws.com
	\n");
    exit(-1);
}
