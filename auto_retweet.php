<?php
# */15 * * * * php /path/auto_retweet.php

require dirname(__FILE__) . '/config.inc.php';

$twitter = new TwitterAPIExchange($settings);
$json = $twitter->setGetfield('?screen_name=user_name')
    ->buildOauth('https://api.twitter.com/1.1/statuses/user_timeline.json', 'GET')
    ->performRequest();

$array = json_decode($json, true);

$result = array();
foreach ($array as $key => $value)
{
    if (timediff(strtotime($array[$key]['created_at']), time()) <= 600)
    {
        $result[] = $array[$key]['id'];
    }
}
if (count($result) == 0)
{
    exit;
}

foreach ($result as $value)
{
    $twitter = new TwitterAPIExchange($settings);
    echo $twitter->buildOauth('https://api.twitter.com/1.1/statuses/retweet/' . $value . '.json', 'POST')
        ->setPostfields(array('id' => $value))
        ->performRequest();
}

function timediff($begin_time, $end_time)
{
    if ($begin_time < $end_time)
    {
        $starttime = $begin_time;
        $endtime = $end_time;
    }
    else
    {
        $starttime = $end_time;
        $endtime = $begin_time;
    }
    return ($endtime - $starttime);
}
