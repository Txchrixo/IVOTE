const OPEN = 'is-open';

function items(dropdown) {
    return [...dropdown.querySelectorAll('[role="menuitem"]')];
}

function close(dropdown, { focusTrigger = false } = {}) {
    if (!dropdown.classList.contains(OPEN)) return;
    dropdown.classList.remove(OPEN);
    const trigger = dropdown.querySelector('.ui-dropdown-trigger');
    trigger.setAttribute('aria-expanded', 'false');
    if (focusTrigger) trigger.focus();
}

function open(dropdown, { focus = 'current' } = {}) {
    document.querySelectorAll(`[data-dropdown].${OPEN}`).forEach((other) => other !== dropdown && close(other));
    dropdown.classList.add(OPEN);
    dropdown.querySelector('.ui-dropdown-trigger').setAttribute('aria-expanded', 'true');

    const list = items(dropdown);
    const current = list.find((item) => item.getAttribute('aria-current') === 'true');
    const target = focus === 'last' ? list[list.length - 1] : focus === 'first' ? list[0] : current || list[0];
    // Hidden menu items cannot receive focus.
    requestAnimationFrame(() => target?.focus());
}

function move(dropdown, step) {
    const list = items(dropdown);
    const index = list.indexOf(document.activeElement);
    const next = (index + step + list.length) % list.length;
    list[next].focus();
}

export function initDropdowns(root = document) {
    root.querySelectorAll('[data-dropdown]').forEach((dropdown) => {
        if (dropdown.dataset.dropdownReady) return;
        dropdown.dataset.dropdownReady = '1';

        const trigger = dropdown.querySelector('.ui-dropdown-trigger');

        trigger.addEventListener('click', () => {
            dropdown.classList.contains(OPEN) ? close(dropdown) : open(dropdown);
        });

        trigger.addEventListener('keydown', (event) => {
            if (event.key === 'ArrowDown') {
                event.preventDefault();
                open(dropdown, { focus: 'current' });
            } else if (event.key === 'ArrowUp') {
                event.preventDefault();
                open(dropdown, { focus: 'last' });
            }
        });

        dropdown.querySelector('.ui-dropdown-menu').addEventListener('keydown', (event) => {
            switch (event.key) {
                case 'ArrowDown':
                    event.preventDefault();
                    move(dropdown, 1);
                    break;
                case 'ArrowUp':
                    event.preventDefault();
                    move(dropdown, -1);
                    break;
                case 'Home':
                    event.preventDefault();
                    items(dropdown)[0].focus();
                    break;
                case 'End':
                    event.preventDefault();
                    items(dropdown).at(-1).focus();
                    break;
                case 'Escape':
                    event.preventDefault();
                    close(dropdown, { focusTrigger: true });
                    break;
                case 'Tab':
                    close(dropdown);
                    break;
                default:
            }
        });
    });

    if (!document.documentElement.dataset.dropdownListeners) {
        document.documentElement.dataset.dropdownListeners = '1';
        document.addEventListener('click', (event) => {
            document.querySelectorAll(`[data-dropdown].${OPEN}`).forEach((dropdown) => {
                if (!dropdown.contains(event.target)) close(dropdown);
            });
        });
    }
}
