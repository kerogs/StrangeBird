// notifications.js
(function () {
    'use strict';

    const notificationsConfig = {
        scan_added: { type: 'success', message: 'Scan added successfully.' },
        scan_deleted: { type: 'success', message: 'Scan deleted successfully.' },
        error_upload: { type: 'error', message: 'Error uploading images.' },
        unauthorized: { type: 'error', message: 'Unauthorized action.' },
        empty_fields: { type: 'error', message: 'Please fill all fields.' },
        invalid_credentials: { type: 'error', message: 'Invalid credentials.' },
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

    function initNotyf() {
        if (notyfInstance) return;
        if (typeof Notyf === 'undefined') {
            console.warn('Notyf non chargé');
            return;
        }

        notyfInstance = new Notyf({
            duration: 5000,
            position: { x: 'right', y: 'bottom' },
            ripple: true,
            dismissible: true
        });

        _flushQueue();

        // Traiter les notifications depuis l'URL après l'initialisation
        try {
            const params = new URLSearchParams(window.location.search);
            const code = params.get('ntf');
            if (code) window.ntf(code);
        } catch (e) {
            console.error('Erreur lecture paramètres URL', e);
        }
    }

    // Attendre que le DOM soit complètement chargé
    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', function () {
            // Petit délai pour s'assurer que Notyf est bien chargé
            setTimeout(initNotyf, 100);
        });
    } else {
        // DOM déjà chargé, petit délai pour Notyf
        setTimeout(initNotyf, 100);
    }
})();