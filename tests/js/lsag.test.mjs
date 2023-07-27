import { test } from 'node:test';
import assert from 'node:assert/strict';
import { generateKeyPair, keyImageOf, linked, publicKeyOf, sign, signatureHash, verify } from '../../resources/js/crypto/lsag.js';

const voters = Array.from({ length: 6 }, generateKeyPair);
const ring = voters.map((v) => v.publicKey);
const ballot = 'election:2|candidate:3';

test('a member of the ring produces a valid signature', () => {
    for (const voter of voters) {
        assert.equal(verify(ballot, ring, sign(ballot, ring, voter.secretKey)), true);
    }
});

test('the signature is bound to the ballot', () => {
    const sig = sign(ballot, ring, voters[0].secretKey);
    assert.equal(verify('election:2|candidate:4', ring, sig), false);
});

test('the signature is bound to the ring', () => {
    const sig = sign(ballot, ring, voters[0].secretKey);
    const other = [...ring.slice(0, 5), generateKeyPair().publicKey];
    assert.equal(verify(ballot, other, sig), false);
    assert.equal(verify(ballot, [...ring].reverse(), sig), false);
});

test('tampering with any component is rejected', () => {
    const sig = sign(ballot, ring, voters[2].secretKey);
    const flip = (hex) => (hex[hex.length - 1] === '0' ? hex.slice(0, -1) + '1' : hex.slice(0, -1) + '0');
    assert.equal(verify(ballot, ring, { ...sig, c0: flip(sig.c0) }), false);
    assert.equal(verify(ballot, ring, { ...sig, s: sig.s.map((h, i) => (i === 3 ? flip(h) : h)) }), false);
    assert.equal(verify(ballot, ring, { ...sig, keyImage: keyImageOf(voters[3].secretKey) }), false);
    assert.equal(verify(ballot, ring, { ...sig, s: sig.s.slice(1) }), false);
});

test('out-of-range scalars and invalid points are rejected', () => {
    const sig = sign(ballot, ring, voters[1].secretKey);
    const zero = '0'.repeat(64);
    const order = 'fffffffffffffffffffffffffffffffebaaedce6af48a03bbfd25e8cd0364141';
    assert.equal(verify(ballot, ring, { ...sig, c0: zero }), false);
    assert.equal(verify(ballot, ring, { ...sig, s: sig.s.map((h, i) => (i ? h : order)) }), false);
    assert.equal(verify(ballot, ring, { ...sig, keyImage: '02' + 'ff'.repeat(32) }), false);
    assert.equal(verify(ballot, ['02' + '00'.repeat(32), ring[1]], sig), false);
});

test('two signatures by the same voter are linked, by different voters are not', () => {
    const first = sign(ballot, ring, voters[4].secretKey);
    const second = sign('election:2|candidate:1', ring, voters[4].secretKey);
    const someoneElse = sign(ballot, ring, voters[5].secretKey);
    assert.equal(linked(first, second), true);
    assert.equal(linked(first, someoneElse), false);
    assert.equal(first.keyImage, keyImageOf(voters[4].secretKey));
});

test('signing is randomised and the key image does not reveal the signer', () => {
    const a = sign(ballot, ring, voters[0].secretKey);
    const b = sign(ballot, ring, voters[0].secretKey);
    assert.notEqual(a.c0, b.c0);
    assert.notEqual(signatureHash(a), signatureHash(b));
    assert.ok(!ring.includes(a.keyImage));
});

test('a signer outside the ring or a degenerate ring is refused', () => {
    const outsider = generateKeyPair();
    assert.throws(() => sign(ballot, ring, outsider.secretKey), /not in the ring/);
    assert.throws(() => sign(ballot, [ring[0]], voters[0].secretKey), /at least two/);
    assert.throws(() => sign(ballot, [ring[0], ring[0]], voters[0].secretKey), /twice/);
    assert.equal(publicKeyOf(outsider.secretKey), outsider.publicKey);
});
