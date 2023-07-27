import { sha256 } from '@noble/hashes/sha256';
import { utf8ToBytes } from '@noble/hashes/utils';

export function ballotMessage(reference, candidateId) {
    return `IVOTE/BALLOT/v1/${reference}/${candidateId}`;
}

export function registrationDigest(election, code) {
    return sha256(utf8ToBytes('IVOTE/REGISTRATION/v1/' + election + '/' + code.toUpperCase()));
}
