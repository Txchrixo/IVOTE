// Times LSAG signing and verification for growing rings (median of 5 runs) and prints JSON.
import { generateKeyPair, sign, verify } from '../resources/js/crypto/lsag.js';

const median = (xs) => xs.slice().sort((a, b) => a - b)[Math.floor(xs.length / 2)];
const sizes = [2, 4, 8, 16, 32, 64, 128];
const pool = Array.from({ length: Math.max(...sizes) }, generateKeyPair);
const rows = [];

for (const n of sizes) {
    const ring = pool.slice(0, n).map((k) => k.publicKey);
    const signs = [], verifs = [];
    let bytes = 0;
    for (let r = 0; r < 5; r++) {
        let t = performance.now();
        const sig = sign('election:1|candidate:2', ring, pool[n - 1].secretKey);
        signs.push(performance.now() - t);
        t = performance.now();
        if (!verify('election:1|candidate:2', ring, sig)) throw new Error('verification failed');
        verifs.push(performance.now() - t);
        bytes = 32 * (sig.s.length + 1) + 33;
    }
    // First run hashes the ring (cold, what a voter pays); later runs reuse it (warm, what a tally pays)
    rows.push({ n, signColdMs: Math.round(signs[0]), signMs: Math.round(median(signs)), verifyMs: Math.round(median(verifs)), bytes });
}
console.log(JSON.stringify({ node: process.version, rows }, null, 1));
