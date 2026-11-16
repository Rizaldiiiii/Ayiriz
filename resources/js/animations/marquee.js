const initMarquee = () => {
    const marquees = document.querySelectorAll('.marquee');
    if (marquees.length === 0) {
        return;
    }

    marquees.forEach((marquee) => {
        const track = marquee.querySelector('.marquee-track');
        if (!track) {
            return;
        }

        marquee.addEventListener('focusin', () => {
            track.style.animationPlayState = 'paused';
        });

        marquee.addEventListener('focusout', () => {
            track.style.animationPlayState = '';
        });
    });
};

if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', initMarquee);
} else {
    initMarquee();
}
