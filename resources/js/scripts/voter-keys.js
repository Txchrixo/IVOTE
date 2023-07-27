/**
 * Voter keys
 * The secp256k1 key pair used for linkable ring signatures is generated in the browser:
 * the secret key never leaves the page, only the public key is kept so the waiting page
 * can show it again.
 */
import { generateKeyPair } from '../crypto/lsag.js';

const STORAGE_KEY = 'ivote.publicKey';

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
                const keys = generateKeyPair();
                $('.public-key-area').val(keys.publicKey);
                $('.private-key-area').val(keys.secretKey);
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
