/**
 * Voter keys
 * The key pair is generated in the browser: the private key never leaves the page,
 * only the public key is kept so the waiting page can show it again.
 */
const STORAGE_KEY = 'ivote.publicKey';

function toPem(buffer, label) {
    const base64 = btoa(String.fromCharCode(...new Uint8Array(buffer)));
    const lines = base64.match(/.{1,64}/g).join('\n');
    return `-----BEGIN ${label}-----\n${lines}\n-----END ${label}-----`;
}

async function generatePair() {
    const pair = await crypto.subtle.generateKey(
        { name: 'RSASSA-PKCS1-v1_5', modulusLength: 2048, publicExponent: new Uint8Array([1, 0, 1]), hash: 'SHA-256' },
        true,
        ['sign', 'verify']
    );
    const [spki, pkcs8] = await Promise.all([
        crypto.subtle.exportKey('spki', pair.publicKey),
        crypto.subtle.exportKey('pkcs8', pair.privateKey),
    ]);
    return { publicKey: toPem(spki, 'PUBLIC KEY'), privateKey: toPem(pkcs8, 'PRIVATE KEY') };
}

function remember(publicKey) {
    try { localStorage.setItem(STORAGE_KEY, publicKey); } catch (e) { /* storage unavailable */ }
}

function recall() {
    try { return localStorage.getItem(STORAGE_KEY); } catch (e) { return null; }
}

$(function () {
    const $generate = $('.btn-generate');
    if ($generate.length) {
        $generate.on('click', async function () {
            $generate.prop('disabled', true);
            try {
                const keys = await generatePair();
                $('.public-key-area').val(keys.publicKey);
                $('.private-key-area').val(keys.privateKey);
                remember(keys.publicKey);
            } finally {
                $generate.prop('disabled', false);
            }
        });
        return;
    }

    // Waiting page: show the public key generated earlier, renew it on demand
    const publicKey = recall();
    if (publicKey) {
        $('.public-key-area').val(publicKey);
    }
    $('.btn-updatekey').on('click', function () {
        window.location.href = $(this).data('href');
    });
});
