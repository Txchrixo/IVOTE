import { secp256k1 } from '@noble/curves/secp256k1';
import { ballotMessage, registrationDigest } from './ballot.mjs';
import { signatureHash, verify } from './lsag.js';

let input = '';
for await (const chunk of process.stdin) input += chunk;

try {
    const { publicKey, proof, election, code, reference, candidate, ring, signature } = JSON.parse(input);
    if (publicKey) {
        if (secp256k1.verify(proof, registrationDigest(election, code), publicKey)) {
            process.stdout.write(secp256k1.ProjectivePoint.fromHex(publicKey).toHex(true));
        }
    } else if (verify(ballotMessage(reference, candidate), ring, signature)) {
        process.stdout.write(signatureHash(signature));
    }
} catch {
    // Malformed keys and signatures are rejected by the caller.
}
