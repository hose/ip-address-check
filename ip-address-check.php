<?php
/*
Plugin Name: ipアドレスチェック
Plugin URI: http://blog.a-z0-9.net/access
Description: 閲覧者のアクセス情報を表示する事ができるようになります。
Version: 0.2
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

    //ユーザーの情報を安全に取得
    $server = $_SERVER;
    
    // REMOTE_HOST の安全な処理
    if (isset($server["REMOTE_HOST"]) && !empty($server["REMOTE_HOST"])) {
        $host = sanitize_text_field($server["REMOTE_HOST"]);
    } else {
        $host = '';
    }
    
    // REMOTE_ADDR の存在確認と処理
    $remote_addr = isset($server["REMOTE_ADDR"]) ? sanitize_text_field($server["REMOTE_ADDR"]) : '';
    if($host == "" || $host == $remote_addr){
        if (!empty($remote_addr)) {
            $resolved_host = gethostbyaddr($remote_addr);
            $server['REMOTE_HOST'] = ($resolved_host !== $remote_addr) ? $resolved_host : $remote_addr;
        } else {
            $server['REMOTE_HOST'] = '情報がありません。';
        }
    }

    //リファラーチェック
    if( isset($server['HTTP_REFERER']) && !empty($server['HTTP_REFERER']) ){
        $referer = esc_html($server['HTTP_REFERER']);
    } else {
        $referer = '情報がありません。';
    }

    //タイトル画像
    $titleImagePath = plugins_url() . "/ip-address-check/images/access_logo.png";

    //現在アクセスしているURL
    $url = (empty($_SERVER["HTTPS"]) ? "http://" : "https://") . $_SERVER["HTTP_HOST"] . $_SERVER["REQUEST_URI"];

    //WPへのログイン状態
    $current_user = wp_get_current_user();
    if ($current_user->ID == 0) {
        $wp_login_user = "ログインしていません。";
    } else {
        $user_roles = $current_user->roles;
        if (in_array('administrator', $user_roles)) {
            $wp_login_user = "管理者(administrator)としてログインしています。";
        } else if (in_array('editor', $user_roles)) {
            $wp_login_user = "編集者(editor)としてログインしています。";
        } else if (in_array('author', $user_roles)) {
            $wp_login_user = "作成者(author)としてログインしています。";
        } else if (in_array('contributor', $user_roles)) {
            $wp_login_user = "投稿者(contributor)としてログインしています。";
        } else if (in_array('subscriber', $user_roles)) {
            $wp_login_user = "購読者(subscriber)としてログインしています。";
        } else {
            $wp_login_user = "ユーザーとしてログインしています。";
        }
    }


echo '<img style="background:#fff; border-radius: 50%; margin: 20px auto; border: 1px outset #b7b600; padding: 60px 0px;" src="' . esc_attr($titleImagePath) . '" alt="IP Address Check Logo">';
echo '<div id="ip-box">';
echo '<h2>アクセス情報</h2>';

echo '<h3>■URL <small>(現在アクセスしているURL)</small></h3>';
echo '<p>' . esc_html($url) . '</p>';

echo '<h3>■WP_LOGIN <small>(WordPressにログインしているか)</small></h3>';
echo '<p>' . esc_html($wp_login_user) . '</p>';

echo '<h3>■REMOTE_ADDR <small>(IPアドレス)</small></h3>';
echo '<p>' . esc_html(isset($server['REMOTE_ADDR']) ? $server['REMOTE_ADDR'] : '情報がありません。') . '</p>';

echo '<h3>■REMOTE_HOST <small>(ホスト名)</small></h3>';
echo '<p>' . esc_html(isset($server['REMOTE_HOST']) ? $server['REMOTE_HOST'] : '情報がありません。') . '</p>';

echo '<h3>■REMOTE_PORT <small>(通信に利用したポート番号)</small></h3>';
echo '<p>' . esc_html(isset($server['REMOTE_PORT']) ? $server['REMOTE_PORT'] : '情報がありません。') . '</p>';

echo '<h3>■HTTP_ACCEPT <small>(ブラウザサポートMINEタイプ)</small></h3>';
echo '<p>' . esc_html(isset($server['HTTP_ACCEPT']) ? $server['HTTP_ACCEPT'] : '情報がありません。') . '</p>';

echo '<h3>■HTTP_USER_AGENT <small>(ユーザーエージェント)</small></h3>';
echo '<p>' . esc_html(isset($server['HTTP_USER_AGENT']) ? $server['HTTP_USER_AGENT'] : '情報がありません。') . '</p>';

echo '<h3>■HTTP_ACCEPT_LANGUAGE <small>(言語設定)</small></h3>';
echo '<p>' . esc_html(isset($server['HTTP_ACCEPT_LANGUAGE']) ? $server['HTTP_ACCEPT_LANGUAGE'] : '情報がありません。') . '</p>';

echo '<h3>■HTTP_ACCEPT_ENCODING <small>(エンコード方式)</small></h3>';
echo '<p>' . esc_html(isset($server['HTTP_ACCEPT_ENCODING']) ? $server['HTTP_ACCEPT_ENCODING'] : '情報がありません。') . '</p>';

echo '<h3>■HTTP_CONNECTION <small>(接続の状態)</small></h3>';
echo '<p>' . esc_html(isset($server['HTTP_CONNECTION']) ? $server['HTTP_CONNECTION'] : '情報がありません。') . '</p>';

echo '<h3>■HTTP_REFERER <small>(どこから来たのか)</small></h3>';
echo '<p>' . $referer . '</p>';

echo '<h2>ブラウザ情報</h2>';

echo '<h3>■appCodeName <small>(コードネーム)</small></h3>';
echo '<p id="appCodeName"></p>';

echo '<h3>■appName <small>(ブラウザ)</small></h3>';
echo '<p id="appName"></p>';

echo '<h3>■appVersion <small>(ブラウザのバージョン)</small></h3>';
echo '<p id="appVersion"></p>';

echo '<h3>■userAgent <small>(ユーザーエージェント)</small></h3>';
echo '<p id="userAgent"></p>';

echo '<h3>■ディスプレイサイズ</h3>';
echo '<p id="displaySize"></p>';

echo '<h3>■ブラウザ表示サイズ <small>(※ブックマーク領域等除く)</small></h3>';
echo '<p id="screenSize"></p>';

echo '<h3>■referrer <small>(リファラー)</small></h3>';
echo '<p id="referrer"></p>';

echo '<h3>■colorDepth <small>(色数)</small></h3>';
echo '<p id="colorDepth"></p>';

echo '<h3>■navigator.language <small>(言語バージョン)</small></h3>';
echo '<p id="language"></p>';

echo '<h3>■navigator.browserLanguage <small>(ブラウザの言語バージョン)</small></h3>';
echo '<p id="browserLanguage"></p>';

echo '<h3>■cookieEnabled <small>(クッキーが使えるか)</small></h3>';
echo '<p id="cookieEnabled"></p>';

echo '<h3>■javaEnabled <small>(javaアプレットが使えるか)</small></h3>';
echo '<p id="javaEnabled"></p>';

echo '<h3>■javaScriptが使えるか</h3>';
echo '<p><noscript id="no-js">無効</noscript></p>';

echo '<h3>■navigator.mimeTypes <small>(MIMEタイプ)</small></h3>';
echo '<p id="mimeTypes"></p>';

echo '<h3>■navigator.plugins <small>(プラグイン)</small></h3>';
echo '<p id="plugins"></p>';
echo '</div>';

echo '<h3>コピー用</h3>';
echo '<textarea id="copy-ip-address" style="width:98%;height:300px;" readonly></textarea>';

    //jsの読み込み
    wp_enqueue_script( 'ip-address-check', plugin_dir_url( __FILE__ ) . '/js/ip-address-check.js', array( 'jquery' ) );

}

add_shortcode('ipAddress', 'getIpAddress');
?>
