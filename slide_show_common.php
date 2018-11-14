<?php
// common variables and function for slide_show project

function addProject($apiUrl, $apiKey, $step, $projectName)
{
    $url = "$apiUrl/project.php";
    $params = array(
        "apikey" => $apiKey,
        "action" => 'add',
        "name" => $projectName,
    );
    $answer = get_api_answer($url, $params, $step);
    return ($answer);
}

function addLogo($apiUrl, $apiKey, $project_id, $step, $srcUrl)
{
    $url = "$apiUrl/logo.php";
    $params = array(
        "apikey" => $apiKey,
        "project_id" => $project_id,
        "action" => 'add',
        "image_url" => $srcUrl,
        "w" => 80,
        "y" => 10,
        "x" => 10,
    );
    $answer = get_api_answer($url, $params, $step);
    return ($answer);
}

function addAudio($apiUrl, $apiKey, $project_id, $step, $srcUrl)
{
    $url = "$apiUrl/audio.php";
    $params = array(
        "apikey" => $apiKey,
        "project_id" => $project_id,
        "action" => 'add',
        "audio_url" => $srcUrl,
    );
    $answer = get_api_answer($url, $params, $step);
    return ($answer);
}

function addImage($apiUrl, $apiKey, $project_id, $step, $srcUrl)
{
    $url = "$apiUrl/image.php";
    $params = array(
        "apikey" => $apiKey,
        "project_id" => $project_id,
        "action" => 'add',
        "image_url" => $srcUrl,
    );
    $answer = get_api_answer($url, $params, $step);
    return ($answer);
}

function addText($apiUrl, $apiKey, $project_id, $id, $duration, $transition, $step, $text, $text_align="bottom", $font_size=40)
{
    $url = "$apiUrl/effect.php";
    $params = array(
        "apikey" => $apiKey,
        "project_id" => $project_id,
        "id" => $id,
        "action" => 'set',
        "text" => $text,
        "font_size" => $font_size,
        "duration" => $duration,
        "transition" => $transition,
        "fade_in" => 0.5,
        "fade_out" => 0.5,
        "animation" => "none",
        "text_boxopacity" => 50,
        "text_align" => $text_align,
        "crop_image" => 0,
    );
    $answer = get_api_answer($url, $params, $step);
    return ($answer);
}

function prepareVideo($apiUrl, $apiKey, $project_id, $baseName, $step)
{
# 6. Prepare video
    # this mean prepare 'the command to make video'
    #
    $url = "$apiUrl/video.php";
    $params = array(
        "apikey" => $apiKey,
        "project_id" => $project_id,
        "action" => 'prepare',
        "basename" => $baseName,
    );
    $answer = get_api_answer($url, $params, $step);
    return ($answer);
}

function sheduleVideo($apiUrl, $apiKey, $project_id, $step)
{
    # shedule video task
    $url = "$apiUrl/video.php";
    $params = array(
        "apikey" => $apiKey,
        "project_id" => $project_id,
        "action" => 'make',
    );
    $answer = get_api_answer($url, $params, $step);
    return ($answer);
}

function getTaskStatus($apiUrl, $apiKey, $project_id, $task_id, $step)
{
    $url = "$apiUrl/queue.php";
    $params = array(
        "apikey" => $apiKey,
        "action" => 'list',
        "task_id" => $task_id,
    );
    $answer = get_api_answer($url, $params, $step);
    return ($answer);
}

/**
 * getCsv
 * parse scv file and return array of lines
 *
 * @param    string $csvFile
 * @return array or false if any error
 */
function getCsv($csvFile)
{

    $dataArray = array();
    if (($handle = fopen($csvFile, "r")) !== false) {
        while (($data = fgetcsv($handle, 1000, ",")) !== false) {
            $num = count($data);
            $dataArray[] = $data;
        }
        fclose($handle);
    } else {
        print "Cannot open file '$csvFile'";
        return (false);
    }
    return ($dataArray);
}

/**
 * Get an api answer
 * return the array variable of false
 */
function get_api_answer($url, $params, $step)
{
    $result = get_web_page($url, $params);
    if (!$result || $result['errno'] != 0 || $result['http_code'] != 200) {
        fwrite(STDERR, "Step $step. Error. Cannot get result from $url\n");
        return (false);
    }

    $answer = json_decode($result['content'], true);
    if ($answer["status"] != "ok") {
        fwrite(STDERR, "Step $step. Error: " . $answer["errorno"] . " " . $answer["error"] . "\n");
        #echo var_dump($answer);
        #echo var_dump($result['content']);
        return (false);
    }
    return ($answer);
}

/**
 * Get a web file (HTML, XHTML, XML, image, etc.) from a URL.  Return an
 * array containing the HTTP server response header fields and content.
 */
function get_web_page($url, $params)
{
    $user_agent = 'Mozilla/5.0 (Windows NT 6.1; rv:8.0) Gecko/20100101 Firefox/8.0';

    $options = array(
        CURLOPT_USERAGENT => $user_agent, //set user agent
        CURLOPT_RETURNTRANSFER => true, // return web page
        CURLOPT_HEADER => false, // don't return headers
        CURLOPT_FOLLOWLOCATION => true, // follow redirects
        CURLOPT_ENCODING => "", // handle all encodings
        CURLOPT_AUTOREFERER => true, // set referer on redirect
        CURLOPT_CONNECTTIMEOUT => 120, // timeout on connect
        CURLOPT_TIMEOUT => 300, // timeout on response
        CURLOPT_MAXREDIRS => 10, // stop after 10 redirects
        CURLOPT_POST => 1,
        CURLOPT_POSTFIELDS => $params,
    );

    $ch = curl_init($url);
    curl_setopt_array($ch, $options);
    $content = curl_exec($ch);
    $err = curl_errno($ch);
    $errmsg = curl_error($ch);
    $header = curl_getinfo($ch);
    curl_close($ch);

    $header['errno'] = $err;
    $header['errmsg'] = $errmsg;
    $header['content'] = $content;
    return $header;
}



