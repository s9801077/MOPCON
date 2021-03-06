<?php
function getAllSpeakers($order = '')
{
    $jsonText = file_get_contents("https://spreadsheets.google.com/feeds/list/1RDZ9CtURducrXcMgIGb5Nd7PpDUfGPpYNHDGNYScreA/ox2lmxj/public/values?alt=json");
    $jsonArr = json_decode($jsonText, true);
    $returnArr = [];
    foreach($jsonArr as $info) {
        if($info['image'] != '') {
            $id = strtolower(pathinfo($info['image'], PATHINFO_FILENAME));
        } else {
            continue; //異常
        }
        $returnArr[$id] = $info;
    }
    if ($order=='random') {
        shuffle($returnArr);
    } else { //預設為 a > z
        ksort($returnArr);
    }

    return $returnArr;
}
