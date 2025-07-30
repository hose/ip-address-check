<?php
/*
Plugin Name: ipアドレスチェック
Plugin URI: http://blog.a-z0-9.net/access
Description: 閲覧者のアクセス情報を表示する事ができるようになります。
Version: 0.3
Author: hose
Author URI: http://blog.a-z0-9.net/
License: GPL2
Text Domain: ip-address-check
Domain Path: /languages
Requires at least: 4.6
Tested up to: 6.4
Requires PHP: 7.0

Copyright 2015-2024 hose
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

// Prevent direct access
if (!defined('ABSPATH')) {
    exit;
}

/**
 * Plugin constants
 */
define('IP_ADDRESS_CHECK_VERSION', '0.3');
define('IP_ADDRESS_CHECK_PLUGIN_URL', plugin_dir_url(__FILE__));
define('IP_ADDRESS_CHECK_PLUGIN_PATH', plugin_dir_path(__FILE__));

/**
 * Initialize plugin hooks
 */
add_action('init', 'ip_address_check_init');
add_action('wp_enqueue_scripts', 'ip_address_check_enqueue_scripts');
add_shortcode('ipAddress', 'ip_address_check_shortcode');

/**
 * Initialize plugin
 */
function ip_address_check_init() {
    // Load textdomain for internationalization
    load_plugin_textdomain('ip-address-check', false, dirname(plugin_basename(__FILE__)) . '/languages/');
}

/**
 * Enqueue scripts and styles
 */
function ip_address_check_enqueue_scripts() {
    // Only enqueue if shortcode is present on the page
    global $post;
    if (is_a($post, 'WP_Post') && has_shortcode($post->post_content, 'ipAddress')) {
        wp_enqueue_style(
            'ip-address-check-style',
            IP_ADDRESS_CHECK_PLUGIN_URL . 'css/ip-address-check.css',
            array(),
            IP_ADDRESS_CHECK_VERSION
        );
        
        wp_enqueue_script(
            'ip-address-check-script',
            IP_ADDRESS_CHECK_PLUGIN_URL . 'js/ip-address-check.js',
            array('jquery'),
            IP_ADDRESS_CHECK_VERSION,
            true
        );
        
        wp_localize_script('ip-address-check-script', 'ipAddressCheck', array(
            'ajaxurl' => admin_url('admin-ajax.php'),
            'nonce' => wp_create_nonce('ip_address_check_nonce'),
            'strings' => array(
                'copy_success' => __('コピーしました！', 'ip-address-check'),
                'copy_failed' => __('コピーに失敗しました', 'ip-address-check'),
                'copy_manual' => __('手動でコピーしてください', 'ip-address-check'),
                'no_info' => __('情報がありません。', 'ip-address-check'),
                'loading' => __('読み込み中', 'ip-address-check')
            )
        ));
    }
}

/**
 * Main shortcode function
 */
function ip_address_check_shortcode($atts = array()) {
    // Sanitize attributes
    $atts = shortcode_atts(array(
        'show_logo' => 'true',
        'show_copy_button' => 'true',
        'theme' => 'default'
    ), $atts, 'ipAddress');

    // Start output buffering
    ob_start();
    
    // Generate nonce for security
    $nonce = wp_create_nonce('ip_address_check_display');
    
    // Render the interface
    ip_address_check_render_interface($atts, $nonce);
    
    return ob_get_clean();
}

/**
 * Render the main interface
 */
function ip_address_check_render_interface($atts, $nonce) {
    $server_info = ip_address_check_get_server_info();
    $current_user_info = ip_address_check_get_user_info();
    $current_url = ip_address_check_get_current_url();
    
    echo '<div id="ip-address-check-container" class="ip-check-theme-' . esc_attr($atts['theme']) . '">';
    
    // Logo section
    if ($atts['show_logo'] === 'true') {
        ip_address_check_render_logo();
    }
    
    echo '<div id="ip-box" role="main" aria-label="' . esc_attr__('IPアドレスとブラウザ情報', 'ip-address-check') . '">';
    
    // Server information section
    ip_address_check_render_server_section($server_info, $current_url, $current_user_info);
    
    // Browser information section (populated by JavaScript)
    ip_address_check_render_browser_section();
    
    echo '</div>'; // #ip-box
    
    // Copy section
    if ($atts['show_copy_button'] === 'true') {
        ip_address_check_render_copy_section();
    }
    
    echo '<input type="hidden" id="ip-check-nonce" value="' . esc_attr($nonce) . '">';
    echo '</div>'; // #ip-address-check-container
}

/**
 * Render logo section
 */
function ip_address_check_render_logo() {
    $logo_path = IP_ADDRESS_CHECK_PLUGIN_URL . 'images/access_logo.png';
    echo '<img class="ip-check-logo" src="' . esc_url($logo_path) . '" alt="' . esc_attr__('IP Address Check Logo', 'ip-address-check') . '">';
}

/**
 * Render server information section
 */
function ip_address_check_render_server_section($server_info, $current_url, $user_info) {
    echo '<h2>' . esc_html__('アクセス情報', 'ip-address-check') . '</h2>';
    
    $server_fields = array(
        'url' => array(
            'label' => __('URL', 'ip-address-check'),
            'description' => __('現在アクセスしているURL', 'ip-address-check'),
            'value' => $current_url
        ),
        'wp_login' => array(
            'label' => __('WP_LOGIN', 'ip-address-check'),
            'description' => __('WordPressにログインしているか', 'ip-address-check'),
            'value' => $user_info
        ),
        'remote_addr' => array(
            'label' => __('REMOTE_ADDR', 'ip-address-check'),
            'description' => __('IPアドレス', 'ip-address-check'),
            'value' => $server_info['REMOTE_ADDR']
        ),
        'remote_host' => array(
            'label' => __('REMOTE_HOST', 'ip-address-check'),
            'description' => __('ホスト名', 'ip-address-check'),
            'value' => $server_info['REMOTE_HOST']
        ),
        'remote_port' => array(
            'label' => __('REMOTE_PORT', 'ip-address-check'),
            'description' => __('通信に利用したポート番号', 'ip-address-check'),
            'value' => $server_info['REMOTE_PORT']
        ),
        'http_accept' => array(
            'label' => __('HTTP_ACCEPT', 'ip-address-check'),
            'description' => __('ブラウザサポートMIMEタイプ', 'ip-address-check'),
            'value' => $server_info['HTTP_ACCEPT']
        ),
        'http_user_agent' => array(
            'label' => __('HTTP_USER_AGENT', 'ip-address-check'),
            'description' => __('ユーザーエージェント', 'ip-address-check'),
            'value' => $server_info['HTTP_USER_AGENT']
        ),
        'http_accept_language' => array(
            'label' => __('HTTP_ACCEPT_LANGUAGE', 'ip-address-check'),
            'description' => __('言語設定', 'ip-address-check'),
            'value' => $server_info['HTTP_ACCEPT_LANGUAGE']
        ),
        'http_accept_encoding' => array(
            'label' => __('HTTP_ACCEPT_ENCODING', 'ip-address-check'),
            'description' => __('エンコード方式', 'ip-address-check'),
            'value' => $server_info['HTTP_ACCEPT_ENCODING']
        ),
        'http_connection' => array(
            'label' => __('HTTP_CONNECTION', 'ip-address-check'),
            'description' => __('接続の状態', 'ip-address-check'),
            'value' => $server_info['HTTP_CONNECTION']
        ),
        'http_referer' => array(
            'label' => __('HTTP_REFERER', 'ip-address-check'),
            'description' => __('どこから来たのか', 'ip-address-check'),
            'value' => $server_info['HTTP_REFERER']
        )
    );
    
    foreach ($server_fields as $field_key => $field) {
        echo '<h3>■' . esc_html($field['label']) . ' <small>(' . esc_html($field['description']) . ')</small></h3>';
        echo '<p>' . esc_html($field['value']) . '</p>';
    }
}

/**
 * Render browser information section
 */
function ip_address_check_render_browser_section() {
    echo '<h2>' . esc_html__('ブラウザ情報', 'ip-address-check') . '</h2>';
    
    $browser_fields = array(
        'appCodeName' => array(
            'label' => __('appCodeName', 'ip-address-check'),
            'description' => __('コードネーム', 'ip-address-check')
        ),
        'appName' => array(
            'label' => __('appName', 'ip-address-check'),
            'description' => __('ブラウザ', 'ip-address-check')
        ),
        'appVersion' => array(
            'label' => __('appVersion', 'ip-address-check'),
            'description' => __('ブラウザのバージョン', 'ip-address-check')
        ),
        'userAgent' => array(
            'label' => __('userAgent', 'ip-address-check'),
            'description' => __('ユーザーエージェント', 'ip-address-check')
        ),
        'displaySize' => array(
            'label' => __('ディスプレイサイズ', 'ip-address-check'),
            'description' => ''
        ),
        'screenSize' => array(
            'label' => __('ブラウザ表示サイズ', 'ip-address-check'),
            'description' => __('※ブックマーク領域等除く', 'ip-address-check')
        ),
        'referrer' => array(
            'label' => __('referrer', 'ip-address-check'),
            'description' => __('リファラー', 'ip-address-check')
        ),
        'colorDepth' => array(
            'label' => __('colorDepth', 'ip-address-check'),
            'description' => __('色数', 'ip-address-check')
        ),
        'language' => array(
            'label' => __('navigator.language', 'ip-address-check'),
            'description' => __('言語バージョン', 'ip-address-check')
        ),
        'browserLanguage' => array(
            'label' => __('navigator.browserLanguage', 'ip-address-check'),
            'description' => __('ブラウザの言語バージョン', 'ip-address-check')
        ),
        'cookieEnabled' => array(
            'label' => __('cookieEnabled', 'ip-address-check'),
            'description' => __('クッキーが使えるか', 'ip-address-check')
        ),
        'javaEnabled' => array(
            'label' => __('javaEnabled', 'ip-address-check'),
            'description' => __('javaアプレットが使えるか', 'ip-address-check')
        )
    );
    
    foreach ($browser_fields as $field_key => $field) {
        $description_text = !empty($field['description']) ? ' <small>(' . esc_html($field['description']) . ')</small>' : '';
        echo '<h3>■' . esc_html($field['label']) . $description_text . '</h3>';
        echo '<p id="' . esc_attr($field_key) . '" class="loading">' . esc_html__('読み込み中', 'ip-address-check') . '</p>';
    }
    
    // JavaScript status
    echo '<h3>■' . esc_html__('javaScriptが使えるか', 'ip-address-check') . '</h3>';
    echo '<p><noscript id="no-js">' . esc_html__('無効', 'ip-address-check') . '</noscript></p>';
    
    // MIME Types
    echo '<h3>■' . esc_html__('navigator.mimeTypes', 'ip-address-check') . ' <small>(' . esc_html__('MIMEタイプ', 'ip-address-check') . ')</small></h3>';
    echo '<p id="mimeTypes" class="loading">' . esc_html__('読み込み中', 'ip-address-check') . '</p>';
    
    // Plugins
    echo '<h3>■' . esc_html__('navigator.plugins', 'ip-address-check') . ' <small>(' . esc_html__('プラグイン', 'ip-address-check') . ')</small></h3>';
    echo '<p id="plugins" class="loading">' . esc_html__('読み込み中', 'ip-address-check') . '</p>';
}

/**
 * Render copy section
 */
function ip_address_check_render_copy_section() {
    echo '<h3>' . esc_html__('コピー用', 'ip-address-check') . '</h3>';
    echo '<button type="button" id="copy-button" class="copy-button" aria-label="' . esc_attr__('情報をクリップボードにコピー', 'ip-address-check') . '">';
    echo esc_html__('クリップボードにコピー', 'ip-address-check');
    echo '</button>';
    echo '<textarea id="copy-ip-address" readonly aria-label="' . esc_attr__('コピー用テキストエリア', 'ip-address-check') . '"></textarea>';
}

/**
 * Get server information safely
 */
function ip_address_check_get_server_info() {
    $server = $_SERVER;
    $info = array();
    
    // Get remote address
    $info['REMOTE_ADDR'] = isset($server['REMOTE_ADDR']) ? sanitize_text_field($server['REMOTE_ADDR']) : __('情報がありません。', 'ip-address-check');
    
    // Get remote host
    if (isset($server['REMOTE_HOST']) && !empty($server['REMOTE_HOST'])) {
        $info['REMOTE_HOST'] = sanitize_text_field($server['REMOTE_HOST']);
    } else {
        // Try to resolve hostname
        if (!empty($info['REMOTE_ADDR']) && $info['REMOTE_ADDR'] !== __('情報がありません。', 'ip-address-check')) {
            $resolved_host = gethostbyaddr($info['REMOTE_ADDR']);
            $info['REMOTE_HOST'] = ($resolved_host !== $info['REMOTE_ADDR']) ? $resolved_host : $info['REMOTE_ADDR'];
        } else {
            $info['REMOTE_HOST'] = __('情報がありません。', 'ip-address-check');
        }
    }
    
    // Get other server variables safely
    $server_vars = array(
        'REMOTE_PORT', 'HTTP_ACCEPT', 'HTTP_USER_AGENT', 
        'HTTP_ACCEPT_LANGUAGE', 'HTTP_ACCEPT_ENCODING', 'HTTP_CONNECTION'
    );
    
    foreach ($server_vars as $var) {
        $info[$var] = isset($server[$var]) ? sanitize_text_field($server[$var]) : __('情報がありません。', 'ip-address-check');
    }
    
    // Handle referer specially
    if (isset($server['HTTP_REFERER']) && !empty($server['HTTP_REFERER'])) {
        $info['HTTP_REFERER'] = esc_url_raw($server['HTTP_REFERER']);
    } else {
        $info['HTTP_REFERER'] = __('情報がありません。', 'ip-address-check');
    }
    
    return $info;
}

/**
 * Get current user information
 */
function ip_address_check_get_user_info() {
    $current_user = wp_get_current_user();
    
    if ($current_user->ID == 0) {
        return __('ログインしていません。', 'ip-address-check');
    }
    
    $user_roles = $current_user->roles;
    $role_translations = array(
        'administrator' => __('管理者', 'ip-address-check'),
        'editor' => __('編集者', 'ip-address-check'),
        'author' => __('作成者', 'ip-address-check'),
        'contributor' => __('投稿者', 'ip-address-check'),
        'subscriber' => __('購読者', 'ip-address-check')
    );
    
    foreach ($role_translations as $role => $translation) {
        if (in_array($role, $user_roles)) {
            return sprintf(__('%s(%s)としてログインしています。', 'ip-address-check'), $translation, $role);
        }
    }
    
    return __('ユーザーとしてログインしています。', 'ip-address-check');
}

/**
 * Get current URL safely
 */
function ip_address_check_get_current_url() {
    $protocol = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') || $_SERVER['SERVER_PORT'] == 443 ? "https://" : "http://";
    $host = sanitize_text_field($_SERVER['HTTP_HOST']);
    $uri = sanitize_text_field($_SERVER['REQUEST_URI']);
    
    return $protocol . $host . $uri;
}

/**
 * Backward compatibility function
 * @deprecated Use ip_address_check_shortcode() instead
 */
function getIpAddress() {
    return ip_address_check_shortcode();
}
?>
