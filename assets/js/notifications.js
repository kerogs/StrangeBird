// notifications.js
(function () {
    'use strict';

    const notificationsConfig = {
        scan_added: { type: 'success', message: 'Scan added successfully.' },
        scan_deleted: { type: 'success', message: 'Scan deleted successfully.' },
        error_upload: { type: 'error', message: 'Error uploading images.' },
        unauthorized: { type: 'error', message: 'Unauthorized action.' },
    };

    const queue = [];
    let notyfInstance = null;

    function _showNotification(code) {
        const cfg = notificationsConfig[code];
        if (!cfg) {
            console.warn(`ntf: code inconnu "${code}"`);
            return;
        }

        switch (cfg.type) {
            case 'success':
                notyfInstance.success(cfg.message);
                break;
            case 'error':
                notyfInstance.error(cfg.message);
                break;
            default:
                notyfInstance.open({ type: cfg.type || 'info', message: cfg.message });
        }
    }

    window.ntf = function (code) {
        if (typeof code !== 'string') {
            console.warn('ntf: code doit être une string');
            return;
        }
        if (notyfInstance) {
            _showNotification(code);
        } else {
            queue.push(code);
        }
    };

    function _flushQueue() {
        while (queue.length > 0 && notyfInstance) {
            const c = queue.shift();
            _showNotification(c);
        }
    }

    function tryInitNotyf() {
        if (notyfInstance) return;
        if (typeof window.Notyf === 'undefined') return;
        if (!document.body) return;

        notyfInstance = new Notyf({
            duration: 5000,
            position: { x: 'right', y: 'bottom' },
            ripple: true,
            dismissible: true
        });

        _flushQueue();
    }

    (function ensureNotyfLoaded() {
        if (typeof window.Notyf !== 'undefined') {
            return;
        }
    })();

    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', function onReady() {
            document.removeEventListener('DOMContentLoaded', onReady);
            tryInitNotyf();
        });
    } else {
        tryInitNotyf();
    }

    try {
        const params = new URLSearchParams(window.location.search);
        const code = params.get('ntf');
        if (code) window.ntf(code);
    } catch (e) {
    }
})();
