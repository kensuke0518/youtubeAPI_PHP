<?php


/**
 * Youtube Data API v3にアクセスして、
 * 1時間おきにGoogleAppScriptからXfreeサーバーにあるphpを呼び出して、
 * PHPからJSONファイルに取得したデータを書き込みたい。
 * そのJSONデータにJavaScriptからFetchでアクセスして、
 * データを使って過去1週間の急上昇の24時間データを表示させたい。
 * 
 * なぜこんな回りくどいことをやるかというと
 * - APIへの1時間分のアクセス量が制限されているから
 * - 急上昇データは頻繁に変わるものではないため、一度取得して記録しておけばいいから
 * 
 * 参考元
 * PHPからAPIへの接続
 * http://challenge.no1s.biz/programming/php/381
 * Google App Script
 * https://kijtra.com/article/cron-by-google-apps-script/
 * 
 * これを一旦コピペしてAPIキーとチャンネルIDを設定して、APIの認証のキーの制限をなしにしたらいけた。
 * https://qiita.com/kuzira_vimmer/items/dfd9f4febaec11851c06
 * 
 * サーバーはHerokuでもいいかと思ったけど30分でサーバーがアイドル状態になってしまうので
 * https://qiita.com/udon242/items/b8efd594e380aaf830b3
 * これを活用してもいいかもしれない
 * 無料枠ではアプリケーションをいっぱい作られないのもネック
 */
/**
 * composerとは？（npmみたいなもん？）
 * https://qiita.com/sano1202/items/50e5a05227d739302761
 * https://www.webdesignleaves.com/pr/php/php_composer.php
 * https://qiita.com/daikiojm/items/7f74c08221db42ef611c
 * https://qiita.com/niisan-tokyo/items/8cccec88d45f38171c94
 * 
 * vendorが読み込めない！
 * https://genkiroid.github.io/2016/07/15/about-composer-autoload/
 * ただvendorの場所を変えているからこれでいいのかわからない。
 * 個人的にはwwwの上にvendorを常に生成して、.gitignoreしたい。
 * 
 */
require_once (dirname(__FILE__) . '/vendor/autoload.php');
//先ほど取得したAPIキーを定数にセットする
const API_KEY = process.env.MY_API_KEY;

//認証を行う
function getClient() 
{
    $client = new Google_Client();
    $client->setApplicationName("youtube-api-test");
    $client->setDeveloperKey(API_KEY);
    return $client;
}

//動画を取得する.
function popularVideos() 
{
    $youtube = new Google_Service_YouTube(getClient());
    //ここに好きなYouTubeのチャンネルIDを入れる
    /*$params['channelId'] = 'UCPyNsNSTUtywkekbDdCA_8Q';
    $params['type'] = 'video';
    $params['maxResults'] = 50;
    $params['order'] = 'date';*/
    $params['chart'] = 'mostPopular';
    $params['regionCode'] = 'JP';
    $params['maxResults'] = 50;
    try {
        //$searchResponse = $youtube->search->listSearch('snippet', $params);
        $videosResponse = $youtube->videos->listVideos('snippet', $params); //このメソッドlistVideos()の説明はどこで調べられるのか
    } catch (Google_Service_Exception $e) {
        echo htmlspecialchars($e->getMessage());
        exit;
    } catch (Google_Exception $e) {
        echo htmlspecialchars($e->getMessage());
        exit;
    }
    foreach ($videosResponse['items'] as $videos_result) {
        $videos[] = $videos_result;
    }
    return $videos;
}

$videos = popularVideos();

//取得した動画のサムネを表示してみる
foreach ($videos as $video) {
    //var_dump($video);
    //echo '<img src="' . $video['snippet']['thumbnails']['high']['url']. '" />';
}
//var_dump($videos);
//$arr = json_encode($videos);

//test.jsonがあれば読み込む
$url = 'test.json';
$json = file_get_contents($url);

if($json === false){
    $arr = [$videos];
    $arr = json_encode($arr);
    file_put_contents("test.json" , $arr);
}
else{
    $json = json_decode($json,true);
    $arr = $videos;
    array_unshift($json,$arr);
    $count = count($json);
    if($count > 10){
        array_pop($json);
    }
    $aaa = json_encode($json);
    file_put_contents("test.json" , $aaa);
}



?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
</head>
<body>
    <a href="page.html">あああ</a>
</body>
</html>
