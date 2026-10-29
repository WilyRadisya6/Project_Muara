/* Site performance helpers
 * - Lazy-load images/backgrounds via IntersectionObserver (data-src / data-bg)
 * - Prefetch internal links on hover
 * - Debounce resize events and expose custom 'yummy_resized' event
 * - Defer loading of non-critical images (data-src-lazy) using requestIdleCallback
 */
(function(){
    'use strict';

    document.documentElement.classList.add('has-js');

    function onReady(fn){
        if (document.readyState === 'loading') document.addEventListener('DOMContentLoaded', fn);
        else fn();
    }

    onReady(function(){
        // Lazy load images with data-src
        var lazyImgs = Array.prototype.slice.call(document.querySelectorAll('img[data-src]'));
        if ('IntersectionObserver' in window && lazyImgs.length) {
            var imgObserver = new IntersectionObserver(function(entries, observer){
                entries.forEach(function(entry){
                    if (entry.isIntersecting) {
                        var img = entry.target;
                        if (img.dataset.src) img.src = img.dataset.src;
                        if (img.dataset.srcset) img.srcset = img.dataset.srcset;
                        img.removeAttribute('data-src');
                        observer.unobserve(img);
                    }
                });
            }, {rootMargin: '200px 0px'});
            lazyImgs.forEach(function(img){ imgObserver.observe(img); });
        } else {
            lazyImgs.forEach(function(img){ if (img.dataset.src) img.src = img.dataset.src; if (img.dataset.srcset) img.srcset = img.dataset.srcset; img.removeAttribute('data-src'); });
        }

        // Lazy-load background images via data-bg
        var bgEls = Array.prototype.slice.call(document.querySelectorAll('[data-bg]'));
        if ('IntersectionObserver' in window && bgEls.length) {
            var bgObserver = new IntersectionObserver(function(entries, observer){
                entries.forEach(function(entry){
                    if (entry.isIntersecting) {
                        var el = entry.target;
                        el.style.backgroundImage = 'url(' + el.dataset.bg + ')';
                        observer.unobserve(el);
                    }
                });
            }, {rootMargin: '200px 0px'});
            bgEls.forEach(function(el){ bgObserver.observe(el); });
        } else {
            bgEls.forEach(function(el){ el.style.backgroundImage = 'url(' + el.dataset.bg + ')'; });
        }

        // Prefetch internal links on hover (simple)
        var prefetchSet = new Set();
        function isInternal(href){
            try { var u = new URL(href, location.href); return u.origin === location.origin; } catch(e){ return false; }
        }
        document.querySelectorAll('a[href]').forEach(function(a){
            var href = a.getAttribute('href');
            if (!href || href.indexOf('#') === 0) return;
            if (!isInternal(href)) return;
            a.addEventListener('mouseenter', function(){
                if (prefetchSet.has(href)) return;
                try { var link = document.createElement('link'); link.rel = 'prefetch'; link.href = href; document.head.appendChild(link); prefetchSet.add(href); } catch(e){}
            }, {passive:true});
        });

        // Debounced resize -> custom event
        var resizeTimer;
        window.addEventListener('resize', function(){
            if (resizeTimer) clearTimeout(resizeTimer);
            resizeTimer = setTimeout(function(){ window.dispatchEvent(new Event('yummy_resized')); }, 200);
        }, {passive:true});

        // Load non-critical images when idle
        function loadIdleImages(){
            var idleImgs = Array.prototype.slice.call(document.querySelectorAll('img[data-src-lazy]'));
            idleImgs.forEach(function(img){
                var src = img.dataset.srcLazy || img.dataset.src;
                if (src) img.src = src;
                img.removeAttribute('data-src-lazy');
            });
        }
        if ('requestIdleCallback' in window) {
            requestIdleCallback(loadIdleImages, {timeout:1500});
        } else {
            setTimeout(loadIdleImages, 1500);
        }

    }); // onReady

})();
