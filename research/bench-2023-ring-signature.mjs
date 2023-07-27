// Benchmarks the 2023 ring signature code (resources/vendor/ring-signature) unchanged, in Node.
import fs from 'node:fs';
import path from 'node:path';
import vm from 'node:vm';
import { webcrypto } from 'node:crypto';
import { fileURLToPath } from 'node:url';

const dir = process.argv[2] || fileURLToPath(new URL('../resources/vendor/ring-signature', import.meta.url));
const ctx = vm.createContext({ window: { crypto: webcrypto }, navigator: { appName: 'Netscape', appVersion: '5.0' }, console, Math, Date, Uint32Array, Array });
for (const f of ['jsbn.js', 'jsbn2.js', 'prng4.js', 'rng.js', 'rsa.js', 'rsa2.js', 'sha1.js', 'ring.js']) {
  vm.runInContext(fs.readFileSync(path.join(dir, f), 'utf8').replace(/^export default .*$/m, ''), ctx, { filename: f });
}

const run = (code) => vm.runInContext(code, ctx);
const median = (xs) => xs.slice().sort((a, b) => a - b)[Math.floor(xs.length / 2)];

// Key pool: RSA-1024, e = 65537 (as JSEncrypt generated them in 2023)
const MAX = Number(process.argv[3] || 128);
let t = performance.now();
run(`var pool = []; for (var i = 0; i < ${MAX}; i++) { var k = new RSAKey(); k.generate(1024, "10001"); pool.push(k); }`);
const keygen = (performance.now() - t) / MAX;

const rows = [];
for (const n of [2, 4, 8, 16, 32, 64, 128].filter((n) => n <= MAX)) {
  const signs = [], verifs = [];
  let size = 0, ok = true;
  for (let r = 0; r < 5; r++) {
    run(`var ring = pool.slice(0, ${n}); init(ring);`);
    t = performance.now();
    run(`var s = sign("candidate-3", ${n - 1});`);
    signs.push(performance.now() - t);
    t = performance.now();
    ok = ok && run(`verify("candidate-3", s)`) && !run(`verify("candidate-4", s)`);
    verifs.push(performance.now() - t);
    size = Buffer.byteLength(run('JSON.stringify(s)'));
  }
  rows.push({ n, sign: median(signs), verify: median(verifs), size, ok });
}
console.log(JSON.stringify({ node: process.version, keygen, rows }, null, 1));
