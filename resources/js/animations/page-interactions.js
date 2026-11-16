const initPageInteractions = () => {
    document.addEventListener('click', (event) => {
        const emoteBtn = event.target.closest('[data-emote-trigger]');
        if (!emoteBtn) {
            return;
        }

        emoteBtn.classList.remove('emote-pop');
        void emoteBtn.offsetWidth;
        emoteBtn.classList.add('emote-pop');
    });
};

if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', initPageInteractions);
} else {
    initPageInteractions();
}
