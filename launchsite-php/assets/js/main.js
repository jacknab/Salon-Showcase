document.addEventListener('DOMContentLoaded', function () {

    const mobileMenuBtn = document.getElementById('mobileMenuBtn');
    const mobileMenu = document.getElementById('mobileMenu');

    if (mobileMenuBtn && mobileMenu) {
        mobileMenuBtn.addEventListener('click', function () {
            mobileMenu.classList.toggle('is-open');
            mobileMenuBtn.classList.toggle('is-open');
        });
    }

    const filterBtns = document.querySelectorAll('[data-filter]');
    const templateCards = document.querySelectorAll('[data-category]');

    filterBtns.forEach(function (btn) {
        btn.addEventListener('click', function () {
            const filter = btn.getAttribute('data-filter');

            filterBtns.forEach(function (b) { b.classList.remove('is-active'); });
            btn.classList.add('is-active');

            templateCards.forEach(function (card) {
                if (filter === 'all' || card.getAttribute('data-category') === filter) {
                    card.style.display = '';
                    setTimeout(function () { card.classList.add('is-visible'); }, 10);
                } else {
                    card.classList.remove('is-visible');
                    card.style.display = 'none';
                }
            });
        });
    });

    const observer = new IntersectionObserver(function (entries) {
        entries.forEach(function (entry) {
            if (entry.isIntersecting) {
                entry.target.classList.add('in-view');
            }
        });
    }, { threshold: 0.08 });

    document.querySelectorAll('.template-card, .hero-badge, .section-label').forEach(function (el) {
        observer.observe(el);
    });
});
