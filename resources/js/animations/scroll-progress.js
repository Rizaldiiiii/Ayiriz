const initScrollProgress = () => {
    const progressEl = document.querySelector('.scroll-progress');
    if (!progressEl) {
        return;
    }

    const updateProgress = () => {
        const scrollTop = window.scrollY;
        const scrollHeight =
            document.documentElement.scrollHeight - window.innerHeight;
        const progress =
            scrollHeight > 0 ? (scrollTop / scrollHeight) * 100 : 0;

        document.documentElement.style.setProperty(
            '--scroll-progress',
            `${progress}%`
        );
    };

    window.addEventListener('scroll', updateProgress, { passive: true });
    updateProgress();
};

if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', initScrollProgress);
} else {
    initScrollProgress();
}
