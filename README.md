# ip-address-check
(・∀・)「表示崩れてるんですけど」  
(ヽ’ω`)「お使いのブラウザを教えて下さい（あっ、でもレスポンシブデザインだから見ているブラウザのサイズも確認しなきゃ・・・）」  
  
(・∀・)「スマホから見ると崩れてるんですけど」  
(ヽ’ω`)「スマホの機種名とブラウザと、それぞれのバージョンを・・・」  
(・∀・)「よく分からないよ！」  
(ヽ’ω`)「ええっと、確認の仕方は・・（何でこんな面倒な事を・・）」  
  
##もう大丈夫！
  
「IPアドレスチェック」は表示したい投稿、ページにショートコード[ipAddress]を追加するだけで、IPアドレスやブラウザ情報をチェックできるWordPressプラグインです。情報をコピペして送ってもらえればデバッグの強力な味方になります。

##使い方
投稿、ページのすきな所に[ipAddress]を追加

## Demo
http://blog.a-z0-9.net/access

##表示される情報
###アクセス情報
* URL（現在アクセスしているURL）
* REMOTE_ADDR (IPアドレス)
* REMOTE_HOST (ホスト名)
* REMOTE_PORT (通信に利用したポート番号)
* HTTP_ACCEPT (ブラウザサポートMINEタイプ)
* HTTP_USER_AGENT (ユーザーエージェント)
* HTTP_ACCEPT_LANGUAGE (言語設定)
* HTTP_ACCEPT_ENCODING (エンコード方式)
* HTTP_CONNECTION (接続の状態)
* HTTP_REFERER (どこから来たのか)

###ブラウザ情報
* appCodeName (コードネーム)
* appName (ブラウザ)
* appVersion (ブラウザのバージョン)
* userAgent (ユーザーエージェント)
* ディスプレイサイズ
* ブラウザ表示サイズ (※ブックマーク領域等除く)
* referrer (リファラー)
* colorDepth (色数)
* navigator.language (言語バージョン)
* navigator.browserLanguage (ブラウザの言語バージョン)
* cookieEnabled (クッキーが使えるか)
* javaEnabled (javaアプレットが使えるか)
* javaScriptが使えるか
* navigator.mimeTypes (MIMEタイプ)
* navigator.plugins (プラグイン)
