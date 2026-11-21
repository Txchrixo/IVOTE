<p align="center">
  <img src="resources/img/ivote-purple.svg" alt="IVote" width="200">
</p>

<h3 align="center">Design and Implementation of an Electronic Voting System<br>Based on Blockchain and Cryptographic Techniques</h3>

<p align="center">
  <img src="docs/assets/cameroon.svg" alt="Flag of Cameroon" width="21" height="14">&nbsp; Bachelor's thesis · IUT de Douala, University of Douala, Cameroon · 2023<br>
  <sub>Proof of concept: Bitcoin testnet only, not meant for binding elections.</sub>
</p>

<p align="center">
  <img src="https://img.shields.io/badge/status-proof%20of%20concept-orange" alt="Proof of concept">
  <img src="https://img.shields.io/badge/PHP-8.1%20%E2%86%92%208.4%2B-777bb4" alt="PHP 8.1, source release uses 8.4+">
  <img src="https://img.shields.io/badge/Laravel-10%20%E2%86%92%2012-ff2d20" alt="Laravel 10, upgraded to 12">
  <img src="https://img.shields.io/badge/Bitcoin-testnet-f7931a" alt="Bitcoin testnet">
  <img src="https://img.shields.io/badge/anonymity-ring%20signatures-7c5aed" alt="Ring signatures">
  <img src="https://img.shields.io/badge/license-GPL--3.0--or--later-blue" alt="GPL-3.0-or-later">
</p>

<p align="center">
  <a href="#abstract">Abstract</a> ·
  <a href="#adapting-the-design-to-cameroon">Cameroon</a> ·
  <a href="#protocol">Protocol</a> ·
  <a href="#cryptography">Cryptography</a> ·
  <a href="#evaluation">Evaluation</a> ·
  <a href="#election-walkthrough">Walkthrough</a> ·
  <a href="#design">Design</a> ·
  <a href="#related-work">Related work</a> ·
  <a href="#getting-started">Getting started</a> ·
  <a href="#citation">Citation</a>
</p>

---

| | |
|---|---|
| **Author** | [NANA TCHOUNKEU Thierry Christian](https://orcid.org/0009-0009-1526-7131) [<img src="docs/assets/orcid.svg" alt="ORCID iD" width="16" height="16">](https://orcid.org/0009-0009-1526-7131) |
| **Academic supervisor** | [Dr. Jean de Dieu NKAPKOP](https://orcid.org/0000-0001-7736-5145) [<img src="docs/assets/orcid.svg" alt="ORCID iD" width="16" height="16">](https://orcid.org/0000-0001-7736-5145), University of Douala |
| **Professional supervisor** | Mr. DJIMELI Christian |
| **Degree** | Licence de Technologie, Software Engineering |
| **Institution** | Institut Universitaire de Technologie (IUT), University of Douala |
| **Host company** | CODEC, Douala |
| **Year** | 2023 |
| **Defense slides** | [PDF, 41 slides](docs/slides/ivote-defense-2023.pdf) |

## Abstract

IVote is an electronic voting prototype. It uses **linkable ring signatures** [3] to
verify that a ballot came from a registered key without identifying the signer. The ballot
box accepts one ballot per key image and returns a receipt. In Bitcoin mode, a transaction
on testnet or regtest commits the receipt in an `OP_RETURN` output. Local mode runs the vote,
tally and receipt checks without a Bitcoin node.

The project was designed for the Cameroonian electoral context using UML and 2TUP, and
built with PHP, Laravel and MySQL. It draws on Wu’s ring-signature voting work [8] and adds
remote identity checks, linkable ring signatures, two-factor login for authorities and an
audit log.

<p align="center">
  <img src="docs/slides/02-idea.jpg" alt="E-voting + Blockchain + Ring signature = IVote" width="720">
</p>

### Contributions

1. **A process built for Cameroon**: identity checked remotely on Cameroonian documents with Veriff,
   a hand-off to the voter's phone, distinct election and registration authorities, a bilingual interface.
2. **A security review of the reused ring signature**: the RSA-based scheme could be forged with public
   keys only (20 forgeries out of 20), and a reproducible attack is provided.
3. **Linkable ring signatures** (LSAG on secp256k1) replace the prototype signature. The tests cover
   signature verification and rejection of a second ballot from the same registered key.
4. **A revised protocol**: one ballot per key image, public signed ballots after closing
   and an optional 32-byte `OP_RETURN` commitment paid by the test wallet.
5. **A hardened back office**: TOTP two-factor sign-in, role gates, nonce-based CSP, rate limits, audit log.
6. **An evaluation**: signing and verification cost by ring size, on-chain cost per ballot, a threat model.

## Adapting the design to Cameroon

Earlier blockchain voting prototypes [7, 8] target boardrooms or university elections with
pre-issued ballot codes. IVote is designed around how elections are organised in Cameroon:

| Cameroonian reality | What IVote does |
|---|---|
| Elections are organised by an independent body, Elections Cameroon (ELECAM) | The Election Authority plays that role; registration is handed to a separate Registration Authority so that no single party holds both voters' identities and their ballots |
| Identity rests on the national identity card (CNI), passport or residence permit | Remote identity check with Veriff on these Cameroonian documents, and a CNI field for candidates |
| Most citizens are online through a smartphone rather than a computer | Veriff hands the check over to the voter's phone by QR code or SMS |
| English and French are both official languages | The whole interface is bilingual, with a language switch on every page |
| Disputed results erode trust in the outcome | After the election closes, anyone can download and verify the signed ballots |

The 2023 demonstration simulated a municipal election. This remains a proof of concept: it does
not claim compliance with the Cameroonian electoral code and was not endorsed by ELECAM.

## Protocol

Three actors take part: the **Election Authority** (EA), the **Registration Authority** (RA)
and the **voter** V<sub>i</sub>.

| Notation | Meaning |
|---|---|
| G, q | generator and order of secp256k1 |
| x<sub>i</sub>, P<sub>i</sub> = x<sub>i</sub>G | voter key pair, generated in the browser; x<sub>i</sub> never leaves it |
| R = (P<sub>1</sub>, …, P<sub>n</sub>) | ring: every registered public key of the election, frozen when registration closes |
| H<sub>p</sub> | hash to curve (RFC 9380 [16]) |
| I<sub>i</sub> = x<sub>i</sub>H<sub>p</sub>(P<sub>i</sub>) | key image: identical in every signature made with x<sub>i</sub>, unlinkable to P<sub>i</sub> |
| m = (L, C) | ballot: election identifier L and candidate C |
| σ = (c<sub>0</sub>, s<sub>1</sub>…s<sub>n</sub>, I) | LSAG signature of m by some member of R |

1. **Setup.** The EA creates the election and the RA registers candidates and issues one-time codes.
2. **Identification.** The RA hands codes to eligible voters. With Veriff configured, a signed approval for the code's session is also required.
3. **Key registration.** V<sub>i</sub> proves possession of a fresh key and registers P<sub>i</sub> with the code. Opening voting freezes R and the candidate list.
4. **Vote.** The browser signs (L, C) with LSAG. The server verifies the signature and accepts at most one ballot per key image. The voter saves H(σ) as a receipt.
5. **Bitcoin anchoring.** In Bitcoin mode, the authority wallet commits H(σ) in an `OP_RETURN` output. The tally waits for a confirmation.
6. **Tally and verification.** After closing, the voter can check their receipt in the browser. Anyone can download the public ring and ballots, verify each signature and recompute the tally. Bitcoin transaction IDs are included when available.

| Property | Mechanism | Status |
|---|---|---|
| Eligibility | RA-issued codes and optional Veriff approval. Only keys in R can produce a valid σ | implemented |
| Unforgeability | LSAG, secure under the discrete logarithm assumption in the random oracle model [3] | implemented, tested |
| One registered key, one vote | the ballot box rejects a second ballot with the same key image | implemented, tested |
| Ballot privacy | signer ambiguity among all n keys of R [3] | signature tested |
| Integrity | the receipt binds the signature; Bitcoin mode commits it on-chain | implemented |
| Individual and universal verifiability | receipt verification, public ballots and a deterministic tally after closing | implemented, tested |
| Authority accountability | TOTP 2FA, role gates, audit log, staged life cycle | implemented |
| Coercion resistance | not addressed: a voter can prove their vote by revealing x<sub>i</sub> | out of scope |

The [`LSAG tests`](tests/js/lsag.test.mjs) cover the signature scheme.
The [`voting flow tests`](tests/Feature/VotingFlowTest.php) cover code issuance, registration, ballot validation and the closed-election tally.

## Cryptography

### From the 2023 prototype to LSAG

Reviewing the prototype for this repository showed that its ring signature, inherited from
an open-source implementation [8], **could be forged with public keys only**. It used the Rivest-Shamir-Tauman
construction [2] with RSA-1024 keys but a common domain of `L = 1024` bits. RST require
`L ≥ |n| + 160`, because their extended trapdoor `g` is the identity on
`[⌊(2^L−1)/n⌋·n, 2^L)`; with `L = |n|` that zone is large, and a forger closes the ring there
without inverting RSA. The attack succeeded in 20 out of 20 runs
([`research/forge-2023-ring-signature.mjs`](research/forge-2023-ring-signature.mjs)).

| | 2023 prototype | Current scheme |
|---|---|---|
| Scheme | RST ring signature [2] | LSAG linkable ring signature [3] |
| Keys | RSA-1024, generated with JSEncrypt | secp256k1, the curve of Bitcoin |
| Hash | SHA-1 | SHA-256, domain-separated; RFC 9380 hash to curve |
| Randomness | RC4-based PRNG seeded once | platform CSPRNG for every scalar |
| Ring bound to the signature | no, keys travel inside σ | yes, the ring digest enters every challenge |
| Double vote | detected only by counting one ballot per address | detected by the key image, whatever the address |
| Forgery without a private key | **possible** (20/20) | none known; off-curve points and out-of-range scalars are rejected |
| Signature size | ≈ 640 B per ring member | 32 B per ring member + 65 B |

The implementation relies on the audited `@noble/curves` and `@noble/hashes` libraries and is
covered by tests for correctness, binding to the ballot and to the ring, tampering, invalid
encodings, linkability and randomisation ([`tests/js/lsag.test.mjs`](tests/js/lsag.test.mjs)).

### Threat model

| Adversary | Can | Cannot |
|---|---|---|
| Public observer | read every ballot and recount | tell which registered voter cast a ballot |
| Curious EA | see ballots, refuse to fund or serve addresses (visibly) | forge or alter ballots, link a ballot to a voter |
| Curious RA, Veriff | link a person to P<sub>i</sub> | link P<sub>i</sub> to a ballot (the key image hides it) |
| Dishonest voter | vote once, abstain, reveal their own vote | vote twice, vote outside the ring |
| Network observer | link the IP that fetched an address or published σ | anything more, if voters use an anonymity network such as Tor |

Assumptions: the EA and the RA do not collude; voters' browsers are not compromised; the
discrete logarithm problem on secp256k1 is hard; the Bitcoin network keeps a single honest
history. The bulletin board must keep every σ available; since each H(σ) is on-chain, a
withheld or altered σ is detected, not hidden.

## Evaluation

Timings are medians of 5 runs on an Intel Core i7-10610U laptop with Node.js, produced by the
scripts in [`research/`](research/). Transaction sizes follow the Bitcoin serialisation format.

### Ring signatures

| Ring size n | 2023 sign | 2023 verify | 2023 size | LSAG sign | LSAG verify | LSAG size |
|---:|---:|---:|---:|---:|---:|---:|
| 2 | 118 ms | 21 ms | 1.3 KB | 50 ms | 42 ms | 129 B |
| 8 | 199 ms | 53 ms | 5.2 KB | 133 ms | 137 ms | 321 B |
| 32 | 742 ms | 279 ms | 20.6 KB | 539 ms | 516 ms | 1.1 KB |
| 64 | 1.22 s | 437 ms | 41.1 KB | 1.03 s | 1.04 s | 2.1 KB |
| 128 | 1.68 s | 740 ms | 82.0 KB | 2.71 s | 2.94 s | 4.2 KB |

LSAG costs about 22 ms per ring member to sign or verify, linear in n, and its signatures are
20 times smaller. The 2023 scheme verifies faster (RSA with e = 65537) but is forgeable. A
tally checks every ballot against the same ring, so n ballots cost O(n²) operations, about
6 minutes for a ring of 128; rings of a few hundred voters, for example one per polling
station, are the practical size.

### On-chain cost

| Transaction | Size | Fee at 2 sat/vB | at 10 sat/vB | at 50 sat/vB |
|---|---:|---:|---:|---:|
| Ballot: 1 P2PKH input, `OP_RETURN` of 68 B, 1 P2PKH output | 271 B | 542 sat | 2,710 sat | 13,550 sat |
| Funding: each pool address in a batched transaction | 34 B | 68 sat | 340 sat | 1,700 sat |

The 2023 code paid a fixed fee of 0.01 BTC per ballot, about 4,000 sat/vB for its 249-byte
transaction. A ballot is recorded once its transaction is mined, about 10 minutes on average,
and is commonly treated as final after 6 confirmations, about one hour; these are properties
of Bitcoin, not measurements of IVote.

## Election walkthrough

An election moves through five stages: **Initial → Identity check → Waiting → Voting → Closed**.
Screens below come from the demo data set (`DemoSeeder`); wallet balances are mocked since the demo has no funded wallets.

### Authorities

<table>
  <tr>
    <td width="33%"><img src="docs/screenshots/01-login.jpg" alt="Sign in"></td>
    <td width="33%"><img src="docs/screenshots/02-two-factor.jpg" alt="Two-factor authentication"></td>
    <td width="33%"><img src="docs/screenshots/03-ea-dashboard.jpg" alt="Authority dashboard"></td>
  </tr>
  <tr>
    <td align="center"><b>1.</b> Back-office sign-in</td>
    <td align="center"><b>1.</b> TOTP second factor</td>
    <td align="center"><b>1.</b> EA dashboard, three chains</td>
  </tr>
  <tr>
    <td><img src="docs/screenshots/04-ea-wallet.jpg" alt="Wallet settings"></td>
    <td><img src="docs/screenshots/05-ea-create-election.jpg" alt="Create an election"></td>
    <td><img src="docs/screenshots/06-ea-elections.jpg" alt="Election list"></td>
  </tr>
  <tr>
    <td align="center"><b>1.</b> EA testnet wallet</td>
    <td align="center"><b>1.</b> New election</td>
    <td align="center"><b>1.</b> Elections and their stage</td>
  </tr>
  <tr>
    <td><img src="docs/screenshots/09-ra-dashboard.jpg" alt="Registration dashboard"></td>
    <td><img src="docs/screenshots/08-ra-add-candidate.jpg" alt="Add a candidate"></td>
    <td><img src="docs/screenshots/07-ra-candidates.jpg" alt="Candidates"></td>
  </tr>
  <tr>
    <td align="center"><b>1.</b> RA dashboard</td>
    <td align="center"><b>1.</b> New candidate</td>
    <td align="center"><b>1.</b> Candidates per election</td>
  </tr>
  <tr>
    <td><img src="docs/screenshots/12-ra-ballot-codes.jpg" alt="Ballot codes"></td>
    <td><img src="docs/screenshots/16-ea-fund-addresses.jpg" alt="Fund voter addresses"></td>
    <td><img src="docs/screenshots/17-ea-balances.jpg" alt="Address balances"></td>
  </tr>
  <tr>
    <td align="center"><b>2.</b> Personal ballot codes</td>
    <td align="center"><b>4.</b> Funding voter addresses</td>
    <td align="center"><b>4.</b> Address balances</td>
  </tr>
</table>

### Identity verification and voting

<table>
  <tr>
    <td width="33%"><img src="docs/screenshots/10-voter-veriff.jpg" alt="Identity check page"></td>
    <td width="33%"><img src="docs/screenshots/11-voter-veriff-submitted.jpg" alt="Verification submitted"></td>
    <td width="33%"><img src="docs/screenshots/15-voter-wait.jpg" alt="Waiting for the vote"></td>
  </tr>
  <tr>
    <td align="center">Identity check</td>
    <td align="center">Check submitted</td>
    <td align="center">Waiting for the voting day</td>
  </tr>
</table>

For the current voting and receipt screens, follow [the local demo](#getting-started).

## Design

The analysis follows the 2TUP process with UML. Click a diagram to open it full size.

<table>
  <tr>
    <td width="50%"><img src="docs/diagrams/use-cases.jpg" alt="Use case diagram"></td>
    <td width="50%"><img src="docs/diagrams/class-diagram.jpg" alt="Class diagram"></td>
  </tr>
  <tr>
    <td align="center">Use cases</td>
    <td align="center">Classes</td>
  </tr>
  <tr>
    <td><img src="docs/diagrams/sequence-identification.jpg" alt="Identification sequence"></td>
    <td><img src="docs/diagrams/sequence-vote.jpg" alt="Vote sequence"></td>
  </tr>
  <tr>
    <td align="center">Sequence: identification with Veriff</td>
    <td align="center">Sequence: casting a vote</td>
  </tr>
  <tr>
    <td><img src="docs/diagrams/sequence-verification.jpg" alt="Verification sequence"></td>
    <td><img src="docs/diagrams/sequence-authentication.jpg" alt="Authentication sequence"></td>
  </tr>
  <tr>
    <td align="center">Sequence: verifying a vote</td>
    <td align="center">Sequence: authority sign-in</td>
  </tr>
  <tr>
    <td colspan="2" align="center"><img src="docs/diagrams/deployment.jpg" alt="Deployment diagram" width="528"></td>
  </tr>
  <tr>
    <td colspan="2" align="center">Deployment (2023): WampServer, the IVote app, the voter's phone for Veriff, SoChain and Bitcoin testnet</td>
  </tr>
</table>

<details>
<summary><b>Project timeline (18 weeks)</b></summary>
<br>
<img src="docs/diagrams/gantt.jpg" alt="Gantt chart">
</details>

<details>
<summary><b>Defense slides</b></summary>
<br>
<table>
  <tr>
    <td width="33%"><img src="docs/slides/01-title.jpg" alt="Title slide"></td>
    <td width="33%"><img src="docs/slides/03-identification.jpg" alt="Identification protocol slide"></td>
    <td width="33%"><img src="docs/slides/04-vote.jpg" alt="Vote protocol slide"></td>
  </tr>
</table>
<p>Full deck: <a href="docs/slides/ivote-defense-2023.pdf">ivote-defense-2023.pdf</a> (GitHub shows it in the browser).</p>
</details>

## Architecture

```
 Voter browser ── key pair, ring signature (private key stays here)
        │
        ▼
 Laravel app ── MySQL (elections, candidates, public keys, audit log)
        │
        ├── Veriff ............ identity verification (document + selfie)
        ├── Block.io .......... funding voter addresses, OP_RETURN on Bitcoin testnet
        ├── CryptoAPIs ........ EA balance on Bitcoin testnet and Ethereum Goerli
        └── Solana RPC ........ EA balance on Solana devnet
```

### Stack

The thesis version (July 2023) ran on WampServer. Later changes upgraded Laravel, replaced
the ballot protocol and updated the voting and verification screens.

| Layer | Thesis version (2023) | Current version |
|---|---|---|
| Back end | PHP 8.1, Laravel 10, MySQL 8 on WampServer (Apache) | PHP 8.4.1+ with the lockfile, Laravel 12, MySQL 8 |
| Front end | Blade, Bootstrap 5.3, jQuery 3.7, Vite 4, Chartist | same, Vite 6 |
| Blockchain | Bitcoin testnet (`OP_RETURN`), BitcoinECDSA, BitcoinJS, Block.io, SoChain API | Block.io, CryptoAPIs (SoChain's free API was retired), Ethereum and Solana balances |
| Cryptography | RST ring signatures, RSA-1024 voter keys, secp256k1 Bitcoin keys | LSAG on secp256k1 (`@noble/curves`), authority keys encrypted at rest (AES-256) |
| Identity and access | Veriff, password sign-in for the authorities | Veriff, TOTP 2FA, role gates, CSP with nonces, rate limits, audit log |
| Method | 2TUP, UML (Visual Paradigm), Figma mock-ups | same, automated feature tests |

<p align="center"><img src="docs/diagrams/tools.jpg" alt="Tools used" width="640"></p>

## Related work

IVote uses ring signatures to hide the signer and can publish ballot receipts to Bitcoin.
The signed ballots themselves are published by the application after the election closes.

| Work | Year | Ledger | Anonymity | Tally | Identity |
|---|---|---|---|---|---|
| Zhao & Chan [6] | 2015 | Bitcoin | zero-knowledge proofs | on-chain deposits | not covered |
| Open Vote Network [7] | 2017 | Ethereum | self-tallying, ZK proofs | smart contract | registered voters |
| BlockVotes [8] | 2017 | Bitcoin | ring signatures | public, off-chain | ballot codes |
| Hjálmarsson et al. [9] | 2018 | permissioned chain | trusted authority | smart contract | trusted authority |
| Yu et al. [10] | 2018 | Hyperledger Fabric | linkable ring signatures | homomorphic | registered voters |
| Chirotonia [11] | 2021 | Ethereum | linkable ring signatures | smart contract | registered voters |
| **IVote** | 2023 | Bitcoin testnet | linkable ring signatures | public, off-chain | Veriff on Cameroonian ID + email link |

Surveys [12-15] cover the wider landscape and its open problems (scalability, coercion, usability).

## Getting started

The Composer lockfile needs PHP 8.4.1 or newer with `gmp`. Use Composer 2 and Node.js
18.x, 20.x or 22+. MySQL 8 is the default database.

From the extracted source archive, start with `composer install`. Configure `.env` to
use an empty database before running migrations.

```bash
git clone https://github.com/Txchrixo/IVOTE.git && cd IVOTE
composer install && npm ci
cp .env.example .env && php artisan key:generate
php artisan migrate --seed    # creates the EA and RA accounts, prints their passwords
npm run build && php artisan serve
```

**Demo data.** To load five sample elections and sign in as `ea` or `ra`, run this on a disposable database:

```bash
php artisan migrate:fresh --seed --seeder=DemoSeeder
```

Accounts `ea` and `ra` share a password and TOTP secret, both printed once
(or set through `DEMO_PASSWORD` and `DEMO_2FA_SECRET`). The seeder refuses to run in production.

**Run a new election locally.**

1. As EA, create an election and set the verification and voting dates. As RA, add two candidates and generate two codes under **Ballot codes**.
2. As EA, advance to **Veriff**. Open `/voter/generateKeys` in two voter sessions, generate and save a separate private key in each, then register each public key with its code.
3. As EA, advance through **Waiting** to **Ongoing**. Open `/voter/vote`, choose the election and sign with one registered private key. Save the receipt. A second submission signed by that key for another candidate is rejected.
4. As EA, close the election. Open `/tally/home` to see the count and `/verify/home` to check the receipt. The sample elections illustrate stages. Use a new election for a complete run.

**Veriff sandbox.** Set `VERIFF_HOST`, `VERIFF_API_KEY` and `VERIFF_SECRET_KEY` from the same Test integration in Veriff Station. Register the HTTPS decision webhook at `/api/veriff/decision`. Test decisions are set in Veriff Station.

**Bitcoin anchoring.** Set `BITCOIN_RPC_URL` to a funded Bitcoin Core wallet endpoint and configure its RPC username and password before opening the election. Use `-testnet4` for the public Testnet4 network or use regtest for a local run. Testnet3 is also accepted. Run `php artisan ivote:anchor ELECTION_ID` to publish receipts, then rerun it after a block confirms the transactions. `BLOCKIO_*` and `CRYPTOAPIS_API_KEY` are used by the existing back-office tools.

## Tests

```bash
php artisan test     # back end
npm run test:js      # linkable ring signature
```

Feature tests cover authorization, two-factor sign-in, election stages, candidates, blockchain
balances, security headers, the audit log and the public results pages.

## Repository layout

```
app/          controllers, models, election stages, security middleware
database/     migrations and seeders (including DemoSeeder)
resources/    Blade views, styles, page scripts, LSAG module (js/crypto)
routes/       web routes (back office, voter flow, results)
tests/        feature tests (PHP) and ring signature tests (JS)
research/     forgery of the 2023 scheme, benchmarks
docs/         screenshots, UML diagrams, defense slides
```

## Limitations and future work

- This is a proof of concept on Bitcoin testnet; it has not been audited and must not run a binding election.
- Eligibility relies on the registration authority issuing one code per person. An operator with access to network logs can correlate request timing.
- Tally cost grows with n² for one ring; large elections need one ring per polling station, which shrinks each anonymity set.
- No coercion resistance: a voter can prove how they voted.
- Transaction timing and amounts may allow linkability; batching or mixing would reduce it.
- Bitcoin fees and confirmation times do not scale to national elections.
- The ballot box supports 2 to 128 registered keys per election. Historical sample elections have no signed ballots in the current format.

## Citation

To cite the software, use [`CITATION.cff`](CITATION.cff). The original 2023 thesis by
[Nana Tchounkeu, Thierry Christian](https://orcid.org/0009-0009-1526-7131) [<img src="docs/assets/orcid.svg" alt="ORCID iD" width="16" height="16">](https://orcid.org/0009-0009-1526-7131)
has its own citation.

```bibtex
@thesis{nana2023ivote,
  author      = {Nana Tchounkeu, Thierry Christian},
  title       = {Design and Implementation of an Electronic Voting System Based on Blockchain and Cryptographic Techniques},
  type        = {Bachelor's thesis},
  institution = {Institut Universitaire de Technologie, University of Douala},
  year        = {2023}
}
```

## References

1. S. Nakamoto. *Bitcoin: A Peer-to-Peer Electronic Cash System*. 2008.
2. R. L. Rivest, A. Shamir, Y. Tauman. *How to Leak a Secret*. ASIACRYPT 2001, LNCS 2248, pp. 552-565. [doi:10.1007/3-540-45682-1_32](https://doi.org/10.1007/3-540-45682-1_32)
3. J. K. Liu, V. K. Wei, D. S. Wong. *Linkable Spontaneous Anonymous Group Signature for Ad Hoc Groups*. ACISP 2004, LNCS 3108.
4. B. Adida. *Helios: Web-based Open-Audit Voting*. USENIX Security Symposium, 2008.
5. F. Hao, P. Y. A. Ryan, P. Zieliński. *Anonymous Voting by Two-Round Public Discussion*. IET Information Security 4(2), 2010.
6. Z. Zhao, T.-H. H. Chan. *How to Vote Privately Using Bitcoin*. ICICS 2015, LNCS 9543, pp. 82-96. [ePrint 2015/1007](https://eprint.iacr.org/2015/1007)
7. P. McCorry, S. F. Shahandashti, F. Hao. *A Smart Contract for Boardroom Voting with Maximum Voter Privacy*. Financial Cryptography 2017. [doi:10.1007/978-3-319-70972-7_20](https://doi.org/10.1007/978-3-319-70972-7_20)
8. Y. Wu. *An E-voting System based on Blockchain and Ring Signature* (BlockVotes). MSc thesis, University of Birmingham, 2017. [github.com/yfgeek/BlockVotes](https://github.com/yfgeek/BlockVotes)
9. F. Þ. Hjálmarsson, G. K. Hreiðarsson, M. Hamdaqa, G. Hjálmtýsson. *Blockchain-Based E-Voting System*. IEEE CLOUD 2018, pp. 983-986. [doi:10.1109/CLOUD.2018.00151](https://doi.org/10.1109/CLOUD.2018.00151)
10. B. Yu, J. K. Liu, A. Sakzad, S. Nepal, R. Steinfeld, P. Rimba, M. H. Au. *Platform-Independent Secure Blockchain-Based Voting System*. ISC 2018. [doi:10.1007/978-3-319-99136-8_20](https://doi.org/10.1007/978-3-319-99136-8_20)
11. A. Russo, A. Fernández Anta, M. I. González Vasco, S. P. Romano. *Chirotonia: A Scalable and Secure e-Voting Framework based on Blockchains and Linkable Ring Signatures*. IEEE Blockchain 2021, pp. 417-424. [arXiv:2111.02257](https://arxiv.org/abs/2111.02257)
12. J. Huang, D. He, M. S. Obaidat, P. Vijayakumar, M. Luo, K.-K. R. Choo. *The Application of the Blockchain Technology in Voting Systems: A Review*. ACM Computing Surveys 54(3), 2021.
13. M. Benabdallah, A. Audras, L. Coudert, N. El Madhoun, M. Badra. *Analysis of Blockchain Solutions for E-Voting: A Systematic Literature Review*. IEEE Access 10, 2022. [doi:10.1109/ACCESS.2022.3187688](https://doi.org/10.1109/ACCESS.2022.3187688)
14. U. Jafar, M. J. Ab Aziz, Z. Shukur, H. A. Hussain. *A Systematic Literature Review and Meta-Analysis on Scalable Blockchain-Based Electronic Voting Systems*. Sensors 22(19), 2022. [doi:10.3390/s22197585](https://doi.org/10.3390/s22197585)
15. V. Vladucu, Z. Dong, J. Medina, R. Rojas-Cessa. *E-Voting Meets Blockchain: A Survey*. IEEE Access 11, pp. 23293-23308, 2023. [doi:10.1109/ACCESS.2023.3253682](https://doi.org/10.1109/ACCESS.2023.3253682)
16. A. Faz-Hernández, S. Scott, N. Sullivan, R. S. Wahby, C. A. Wood. *Hashing to Elliptic Curves*. RFC 9380, IETF, 2023. [doi:10.17487/RFC9380](https://doi.org/10.17487/RFC9380)

## Acknowledgments

[Dr. Jean de Dieu NKAPKOP](https://orcid.org/0000-0001-7736-5145) [<img src="docs/assets/orcid.svg" alt="ORCID iD" width="16" height="16">](https://orcid.org/0000-0001-7736-5145) for the academic supervision, Mr. DJIMELI Christian and the CODEC team for
the internship and their blockchain expertise. The first prototype reused parts of Yifan Wu's
open-source BlockVotes [8]; IVote also relies on BitcoinECDSA.php and the noble cryptography libraries.

## License

[GPL-3.0-or-later](LICENSE).
