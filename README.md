<p align="center">
  <img src="resources/img/ivote-red.png" alt="IVote" width="200">
</p>

<h3 align="center">Design and Implementation of an Electronic Voting System<br>Based on Blockchain and Cryptographic Techniques</h3>

<p align="center">
  Bachelor's thesis · IUT de Douala, University of Douala · 2023<br>
  <sub>Proof of concept: Bitcoin testnet only, not meant for binding elections.</sub>
</p>

<p align="center">
  <img src="https://img.shields.io/badge/status-proof%20of%20concept-orange" alt="Proof of concept">
  <img src="https://img.shields.io/badge/PHP-8.1%2B-777bb4" alt="PHP 8.1+">
  <img src="https://img.shields.io/badge/Laravel-10-ff2d20" alt="Laravel 10">
  <img src="https://img.shields.io/badge/Bitcoin-testnet-f7931a" alt="Bitcoin testnet">
  <img src="https://img.shields.io/badge/anonymity-ring%20signatures-7c5aed" alt="Ring signatures">
  <img src="https://img.shields.io/badge/license-GPL--3.0-blue" alt="GPL-3.0">
  <img src="https://img.shields.io/badge/DOI-pending-lightgrey" alt="DOI pending">
</p>

<p align="center">
  <a href="#abstract">Abstract</a> ·
  <a href="#protocol">Protocol</a> ·
  <a href="#election-walkthrough">Walkthrough</a> ·
  <a href="#design">Design</a> ·
  <a href="#related-work">Related work</a> ·
  <a href="#getting-started">Getting started</a> ·
  <a href="#citation">Citation</a>
</p>

---

| | |
|---|---|
| **Author** | NANA TCHOUNKEU Thierry Christian |
| **Academic supervisor** | Dr. JDD NKAPKOP |
| **Professional supervisor** | Mr. DJIMELI Christian (CODEC) |
| **Degree** | Licence de Technologie, Software Engineering |
| **Institution** | Institut Universitaire de Technologie (IUT), University of Douala |
| **Host company** | CODEC group, Douala |
| **Year** | 2023 |
| **Thesis** | Zenodo, DOI to be added on publication |
| **Defense slides** | [PDF, 41 slides](docs/slides/ivote-defense-2023.pdf) |

## Abstract

Electronic voting speeds up tabulation and widens access, but only earns trust if ballots stay
secret and anyone can check the result. IVote combines the **Bitcoin blockchain** with
**ring signatures** [2]: each vote is a testnet transaction whose `OP_RETURN` carries a ring
signature over the chosen candidate. The signature proves the voter belongs to the registered
electorate without revealing who they are, and the public ledger lets every voter confirm their
ballot was counted unchanged, while organisers can neither alter results nor see voters'
identities.

The system was modelled with UML following the 2TUP process and built with PHP, Laravel
and MySQL. It extends the BlockVotes design [8] with remote identity verification (Veriff),
two-factor authentication for the authorities and an auditable election life cycle.

<p align="center">
  <img src="docs/slides/02-idea.jpg" alt="E-voting + Blockchain + Ring signature = IVote" width="720">
</p>

## Protocol

Three actors take part: the **Election Authority** (EA), the **Registration Authority** (RA)
and the **voter** (V<sub>i</sub>). Authorities are assumed not to collude.

| Notation | Meaning |
|---|---|
| SK<sub>i</sub>, PK<sub>i</sub> | voter key pair, generated in the browser; SK<sub>i</sub> never leaves it |
| PK = {PK<sub>1</sub> … PK<sub>n</sub>} | ring of registered public keys for an election |
| A<sub>i</sub> | one-time Bitcoin testnet address funded by the EA |
| C<sub>j</sub> | candidate identifier |
| σ = Sign<sub>ring</sub>(C<sub>j</sub>, SK<sub>i</sub>, PK) | ring signature over the ballot |

1. **Setup.** The EA creates the election and its Bitcoin address; the RA registers candidates.
2. **Identification.** V<sub>i</sub> proves their identity with Veriff and receives a one-time link by email.
3. **Key registration.** V<sub>i</sub> generates (SK<sub>i</sub>, PK<sub>i</sub>) and registers PK<sub>i</sub> only.
4. **Funding.** The EA funds a fresh address A<sub>i</sub> per voter, tagged with `OP_RETURN hash(PK_i)`.
5. **Vote.** V<sub>i</sub> signs C<sub>j</sub> with SK<sub>i</sub> and the ring PK, then spends A<sub>i</sub> to the EA address with `OP_RETURN (hash(σ), C_j)`.
6. **Tally.** Anyone decodes the `OP_RETURN` outputs, verifies σ against PK and counts the first transaction per address.
7. **Verification.** V<sub>i</sub> finds their own transaction (individual verifiability); anyone can recount (universal verifiability).

| Property | How the PoC addresses it | Status |
|---|---|---|
| Eligibility | Veriff identity check, one registered key per voter | implemented |
| Ballot privacy | ring signature hides the signer among PK | 2023 prototype |
| Integrity | ballots are Bitcoin transactions, immutable once confirmed | 2023 prototype |
| Individual verifiability | voter looks up their transaction and signature | 2023 prototype |
| Universal verifiability | public tally from on-chain data | 2023 prototype |
| Authority accountability | TOTP 2FA, role gates, audit log, staged life cycle | implemented |
| Coercion resistance | not addressed | out of scope |

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

### Identity verification with Veriff

<table>
  <tr>
    <td width="33%"><img src="docs/screenshots/10-voter-veriff.jpg" alt="Identity check page"></td>
    <td width="33%"><img src="docs/screenshots/2023/veriff-start.jpg" alt="Veriff session start"></td>
    <td width="33%"><img src="docs/screenshots/2023/veriff-handoff.jpg" alt="Continue on a phone"></td>
  </tr>
  <tr>
    <td align="center"><b>2.</b> Identity check page</td>
    <td align="center"><b>2.</b> Starting a Veriff session</td>
    <td align="center"><b>2.</b> Continue on a phone</td>
  </tr>
  <tr>
    <td><img src="docs/screenshots/2023/veriff-sms-sent.jpg" alt="Secure link sent"></td>
    <td><img src="docs/screenshots/2023/veriff-mobile-document.jpg" alt="Document selection"></td>
    <td><img src="docs/screenshots/2023/veriff-mobile-capture.jpg" alt="Document photo and selfie"></td>
  </tr>
  <tr>
    <td align="center"><b>2.</b> Secure link sent by SMS</td>
    <td align="center"><b>2.</b> Choosing the ID document</td>
    <td align="center"><b>2.</b> Document photo, selfie, done</td>
  </tr>
  <tr>
    <td><img src="docs/screenshots/11-voter-veriff-submitted.jpg" alt="Verification submitted"></td>
    <td><img src="docs/screenshots/2023/voter-email.jpg" alt="Approval email"></td>
    <td><img src="docs/screenshots/13-voter-ready.jpg" alt="Ready to vote"></td>
  </tr>
  <tr>
    <td align="center"><b>2.</b> Check submitted</td>
    <td align="center"><b>2.</b> Approval email with the voter link</td>
    <td align="center"><b>3.</b> Voter link opened</td>
  </tr>
</table>

### Keys, vote, tally and verification

<table>
  <tr>
    <td width="50%"><img src="docs/screenshots/14-voter-generate-keys.jpg" alt="Key generation"></td>
    <td width="50%"><img src="docs/screenshots/15-voter-wait.jpg" alt="Waiting for the vote"></td>
  </tr>
  <tr>
    <td align="center"><b>3.</b> Key pair generated in the browser</td>
    <td align="center"><b>3.</b> Waiting for the voting day</td>
  </tr>
  <tr>
    <td><img src="docs/screenshots/2023/wait-and-vote.jpg" alt="Ballot and voter identity"></td>
    <td><img src="docs/screenshots/2023/sign-verify-results.jpg" alt="Signing, verification and results"></td>
  </tr>
  <tr>
    <td align="center"><b>5.</b> Ballot and voter identity (2023 prototype)</td>
    <td align="center"><b>5–7.</b> Ring signature, verification, results (2023 prototype)</td>
  </tr>
</table>

The last two captures come from the 2023 prototype (French UI); these steps are being ported to the current Laravel version.

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

The thesis version runs on WampServer (Apache and MySQL).

| Layer | Choice |
|---|---|
| Back end | PHP 8.1, Laravel 10, MySQL 8 on WampServer (Apache) |
| Front end | Blade, Bootstrap 5.3, jQuery 3.7, Vite 4, Chartist |
| Blockchain | Bitcoin testnet (`OP_RETURN`), BitcoinECDSA, Block.io, CryptoAPIs, Ethereum and Solana balances |
| Cryptography | ring signatures, secp256k1 keys, RSA voter keys generated with WebCrypto, authority keys encrypted at rest (AES-256) |
| Identity and access | Veriff, TOTP 2FA, role gates, CSP with nonces, rate limits, audit log |
| Method | 2TUP, UML (Visual Paradigm), Figma mock-ups, automated feature tests |

<p align="center"><img src="docs/diagrams/tools.jpg" alt="Tools used" width="640"></p>

## Related work

IVote sits in the line of work that keeps ballots anonymous with ring signatures while using a
public blockchain as the bulletin board.

| Work | Year | Ledger | Anonymity | Tally | Identity |
|---|---|---|---|---|---|
| Zhao & Chan [6] | 2015 | Bitcoin | zero-knowledge proofs | on-chain deposits | not covered |
| Open Vote Network [7] | 2017 | Ethereum | self-tallying, ZK proofs | smart contract | registered voters |
| BlockVotes [8] | 2017 | Bitcoin | ring signatures | public, off-chain | ballot codes |
| Hjálmarsson et al. [9] | 2018 | permissioned chain | trusted authority | smart contract | trusted authority |
| Yu et al. [10] | 2018 | Hyperledger Fabric | linkable ring signatures | homomorphic | registered voters |
| Chirotonia [11] | 2021 | Ethereum | linkable ring signatures | smart contract | registered voters |
| **IVote** | 2023 | Bitcoin testnet | ring signatures | public, off-chain | Veriff KYC + email link |

Surveys [12–15] cover the wider landscape and its open problems (scalability, coercion, usability).

## Getting started

Requirements: PHP 8.1+ with `gmp`, Composer, Node 18+, MySQL 8.

```bash
git clone https://github.com/Txchrixo/iVote.git && cd iVote
composer install && npm install
cp .env.example .env && php artisan key:generate
php artisan migrate --seed    # creates the EA and RA accounts, prints their passwords
npm run build && php artisan serve
```

Set `BLOCKIO_*`, `CRYPTOAPIS_API_KEY` and `VERIFF_*` in `.env` to reach the external services.

**Demo data.** To explore a full election season (five elections, candidates, ballot codes and
testnet addresses) on an empty database:

```bash
php artisan migrate:fresh --seed --seeder=DemoSeeder
```

Accounts `ea` and `ra` share a password and TOTP secret, both printed once
(or set through `DEMO_PASSWORD` and `DEMO_2FA_SECRET`). The seeder refuses to run in production.

## Tests

```bash
php artisan test
```

Feature tests cover authorization, two-factor sign-in, election stages, candidates, blockchain
balances, security headers, the audit log and the public results pages.

## Repository layout

```
app/          controllers, models, election stages, security middleware
database/     migrations and seeders (including DemoSeeder)
resources/    Blade views, styles and page scripts
routes/       web routes (back office, voter flow, results)
tests/        feature and unit tests
docs/         screenshots, UML diagrams, defense slides
```

## Limitations and future work

- This is a proof of concept on Bitcoin testnet; it has not been audited and must not run a binding election.
- One voter could still receive several funded addresses; the address pool needs a binding to the registered public key.
- Plain ring signatures do not link two ballots from the same key; linkable ring signatures [3] would enforce one vote per voter cryptographically.
- No coercion resistance: a voter can prove how they voted.
- Transaction timing and amounts may allow linkability; batching or mixing would reduce it.
- Bitcoin fees and confirmation times do not scale to national elections.
- Voting, tally and verification run in the 2023 prototype and are being ported to the current Laravel version.

## Citation

If you build on this work, please cite the thesis. GitHub also offers a citation from
[`CITATION.cff`](CITATION.cff).

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
2. R. L. Rivest, A. Shamir, Y. Tauman. *How to Leak a Secret*. ASIACRYPT 2001, LNCS 2248, pp. 552–565. [doi:10.1007/3-540-45682-1_32](https://doi.org/10.1007/3-540-45682-1_32)
3. J. K. Liu, V. K. Wei, D. S. Wong. *Linkable Spontaneous Anonymous Group Signature for Ad Hoc Groups*. ACISP 2004, LNCS 3108.
4. B. Adida. *Helios: Web-based Open-Audit Voting*. USENIX Security Symposium, 2008.
5. F. Hao, P. Y. A. Ryan, P. Zieliński. *Anonymous Voting by Two-Round Public Discussion*. IET Information Security 4(2), 2010.
6. Z. Zhao, T.-H. H. Chan. *How to Vote Privately Using Bitcoin*. ICICS 2015, LNCS 9543, pp. 82–96. [ePrint 2015/1007](https://eprint.iacr.org/2015/1007)
7. P. McCorry, S. F. Shahandashti, F. Hao. *A Smart Contract for Boardroom Voting with Maximum Voter Privacy*. Financial Cryptography 2017. [doi:10.1007/978-3-319-70972-7_20](https://doi.org/10.1007/978-3-319-70972-7_20)
8. Y. Wu. *An E-voting System based on Blockchain and Ring Signature* (BlockVotes). MSc thesis, University of Birmingham, 2017. [github.com/yfgeek/BlockVotes](https://github.com/yfgeek/BlockVotes)
9. F. Þ. Hjálmarsson, G. K. Hreiðarsson, M. Hamdaqa, G. Hjálmtýsson. *Blockchain-Based E-Voting System*. IEEE CLOUD 2018, pp. 983–986. [doi:10.1109/CLOUD.2018.00151](https://doi.org/10.1109/CLOUD.2018.00151)
10. B. Yu, J. K. Liu, A. Sakzad, S. Nepal, R. Steinfeld, P. Rimba, M. H. Au. *Platform-Independent Secure Blockchain-Based Voting System*. ISC 2018. [doi:10.1007/978-3-319-99136-8_20](https://doi.org/10.1007/978-3-319-99136-8_20)
11. A. Russo, A. Fernández Anta, M. I. González Vasco, S. P. Romano. *Chirotonia: A Scalable and Secure e-Voting Framework based on Blockchains and Linkable Ring Signatures*. IEEE Blockchain 2021, pp. 417–424. [arXiv:2111.02257](https://arxiv.org/abs/2111.02257)
12. J. Huang, D. He, M. S. Obaidat, P. Vijayakumar, M. Luo, K.-K. R. Choo. *The Application of the Blockchain Technology in Voting Systems: A Review*. ACM Computing Surveys 54(3), 2021.
13. M. Benabdallah, A. Audras, L. Coudert, N. El Madhoun, M. Badra. *Analysis of Blockchain Solutions for E-Voting: A Systematic Literature Review*. IEEE Access 10, 2022. [doi:10.1109/ACCESS.2022.3187688](https://doi.org/10.1109/ACCESS.2022.3187688)
14. U. Jafar, M. J. Ab Aziz, Z. Shukur, H. A. Hussain. *A Systematic Literature Review and Meta-Analysis on Scalable Blockchain-Based Electronic Voting Systems*. Sensors 22(19), 2022. [doi:10.3390/s22197585](https://doi.org/10.3390/s22197585)
15. V. Vladucu, Z. Dong, J. Medina, R. Rojas-Cessa. *E-Voting Meets Blockchain: A Survey*. IEEE Access 11, pp. 23293–23308, 2023. [doi:10.1109/ACCESS.2023.3253682](https://doi.org/10.1109/ACCESS.2023.3253682)

## Acknowledgments

Dr. JDD NKAPKOP for the academic supervision, Mr. DJIMELI Christian and the CODEC team for
the internship and their blockchain expertise. IVote started from Yifan Wu's BlockVotes [8]
and relies on the BitcoinECDSA.php library.

## License

[GPL-3.0](LICENSE), inherited from BlockVotes [8].
