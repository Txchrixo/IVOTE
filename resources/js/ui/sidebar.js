const STORAGE_KEY = 'ivote.sidebar';
const desktop = window.matchMedia('(min-width: 992px)');

function store(value) {
    try {
        localStorage.setItem(STORAGE_KEY, value);
    } catch (e) {
        // The sidebar still works if storage is blocked.
    }
}

export function initSidebar() {
    const sidebar = document.getElementById('sidebar-nav');
    if (!sidebar) return;

    const root = document.documentElement;
    const toggles = document.querySelectorAll('[data-sidebar-toggle]');
    const collapseButton = sidebar.querySelector('[data-sidebar-collapse]');
    const backdrop = document.querySelector('[data-sidebar-close]');

    const isCollapsed = () => root.classList.contains('sidebar-collapsed');
    const isDrawerOpen = () => root.classList.contains('sidebar-open');

    function syncLabels() {
        const expanded = desktop.matches ? !isCollapsed() : isDrawerOpen();
        toggles.forEach((toggle) => toggle.setAttribute('aria-expanded', String(expanded)));
        if (collapseButton) {
            const label = isCollapsed() ? collapseButton.dataset.labelExpand : collapseButton.dataset.labelCollapse;
            collapseButton.setAttribute('aria-label', label);
            collapseButton.querySelector('.app-nav-label').textContent = label;
        }
    }

    function setCollapsed(collapsed) {
        root.classList.toggle('sidebar-collapsed', collapsed);
        store(collapsed ? 'collapsed' : 'expanded');
        syncLabels();
    }

    function setDrawer(open) {
        root.classList.toggle('sidebar-open', open);
        if (backdrop) backdrop.hidden = !open;
        syncLabels();
        if (open) sidebar.querySelector('.app-nav-link')?.focus();
    }

    toggles.forEach((toggle) =>
        toggle.addEventListener('click', () => {
            if (desktop.matches) setCollapsed(!isCollapsed());
            else setDrawer(!isDrawerOpen());
        })
    );

    collapseButton?.addEventListener('click', () => {
        setCollapsed(!isCollapsed());
        sidebar.classList.remove('is-peeking');
    });
    backdrop?.addEventListener('click', () => setDrawer(false));

    document.addEventListener('keydown', (event) => {
        if (event.key === 'Escape' && isDrawerOpen()) {
            setDrawer(false);
            toggles[0]?.focus();
        }
    });

    desktop.addEventListener('change', () => {
        setDrawer(false);
        syncLabels();
    });

    sidebar.querySelectorAll('[data-nav-group]').forEach((button) => {
        button.addEventListener('click', () => {
            const group = button.closest('.app-nav-group');
            const open = !group.classList.contains('is-open');
            group.classList.toggle('is-open', open);
            button.setAttribute('aria-expanded', String(open));
        });
    });

    sidebar.addEventListener('focusin', (event) => {
        // Keep mouse clicks from pinning the sidebar open.
        if (event.target.matches(':focus-visible')) sidebar.classList.add('is-peeking');
    });
    sidebar.addEventListener('focusout', (event) => {
        if (!sidebar.contains(event.relatedTarget)) sidebar.classList.remove('is-peeking');
    });

    syncLabels();
}
