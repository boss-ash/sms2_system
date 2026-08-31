/**
 * Tabler Icons for JS-rendered UI
 */
(function (global) {
    'use strict';

    var FA_TO_TABLER = {
        'shield-alt': 'shield',
        'shield-halved': 'shield',
        'sign-in-alt': 'login',
        'sign-out-alt': 'logout',
        'right-to-bracket': 'login',
        'th-large': 'layout-grid',
        'info-circle': 'info-circle',
        'exclamation-triangle': 'alert-triangle',
        'exclamation-circle': 'alert-circle',
        'check-circle': 'circle-check',
        'times': 'x',
        'eye': 'eye',
        'eye-slash': 'eye-off',
        'key': 'key',
        'lock': 'lock',
        'envelope': 'mail',
        'bell': 'bell',
        'user': 'user',
        'search': 'search',
        'chevron-down': 'chevron-down',
        'chevron-right': 'chevron-right',
        'circle': 'circle'
    };

    function tablerName(icon) {
        var name = String(icon || '').trim();
        name = name.replace(/^(fa[srb]?|fa-solid|fa-regular|fa-brands)\s+fa-/i, 'fa-');
        name = name.replace(/^(fa[srb]?|fa-solid|fa-regular|fa-brands)\s+/i, '');
        name = name.replace(/^fa-/, '');
        if (FA_TO_TABLER[name]) {
            return FA_TO_TABLER[name];
        }
        return name.replace(/_/g, '-');
    }

    function iconClass(icon, extraClass) {
        var cls = 'ti ti-' + tablerName(icon);
        if (extraClass) {
            cls += ' ' + extraClass;
        }
        return cls;
    }

    function iconHtml(icon, extraClass, attrs) {
        attrs = attrs || {};
        var classes = iconClass(icon, extraClass);
        var html = '<i class="' + classes + '"';
        Object.keys(attrs).forEach(function (key) {
            if (attrs[key] === true) {
                html += ' ' + key;
            } else if (attrs[key] != null && attrs[key] !== false) {
                html += ' ' + key + '="' + String(attrs[key]).replace(/"/g, '&quot;') + '"';
            }
        });
        html += '></i>';
        return html;
    }

    global.smsTablerName = tablerName;
    global.smsIconClass = iconClass;
    global.smsIconHtml = iconHtml;
})(window);
