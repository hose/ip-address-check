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

echo <<<EOT
<img style="background:#fff; border-radius: 50%; margin: 20px auto; border: 1px outset #b7b600; padding: 60px 0px;" src="{$titleImagePath}">
<h4>REMOTE_ADDR <small>(IPアドレス)</small></h4>
<p>{$server['REMOTE_ADDR']}</p>

<h4>REMOTE_HOST <small>(ホスト名)</small></h4>
<p>{$server['REMOTE_HOST']}</p>

<h4>REMOTE_PORT <small>(通信に利用したポート番号)</small></h4>
<p>{$server['REMOTE_PORT']}</p>

<h4>HTTP_ACCEPT <small>(ブラウザサポートMINEタイプ)</small></h4>
<p>{$server['HTTP_ACCEPT']}</p>

<h4>HTTP_USER_AGENT <small>(ユーザーエージェント)</small></h4>
<p>{$server['HTTP_USER_AGENT']}</p>

<h4>HTTP_ACCEPT_LANGUAGE <small>(言語設定)</small></h4>
<p>{$server['HTTP_ACCEPT_LANGUAGE']}</p>

<h4>HTTP_ACCEPT_ENCODING <small>(エンコード方式)</small></h4>
<p>{$server['HTTP_ACCEPT_ENCODING']}</p>

<h4>HTTP_CONNECTION <small>(接続の状態)</small></h4>
<p>{$server['HTTP_CONNECTION']}</p>

<h4>HTTP_REFERER <small>(どこから来たのか)</small></h4>
<p>{$referer}</p>


<h3>コピー用</h3>
<textarea id="copy-ip-address" style="width:98%;height:300px;">
■REMOTE_ADDR (IPアドレス)
{$server['REMOTE_ADDR']}

■REMOTE_HOST (ホスト名)
{$server['REMOTE_HOST']}

■REMOTE_PORT (通信に利用したポート番号)
{$server['REMOTE_PORT']}

■HTTP_ACCEPT (ブラウザサポートMINEタイプ)
{$server['HTTP_ACCEPT']}

■HTTP_USER_AGENT (ユーザーエージェント)
{$server['HTTP_USER_AGENT']}

■HTTP_ACCEPT_LANGUAGE (言語設定)
{$server['HTTP_ACCEPT_LANGUAGE']}

■HTTP_ACCEPT_ENCODING (エンコード方式)
{$server['HTTP_ACCEPT_ENCODING']}

■HTTP_CONNECTION (接続の状態)
{$server['HTTP_CONNECTION']}

■HTTP_REFERER (どこから来たのか)
{$referer}

</textarea>



EOT;

}

add_shortcode('ipAddress', 'getIpAddress');



?>
