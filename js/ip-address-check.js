/**
 * IP Address Check Plugin JavaScript
 * Version: 0.3
 * Updated: 2024
 */

jQuery(function($) {
    'use strict';

    // Initialize all browser information
    initializeBrowserInfo();

    // Set up event listeners
    setupEventListeners();

    // Populate copy area
    updateCopyArea();

    /**
     * Initialize all browser information
     */
    function initializeBrowserInfo() {
        // Basic navigator properties
        setElementText('#appCodeName', navigator.appCodeName);
        setElementText('#appName', navigator.appName);
        setElementText('#appVersion', navigator.appVersion);
        setElementText('#userAgent', navigator.userAgent);

        // Display information
        updateDisplayInfo();

        // Referrer information
        const referrer = document.referrer || "無し";
        setElementText('#referrer', referrer);

        // Color depth
        const colorDepth = screen.colorDepth;
        const colorInfo = `${colorDepth}bit (${Math.pow(2, colorDepth).toLocaleString()}色)`;
        setElementText('#colorDepth', colorInfo);

        // Language information
        setElementText('#language', navigator.language || "取得できませんでした。");
        setElementText('#browserLanguage', navigator.browserLanguage || "取得できませんでした。");

        // Capabilities
        setElementText('#cookieEnabled', navigator.cookieEnabled ? "可" : "不可");
        
        // Java support (deprecated but still checking)
        try {
            const javaEnabled = navigator.javaEnabled ? navigator.javaEnabled() : false;
            setElementText('#javaEnabled', javaEnabled ? "可" : "不可");
        } catch (e) {
            setElementText('#javaEnabled', "不可 (非対応)");
        }

        // JavaScript is obviously enabled if this runs
        $("#no-js").html("").after("有効");

        // MIME types
        updateMimeTypes();

        // Plugins
        updatePlugins();

        // Additional modern browser information
        addModernBrowserInfo();
    }

    /**
     * Set element text with error handling
     */
    function setElementText(selector, text) {
        try {
            $(selector).text(text || "情報がありません。");
        } catch (e) {
            console.warn(`Failed to set text for ${selector}:`, e);
        }
    }

    /**
     * Update display and screen information
     */
    function updateDisplayInfo() {
        const displaySize = `${screen.width} × ${screen.height} (pixel)`;
        setElementText('#displaySize', displaySize);

        const screenSize = `${window.innerWidth} × ${window.innerHeight} (pixel)`;
        setElementText('#screenSize', screenSize);
    }

    /**
     * Set up event listeners
     */
    function setupEventListeners() {
        let resizeTimer = null;
        
        // Debounced resize handler
        $(window).on('resize', function() {
            if (resizeTimer) {
                clearTimeout(resizeTimer);
            }
            
            resizeTimer = setTimeout(function() {
                updateDisplayInfo();
                updateCopyArea();
            }, 300); // Reduced delay for better responsiveness
        });

        // Copy button functionality
        $('#copy-button').on('click', function() {
            copyToClipboard();
        });
    }

    /**
     * Update MIME types list
     */
    function updateMimeTypes() {
        try {
            if (!navigator.mimeTypes || navigator.mimeTypes.length === 0) {
                $("#mimeTypes").html("サポートされていないか、情報がありません。");
                return;
            }

            let mimeTypesHtml = "";
            Array.from(navigator.mimeTypes).forEach((mimeType, index) => {
                mimeTypesHtml += `${index + 1} : ${mimeType.type}<br>`;
            });
            
            $("#mimeTypes").html(mimeTypesHtml);
        } catch (e) {
            $("#mimeTypes").html("取得エラー");
        }
    }

    /**
     * Update plugins list
     */
    function updatePlugins() {
        try {
            if (!navigator.plugins || navigator.plugins.length === 0) {
                $("#plugins").html("プラグインが見つからないか、情報がありません。");
                return;
            }

            let pluginsHtml = "";
            Array.from(navigator.plugins).forEach((plugin, index) => {
                pluginsHtml += `${index + 1} : ${plugin.name}<br>`;
                if (plugin.description) {
                    pluginsHtml += ` (${plugin.description})<br>`;
                }
            });
            
            $("#plugins").html(pluginsHtml);
        } catch (e) {
            $("#plugins").html("取得エラー");
        }
    }

    /**
     * Add modern browser information
     */
    function addModernBrowserInfo() {
        // Add touch support detection
        const touchSupport = 'ontouchstart' in window || navigator.maxTouchPoints > 0;
        
        // Add to existing sections or create new info
        if ($('#touchSupport').length) {
            setElementText('#touchSupport', touchSupport ? "対応" : "非対応");
        }

        // Add connection information if available
        if (navigator.connection || navigator.mozConnection || navigator.webkitConnection) {
            const connection = navigator.connection || navigator.mozConnection || navigator.webkitConnection;
            if ($('#connectionType').length && connection.effectiveType) {
                setElementText('#connectionType', connection.effectiveType);
            }
        }

        // Add device memory if available
        if (navigator.deviceMemory && $('#deviceMemory').length) {
            setElementText('#deviceMemory', `${navigator.deviceMemory}GB`);
        }

        // Add hardware concurrency
        if (navigator.hardwareConcurrency && $('#hardwareConcurrency').length) {
            setElementText('#hardwareConcurrency', `${navigator.hardwareConcurrency}コア`);
        }
    }

    /**
     * Update copy area with current information
     */
    function updateCopyArea() {
        try {
            const ipBoxText = $("#ip-box").text();
            $("#copy-ip-address").val(ipBoxText);
        } catch (e) {
            console.warn("Failed to update copy area:", e);
        }
    }

    /**
     * Copy to clipboard functionality
     */
    function copyToClipboard() {
        try {
            const copyText = document.getElementById("copy-ip-address");
            copyText.select();
            copyText.setSelectionRange(0, 99999); // For mobile devices

            // Try modern clipboard API first
            if (navigator.clipboard && window.isSecureContext) {
                navigator.clipboard.writeText(copyText.value).then(function() {
                    showCopyFeedback("コピーしました！");
                }).catch(function() {
                    // Fallback to execCommand
                    fallbackCopy(copyText);
                });
            } else {
                fallbackCopy(copyText);
            }
        } catch (e) {
            showCopyFeedback("コピーに失敗しました", "error");
            console.error("Copy failed:", e);
        }
    }

    /**
     * Fallback copy method
     */
    function fallbackCopy(textElement) {
        try {
            document.execCommand('copy');
            showCopyFeedback("コピーしました！");
        } catch (e) {
            showCopyFeedback("手動でコピーしてください", "warning");
        }
    }

    /**
     * Show copy feedback to user
     */
    function showCopyFeedback(message, type = "success") {
        const button = $('#copy-button');
        const originalText = button.text();
        
        button.text(message);
        button.addClass(`copy-${type}`);
        
        setTimeout(function() {
            button.text(originalText);
            button.removeClass(`copy-${type}`);
        }, 2000);
    }

    // Initialize performance monitoring (optional)
    if (window.performance && window.performance.mark) {
        window.performance.mark('ip-address-check-loaded');
    }
});