function dismiss(toast) {
    if (toast.classList.contains('is-leaving')) return;
    toast.classList.add('is-leaving');
    toast.addEventListener('animationend', () => toast.remove(), { once: true });
    // Reduced motion can skip the animation end event.
    setTimeout(() => toast.remove(), 400);
}

export function initToasts(root = document) {
    root.querySelectorAll('[data-toast]').forEach((toast) => {
        let remaining = Number(toast.dataset.timeout) || 5000;
        let started = Date.now();
        let timer = setTimeout(() => dismiss(toast), remaining);

        const pause = () => {
            clearTimeout(timer);
            remaining -= Date.now() - started;
        };
        const resume = () => {
            started = Date.now();
            clearTimeout(timer);
            timer = setTimeout(() => dismiss(toast), Math.max(remaining, 1500));
        };

        toast.addEventListener('mouseenter', pause);
        toast.addEventListener('mouseleave', resume);
        toast.addEventListener('focusin', pause);
        toast.addEventListener('focusout', resume);
        toast.querySelector('[data-toast-close]')?.addEventListener('click', () => dismiss(toast));
        toast.addEventListener('keydown', (event) => {
            if (event.key === 'Escape') dismiss(toast);
        });
    });
}
