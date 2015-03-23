jQuery(function($) {
    //appCodeName
    $("#appCodeName").text(navigator.appCodeName);

    //appName
    $("#appName").text(navigator.appName);

    //appVersion
    $("#appVersion").text(navigator.appVersion);

    //ユーザーエージェント
    $("#userAgent").text(navigator.userAgent);

    //ディスプレイサイズ
    var displaySize = screen.width + ' × ' + screen.height + ' (pixel)';
    $("#displaySize").text(displaySize);

    //ブラウザサイズ
    var screenSize = window.innerWidth + ' × ' + window.innerHeight + ' (pixel)';
    $("#screenSize").text(screenSize);
    //リサイズ時の処理
    var timer = false;
    $(window).resize(function() {
        if (timer !== false) {
            clearTimeout(timer);
        }
        timer = setTimeout(function() {
            screenSize = window.innerWidth + ' × ' + window.innerHeight + ' (pixel)';
            $("#screenSize").text(screenSize);
            //ブラウザのりサイズ時に、コピー用エリアにもコピー
            $("#copy-ip-address").text($("#ip-box").text());
        }, 600);
    });

    //referrer
    var referrer_is;
    if (document.referrer) {
        referrer_is = document.referrer;
    } else {
        referrer_is = "無し";
    }
    $("#referrer").text(referrer_is);
    

    //色数
    var color = screen.colorDepth + 'bit';
    color += ' (' + Math.pow(2, screen.colorDepth) + '色)';
    $("#colorDepth").text(color);

    //navigator.language
    var language_is;
    if(navigator.language){
        language_is = navigator.language;
    }else{
        language_is = "取得できませんでした。";
    }
    $("#language").text(language_is);

    //navigator.browserLanguage
    var browserLanguage_is;
    if(navigator.browserLanguage){
        browserLanguage_is = navigator.browserLanguage;
    }else{
        browserLanguage_is = "取得出来ませんでした。";
    }
    $("#browserLanguage").text(browserLanguage_is);
    

    //cookieが使えるか
    var cookie_is;
    if (navigator.cookieEnabled){
        cookie_is = "可";
    } else {
        cookie_is = "不可";
    }
    $("#cookieEnabled").text(cookie_is);

    //javaが使えるか
    var java_is;
    if (navigator.javaEnabled()) { 
        java_is = "可";
    } else {
        java_is = "不可";   
    }
    $("#javaEnabled").text(java_is);

    //jsが使えるか
    $("#no-js").html("").after("有効");

    //mimeTypes
    var i_mimeTypes;
    var j_mimeTypes = 1;
    var mimeTypes = "";
    for(i_mimeTypes=0; i_mimeTypes<navigator.mimeTypes.length-1; i_mimeTypes++){
        value = navigator.mimeTypes[i_mimeTypes].type;
        mimeTypes += j_mimeTypes + ' : ' + value + '<br>';
        j_mimeTypes++;
    }
    $("#mimeTypes").html(mimeTypes);

    //プラグイン
    var i_plugins;
    var j_plugins = 1;
    var plugins = "";
    for(i_plugins=0; i_plugins<navigator.plugins.length-1; i_plugins++){
        value_name        = navigator.plugins[i_plugins].name;
        value_description = navigator.plugins[i_plugins].description;
        plugins += j_plugins + ' : ' + value_name + '<br>';
        if(value_description){
            plugins += ' (' + value_description + ')<br>';
        }
        j_plugins++;
    }
    $("#plugins").html(plugins);

    //コピー用エリアにコピー
    $("#copy-ip-address").text($("#ip-box").text());

});