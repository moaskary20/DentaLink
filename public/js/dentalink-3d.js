/**
 * DentaLink 3D tilt — mouse-follow depth on cards
 */
(function () {
    'use strict';

    const TILT_SELECTORS = [
        '.fi-wi-stats-overview-stat',
        '.fi-section',
        '.fi-ta-ctn',
        '.fi-simple-main',
        '.portal-card',
        '.dentalink-page .stat-card',
        '.dentalink-page .lab-card',
        '.dentalink-page .card',
        '.dentalink-page .wallet-card',
        '.dentalink-page .ai-card',
    ].join(',');

    const MAX_TILT = 14;
    const PERSPECTIVE = 900;

    function applyTilt(el, x, y) {
        el.style.transform =
            'perspective(' + PERSPECTIVE + 'px) ' +
            'rotateY(' + (x * MAX_TILT) + 'deg) ' +
            'rotateX(' + (-y * MAX_TILT) + 'deg) ' +
            'translateZ(12px)';
    }

    function resetTilt(el) {
        el.style.transform = '';
    }

    function bindTilt(el) {
        if (el.dataset.dlTiltBound) return;
        el.dataset.dlTiltBound = '1';
        el.classList.add('dl-tilt-3d');

        el.addEventListener('mousemove', function (e) {
            const rect = el.getBoundingClientRect();
            const x = (e.clientX - rect.left) / rect.width - 0.5;
            const y = (e.clientY - rect.top) / rect.height - 0.5;
            applyTilt(el, x, y);
        });

        el.addEventListener('mouseleave', function () {
            resetTilt(el);
        });
    }

    function init() {
        document.querySelectorAll(TILT_SELECTORS).forEach(bindTilt);
    }

    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', init);
    } else {
        init();
    }

    document.addEventListener('livewire:navigated', init);
})();
