// notifications.js
// robuste : attend DOM + Notyf, queue les appels ntf() faits avant init

(function () {
    'use strict';

    // Config des notifications (code -> { type, message })
    const notificationsConfig = {
        scan_added: { type: 'success', message: 'Scan added successfully.' },
        scan_deleted: { type: 'success', message: 'Scan deleted successfully.' },
        error_upload: { type: 'error', message: 'Error uploading images.' },
        unauthorized: { type: 'error', message: 'Unauthorized action.' },
        // ajoute tes codes ici...
    };

    // Queue pour appels ntf() avant qu'on soit prêt
    const queue = [];
    let notyfInstance = null;

    // Fonction interne pour afficher une notification (doit être appelée quand notyfInstance existe)
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

    // Expose window.ntf immédiatement : met en queue si pas initialisé
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

    // Traite la queue
    function _flushQueue() {
        while (queue.length > 0 && notyfInstance) {
            const c = queue.shift();
            _showNotification(c);
        }
    }

    // Initialise Notyf seulement si la librairie est chargée ET document.body existe
    function tryInitNotyf() {
        if (notyfInstance) return; // déjà initialisé
        if (typeof window.Notyf === 'undefined') return;
        if (!document.body) return; // évite l'erreur appendChild quand body est null

        notyfInstance = new Notyf({
            duration: 5000,
            position: { x: 'right', y: 'bottom' },
            ripple: true,
            dismissible: true
        });

        _flushQueue();
    }

    // Si Notyf n'est pas présent, charge depuis CDN (CSS + JS).
    // Si tu préfères utiliser ta copie locale, supprime ce bloc et inclus Notyf toi-même (avec defer ou en bas de body).
    (function ensureNotyfLoaded() {
        if (typeof window.Notyf !== 'undefined') {
            // déjà chargé
            return;
        }

        // // inject CSS si absent
        // const cssHref = 'https://cdn.jsdelivr.net/npm/notyf/notyf.min.css';
        // if (![...document.styleSheets].some(s => s.href && s.href.includes('notyf'))) {
        //     const link = document.createElement('link');
        //     link.rel = 'stylesheet';
        //     link.href = cssHref;
        //     document.head.appendChild(link);
        // }

        // // inject JS
        // const script = document.createElement('script');
        // script.src = 'https://cdn.jsdelivr.net/npm/notyf/notyf.min.js';
        // script.async = true;
        // script.onload = function () {
        //     // on tente init, mais create se fera seulement si body existe (voir tryInitNotyf)
        //     tryInitNotyf();
        // };
        // script.onerror = function () {
        //     console.warn('notifications.js : échec du chargement de notyf depuis CDN');
        // };
        // document.head.appendChild(script);
    })();

    // Assure l'init après DOMContentLoaded (si body n'existeait pas encore)
    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', function onReady() {
            document.removeEventListener('DOMContentLoaded', onReady);
            tryInitNotyf();
        });
    } else {
        tryInitNotyf();
    }

    // Enfin : si l'URL contient ?ntf=code on le queue immédiatement (sera flushé quand notyf prêt)
    try {
        const params = new URLSearchParams(window.location.search);
        const code = params.get('ntf');
        if (code) window.ntf(code);
    } catch (e) {
        // ignore si URLSearchParams non supporté (très vieux navigateurs)
    }
})();
