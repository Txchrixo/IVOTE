import { ballotMessage } from '../crypto/ballot.mjs';
import { sign, signatureHash, verify } from '../crypto/lsag.js';

const ballot = document.querySelector('[data-ballot-form]');
const receiptForm = document.querySelector('[data-verify-ballot]');

if (ballot) {
    const status = ballot.querySelector('[data-ballot-status]');
    const receipt = ballot.querySelector('[data-ballot-receipt]');
    const submit = ballot.querySelector('button[type=submit]');
    const secret = ballot.querySelector('#voting-key');
    const ring = JSON.parse(ballot.dataset.ring);
    let pending;

    submit.disabled = false;
    ballot.addEventListener('submit', async (event) => {
        event.preventDefault();
        const candidate = Number(ballot.elements.candidate.value);
        submit.disabled = true;

        try {
            if (!pending || pending.candidate !== candidate) {
                const signature = sign(ballotMessage(ballot.dataset.reference, candidate), ring, secret.value);
                pending = { candidate, signature };
            }
        } catch {
            status.textContent = 'Use the private key you registered for this election.';
            submit.disabled = false;
            return;
        }

        try {
            const response = await fetch(ballot.dataset.submitUrl, {
                method: 'POST',
                credentials: 'omit',
                headers: {
                    'Accept': 'application/json',
                    'Content-Type': 'application/json',
                },
                body: JSON.stringify(pending),
            });
            const data = await response.json();
            if (!response.ok) {
                status.textContent = data.errors?.signature?.[0] || data.message || 'The ballot could not be accepted.';
                pending = undefined;
                submit.disabled = false;
                return;
            }
            secret.value = '';
            pending = undefined;
            receipt.querySelector('input').value = data.receipt;
            receipt.hidden = false;
            status.textContent = 'Ballot recorded.';
        } catch {
            status.textContent = 'No response from the server. Retry to submit the same signed ballot.';
            submit.disabled = false;
        }
    });
}

if (receiptForm) {
    const status = receiptForm.querySelector('[data-verify-status]');
    const submit = receiptForm.querySelector('button[type=submit]');
    submit.disabled = false;

    receiptForm.addEventListener('submit', async (event) => {
        event.preventDefault();
        const receipt = receiptForm.querySelector('input').value.toLowerCase();
        submit.disabled = true;

        try {
            const response = await fetch(receiptForm.dataset.receiptUrl.replace('RECEIPT', receipt), {
                headers: { 'Accept': 'application/json' },
            });
            if (response.status === 404) {
                status.textContent = 'No ballot with that receipt exists in this election.';
                return;
            }
            if (!response.ok) {
                status.textContent = 'Unable to retrieve the ballot.';
                return;
            }

            const data = await response.json();
            const valid = verify(ballotMessage(data.reference, data.candidate), data.ring, data.signature)
                && signatureHash(data.signature) === receipt;
            if (!valid) {
                status.textContent = 'The ballot signature or receipt is invalid.';
            } else if (data.bitcoin_confirmed) {
                status.textContent = 'Ballot verified. Bitcoin transaction ' + data.bitcoin_txid;
            } else if (data.bitcoin) {
                status.textContent = 'Ballot signature verified. Awaiting Bitcoin confirmation.';
            } else {
                status.textContent = 'Ballot signature verified against the local election record.';
            }
        } catch {
            status.textContent = 'Unable to verify the ballot.';
        } finally {
            submit.disabled = false;
        }
    });
}
