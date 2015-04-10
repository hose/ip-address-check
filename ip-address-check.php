<?php
/*
Plugin Name: ipアドレスチェック
Plugin URI: http://blog.a-z0-9.net/access
Description: 閲覧者のアクセス情報を表示する事ができるようになります。
Version: 0.1
Author: hose
Author URI: http://blog.a-z0-9.net/
License: GPL2

Copyright 14.03.2015 hose
This program is free software; you can redistribute it and/or modify
it under the terms of the GNU General Public License, version 2, as
published by the Free Software Foundation.
This program is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
GNU General Public License for more details.
You should have received a copy of the GNU General Public License
along with this program; if not, write to the Free Software
Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA 02110-1301 USA

*/

function getIpAddress(){

    //ユーザーの情報
    $server = $_SERVER;
    if (isset($server["REMOTE_HOST"])) {
        $host = $server["REMOTE_HOST"];
    } else {
        $host = '';
    }
    if($host == "" || $host == $server["REMOTE_ADDR"]){
        $server['REMOTE_HOST'] = gethostbyaddr($server["REMOTE_ADDR"]);
    }

    //リファラーチェック
    if( isset($server['HTTP_REFERER']) ){
        $referer = $server['HTTP_REFERER'];
    } else {
        $referer = '情報がありません。';
    }

    //タイトル画像
    $titleImagePath = plugins_url() . "/ip-address-check/images/access_logo.png";

    //現在アクセスしているURL
    $url = (empty($_SERVER["HTTPS"]) ? "http://" : "https://") . $_SERVER["HTTP_HOST"] . $_SERVER["REQUEST_URI"];


echo <<<EOT
<img style="background:#fff; border-radius: 50%; margin: 20px auto; border: 1px outset #b7b600; padding: 60px 0px;" src="{$titleImagePath}">
<div id="ip-box">
<h2>アクセス情報</h2>

<h3>■URL <small>(現在アクセスしているURL)</small></h3>
<p>{$url}</p>

<h3>■REMOTE_ADDR <small>(IPアドレス)</small></h3>
<p>{$server['REMOTE_ADDR']}</p>

<h3>■REMOTE_HOST <small>(ホスト名)</small></h3>
<p>{$server['REMOTE_HOST']}</p>

<h3>■REMOTE_PORT <small>(通信に利用したポート番号)</small></h3>
<p>{$server['REMOTE_PORT']}</p>

<h3>■HTTP_ACCEPT <small>(ブラウザサポートMINEタイプ)</small></h3>
<p>{$server['HTTP_ACCEPT']}</p>

<h3>■HTTP_USER_AGENT <small>(ユーザーエージェント)</small></h3>
<p>{$server['HTTP_USER_AGENT']}</p>

<h3>■HTTP_ACCEPT_LANGUAGE <small>(言語設定)</small></h3>
<p>{$server['HTTP_ACCEPT_LANGUAGE']}</p>

<h3>■HTTP_ACCEPT_ENCODING <small>(エンコード方式)</small></h3>
<p>{$server['HTTP_ACCEPT_ENCODING']}</p>

<h3>■HTTP_CONNECTION <small>(接続の状態)</small></h3>
<p>{$server['HTTP_CONNECTION']}</p>

<h3>■HTTP_REFERER <small>(どこから来たのか)</small></h3>
<p>{$referer}</p>


<h2>ブラウザ情報</h2>

<h3>■appCodeName <small>(コードネーム)</small></h3>
<p id="appCodeName"></p>

<h3>■appName <small>(ブラウザ)</small></h3>
<p id="appName"></p>

<h3>■appVersion <small>(ブラウザのバージョン)</small></h3>
<p id="appVersion"></p>

<h3>■userAgent <small>(ユーザーエージェント)</small></h3>
<p id="userAgent"></p>

<h3>■ディスプレイサイズ</h3>
<p id="displaySize"></p>

<h3>■ブラウザ表示サイズ <small>(※ブックマーク領域等除く)</small></h3>
<p id="screenSize"></p>

<h3>■referrer <small>(リファラー)</small></h3>
<p id="referrer"></p>

<h3>■colorDepth <small>(色数)</small></h3>
<p id="colorDepth"></p>

<h3>■navigator.language <small>(言語バージョン)</small></h3>
<p id="language"></p>

<h3>■navigator.browserLanguage <small>(ブラウザの言語バージョン)</small></h3>
<p id="browserLanguage"></p>

<h3>■cookieEnabled <small>(クッキーが使えるか)</small></h3>
<p id="cookieEnabled"></p>

<h3>■javaEnabled <small>(javaアプレットが使えるか)</small></h3>
<p id="javaEnabled"></p>

<h3>■javaScriptが使えるか</h3>
<p><noscript id="no-js">無効</noscript></p>

<h3>■navigator.mimeTypes <small>(MIMEタイプ)</small></h3>
<p id="mimeTypes"></p>

<h3>■navigator.plugins <small>(プラグイン)</small></h3>
<p id="plugins"></p>
</div>


<h3>コピー用</h3>
<textarea id="copy-ip-address" style="width:98%;height:300px;"></textarea>


EOT;

    //jsの読み込み
    wp_enqueue_script( 'ip-address-check', plugin_dir_url( __FILE__ ) . '/js/ip-address-check.js', array( 'jquery' ) );

}

add_shortcode('ipAddress', 'getIpAddress');
?>
