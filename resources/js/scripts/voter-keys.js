import { secp256k1 } from '@noble/curves/secp256k1';
import { registrationDigest } from '../crypto/ballot.mjs';
import { generateKeyPair } from '../crypto/lsag.js';

const form = document.querySelector('[data-registration]');

if (form) {
    const publicKey = form.querySelector('.public-key-area');
    const secretKey = form.querySelector('.private-key-area');
    const storageKey = 'ivote.publicKey.' + form.dataset.voterElection;

    form.querySelector('.btn-generate').addEventListener('click', () => {
        const keys = generateKeyPair();
        publicKey.value = keys.publicKey;
        secretKey.value = keys.secretKey;
        form.querySelector('button[type=submit]').disabled = false;
    });

    form.addEventListener('submit', () => {
        const digest = registrationDigest(form.dataset.voterElection, form.elements.code.value.trim());
        form.elements.proof.value = secp256k1.sign(digest, secretKey.value).toCompactHex();
        try {
            localStorage.setItem(storageKey, publicKey.value);
        } catch {
            // Registration also works without browser storage.
        }
    });
} else {
    const field = document.querySelector('.public-key-area[data-voter-election]');
    if (field) {
        try {
            field.value = localStorage.getItem('ivote.publicKey.' + field.dataset.voterElection) || '';
        } catch {
            // The voter can still open the ballot with their saved private key.
        }
    }
}
