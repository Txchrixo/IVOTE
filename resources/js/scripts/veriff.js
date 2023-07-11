/**
 * Veriff page
 *
 * La clé publique Veriff est injectée par le serveur (config/services.php),
 * elle n'est plus écrite en dur dans le code.
 * La décision KYC devra être reçue côté serveur via un webhook signé (Phase 3) :
 * le navigateur ne décide jamais de l'éligibilité.
 */
$(function () {
    const config = document.querySelector('input[data-redirect]');

    const veriff = Veriff({
        host: config.dataset.veriffHost,
        apiKey: config.dataset.veriffKey,
        parentId: 'veriff-root',
        onSession: function (err, response) {
            if (err) {
                console.error('Veriff session error');
                return;
            }
            window.veriffSDK.createVeriffFrame({
                url: response.verification.url,
                lang: document.querySelector('html').lang,
                onEvent: function (msg) {
                    if (msg === 'FINISHED') {
                        window.location.href = config.dataset.redirect;
                    }
                },
                onReload: () => {
                    window.location.reload();
                }
            });
        }
    });
    veriff.setParams({
        person: {
            givenName: ' ',
            lastName: ' '
        },
    });
    veriff.mount({
        submitBtnText: config.dataset.langBtn,
        formLabel: {
            vendorData: config.dataset.langInput
        }
    });

    const veriffInput = document.querySelector('.veriff-text');
    const up_arrow = document.querySelector('.up-arrow');
    if (veriffInput) {
        veriffInput.size = 30;
        veriffInput.type = 'email';

        veriffInput.addEventListener('input', () => {
            if (!up_arrow) return;
            up_arrow.style.transition = ' ease 0.7s';
            up_arrow.style.transform = 'translate(125%, 60%) rotate(250deg)';
            if (veriffInput.value === '' && window.innerWidth > 680) {
                up_arrow.style.transform = 'none';
            }
        });
    }
});
