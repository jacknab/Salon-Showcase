document.addEventListener('DOMContentLoaded', function () {

    /* ── Mobile menu ── */
    var mobileMenuBtn = document.getElementById('mobileMenuBtn');
    var mobileMenu    = document.getElementById('mobileMenu');
    if (mobileMenuBtn && mobileMenu) {
        mobileMenuBtn.addEventListener('click', function () {
            mobileMenu.classList.toggle('is-open');
            mobileMenuBtn.classList.toggle('is-open');
        });
    }

    /* ── Scroll-preview hover effect ──────────────────────────────────────
     * On mouseenter: slow downward pan (CSS handles the transition duration)
     * On mouseleave: snap back to top instantly (remove active class)
     * We drive the transition entirely via CSS classes so it's silky smooth.
     * ------------------------------------------------------------------- */
    document.querySelectorAll('.template-card').forEach(function (card) {
        var scroll = card.querySelector('.preview-scroll');
        if (!scroll) return;

        card.addEventListener('mouseenter', function () {
            scroll.classList.add('is-scrolling');
        });

        card.addEventListener('mouseleave', function () {
            scroll.classList.remove('is-scrolling');
            /* Force reflow so the snap-back transition fires immediately */
            void scroll.offsetHeight;
        });
    });

    /* ── Scroll-in entrance animations ── */
    var observer = new IntersectionObserver(function (entries) {
        entries.forEach(function (entry) {
            if (entry.isIntersecting) {
                entry.target.classList.add('in-view');
                observer.unobserve(entry.target);
            }
        });
    }, { threshold: 0.06 });

    document.querySelectorAll('.template-card, .category-card, .hero-badge, .section-label').forEach(function (el) {
        observer.observe(el);
    });
});
