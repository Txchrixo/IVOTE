# Research scripts

Reproducible experiments behind the *Cryptography* and *Evaluation* sections of the main README.
They run with Node 18+ from the repository root, after `npm install`.

| Script | What it shows |
|---|---|
| `forge-2023-ring-signature.mjs [dir] [runs]` | Forges ballots against the 2023 ring signature (`resources/vendor/ring-signature`) using public keys only |
| `bench-2023-ring-signature.mjs [dir] [maxRing]` | Signing and verification time of the 2023 scheme by ring size |
| `bench-lsag.mjs` | Signing and verification time of the current linkable ring signature (`resources/js/crypto/lsag.js`) |

## Finding: the 2023 ring signature is forgeable

The prototype used the Rivest–Shamir–Tauman construction with RSA-1024 keys, but set the common
domain to `L = 1024` bits, the size of the moduli themselves. RST extend each trapdoor
permutation to `[0, 2^L)` and need `L ≥ |n| + 160` so that the part of the domain where the
extension is the identity is negligible. With `L = |n|`, that part is large: on
`[⌊(2^L − 1)/n⌋ · n, 2^L)`, `g(x) = x`. A forger runs the signing equations and, at the point
where the real signer applies their private key, picks a value inside that zone instead. No
private key is needed.

```
$ node research/forge-2023-ring-signature.mjs "" 20
{"runs":20,"ringSize":5,"forgeriesAccepted":20,"acceptedForAnotherVote":0}
```

Other weaknesses of the same code: SHA-1 as the combining function, an RC4-based PRNG
(`prng4.js`), the ring not bound to the challenge, and no linkability, so double voting could
only be caught by bookkeeping. The current scheme (LSAG on secp256k1) removes all of them; the
old files stay in `resources/vendor/ring-signature` as a record of the thesis prototype; no page
loads `ring.js` or its RSA helpers any more.
