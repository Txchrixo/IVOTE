/**
 * Page motion and loading feedback shared by every layout.
 *
 * - Page enter: content is revealed with a short staggered rise (CSS classes below).
 * - Page leave: browsers with cross-document View Transitions use the CSS
 *   @view-transition rule; others get a quick fade-out before navigating.
 * - Loading: a thin progress bar during navigations and AJAX calls, and a spinner
 *   on the submit button of classic forms (which also blocks double submissions).
 */
const reduceMotion = window.matchMedia('(prefers-reduced-motion: reduce)');
const supportsCrossDocumentTransitions = 'PageRevealEvent' in window;

let progressBar;

function progress() {
    if (!progressBar) {
        progressBar = document.createElement('div');
        progressBar.className = 'ui-progress';
        progressBar.setAttribute('aria-hidden', 'true');
        document.body.appendChild(progressBar);
    }
    return progressBar;
}

export function startProgress() {
    const bar = progress();
    bar.classList.remove('is-done');
    // Restart the transition from zero
    bar.classList.remove('is-active');
    void bar.offsetWidth;
    bar.classList.add('is-active');
}

export function endProgress() {
    if (!progressBar || !progressBar.classList.contains('is-active')) return;
    progressBar.classList.add('is-done');
    setTimeout(() => progressBar.classList.remove('is-active', 'is-done'), 350);
}

/** Staggered entrance of the main blocks of back-office and voter pages. */
function revealContent() {
    const blocks = document.querySelectorAll(
        '.main .main-content > .container-fluid > .row > [class*="col"], .main .main-content > .container-fluid > .panel, .main .main-content > .container-fluid > form, .main-content > .container-fluid.bg-white'
    );
    [...blocks].slice(0, 12).forEach((block, index) => {
        block.style.setProperty('--ui-i', index);
        block.classList.add('ui-reveal');
    });
}

function isPlainNavigation(event, link) {
    if (event.defaultPrevented || event.button !== 0) return false;
    if (event.metaKey || event.ctrlKey || event.shiftKey || event.altKey) return false;
    if (link.target && link.target !== '_self') return false;
    if (link.hasAttribute('download') || link.dataset.bsToggle) return false;
    const href = link.getAttribute('href');
    if (!href || href.startsWith('#') || href.startsWith('javascript:')) return false;
    const url = new URL(link.href, window.location.href);
    if (url.origin !== window.location.origin) return false;
    // Same page, only the hash changes
    return !(url.pathname === window.location.pathname && url.search === window.location.search && url.hash);
}

function onLinkClick(event) {
    const link = event.target.closest('a[href]');
    if (!link || !isPlainNavigation(event, link)) return;

    startProgress();

    if (supportsCrossDocumentTransitions || reduceMotion.matches) return;

    event.preventDefault();
    document.body.classList.add('ui-leaving');
    setTimeout(() => {
        window.location.href = link.href;
    }, 140);
}

function onSubmit(event) {
    const form = event.target;
    // AJAX forms call preventDefault in their own handlers, which run before this one
    if (event.defaultPrevented || form.dataset.noLoading !== undefined) return;

    if (form.dataset.submitting) {
        event.preventDefault();
        return;
    }
    form.dataset.submitting = '1';

    const button = event.submitter || form.querySelector('[type="submit"]');
    if (button) {
        button.classList.add('is-loading');
        button.setAttribute('aria-busy', 'true');
        // Disable after the submission started so the button value is still sent
        setTimeout(() => {
            button.disabled = true;
        }, 0);
    }
    startProgress();
}

/** Back/forward cache: the page is shown again as it was left, reset every loading state. */
function onPageShow() {
    document.body.classList.remove('ui-leaving');
    endProgress();
    document.querySelectorAll('form[data-submitting]').forEach((form) => {
        delete form.dataset.submitting;
        form.querySelectorAll('.is-loading').forEach((button) => {
            button.classList.remove('is-loading');
            button.removeAttribute('aria-busy');
            button.disabled = false;
        });
    });
}

/**
 * @param {{ jquery?: Function }} options jQuery instance of the page scripts, to follow their AJAX calls
 */
export function initMotion({ jquery } = {}) {
    document.documentElement.classList.add('ui-js');
    revealContent();

    document.addEventListener('click', onLinkClick);
    document.addEventListener('submit', onSubmit);
    window.addEventListener('pageshow', onPageShow);

    // jQuery AJAX calls of the back-office pages
    if (jquery) {
        jquery(document).on('ajaxStart', startProgress).on('ajaxStop', endProgress);
    }
}
