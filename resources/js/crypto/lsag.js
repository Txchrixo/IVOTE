/**
 * Linkable ring signatures (LSAG, Liu, Wei and Wong, ACISP 2004) on secp256k1.
 *
 * The key image I = x * Hp(P) repeats when the same key signs again. The ballot box
 * uses it to reject duplicates. Each challenge commits to the ordered ring and the message.
 *
 * Wire format: (c0, s_0 .. s_{n-1}, I), 32 * (n + 1) + 33 bytes.
 */
import { secp256k1, hashToCurve } from '@noble/curves/secp256k1';
import { sha256 } from '@noble/hashes/sha256';
import { bytesToHex, concatBytes, hexToBytes, utf8ToBytes } from '@noble/hashes/utils';

const Point = secp256k1.ProjectivePoint;
const N = secp256k1.CURVE.n;
const G = Point.BASE;

const TAG_CHALLENGE = utf8ToBytes('IVOTE/LSAG/v1/challenge');
const TAG_RING = utf8ToBytes('IVOTE/LSAG/v1/ring');
const DST_HP = 'IVOTE/LSAG/v1/hash-to-point';

const mod = (a) => ((a % N) + N) % N;
const toBigInt = (bytes) => BigInt('0x' + (bytesToHex(bytes) || '0'));
const scalarBytes = (k) => hexToBytes(k.toString(16).padStart(64, '0'));
const isScalar = (k) => typeof k === 'bigint' && k > 0n && k < N;

function randomScalar() {
    return toBigInt(secp256k1.utils.randomPrivateKey());
}

function asBytes(message) {
    return typeof message === 'string' ? utf8ToBytes(message) : message;
}

/**
 * Hp uses RFC 9380 hash-to-curve. Cache each point because the tally reuses the ring.
 */
const hpCache = new Map();
function hashToPoint(P) {
    const key = P.toHex(true);
    let H = hpCache.get(key);
    if (!H) {
        const { x, y } = hashToCurve(P.toRawBytes(true), { DST: DST_HP }).toAffine();
        H = Point.fromAffine({ x, y });
        hpCache.set(key, H);
    }
    return H;
}

function ringDigest(points) {
    return sha256(concatBytes(TAG_RING, ...points.map((P) => P.toRawBytes(true))));
}

function challenge(digest, keyImage, message, L, R) {
    const h = sha256(concatBytes(TAG_CHALLENGE, digest, keyImage.toRawBytes(true), message, L.toRawBytes(true), R.toRawBytes(true)));
    return mod(toBigInt(h));
}

function parseRing(ring) {
    if (!Array.isArray(ring) || ring.length < 2) {
        throw new Error('A ring needs at least two public keys');
    }
    const points = ring.map((hex) => Point.fromHex(hex)); // rejects points off the curve
    if (new Set(points.map((P) => P.toHex(true))).size !== points.length) {
        throw new Error('The ring contains the same public key twice');
    }
    return points;
}

/** New voter key pair: secret scalar and compressed public key, both hex. */
export function generateKeyPair() {
    const secret = secp256k1.utils.randomPrivateKey();
    return { secretKey: bytesToHex(secret), publicKey: bytesToHex(secp256k1.getPublicKey(secret, true)) };
}

export function publicKeyOf(secretKeyHex) {
    return bytesToHex(secp256k1.getPublicKey(hexToBytes(secretKeyHex), true));
}

export function keyImageOf(secretKeyHex) {
    const x = toBigInt(hexToBytes(secretKeyHex));
    return hashToPoint(G.multiply(x)).multiply(x).toHex(true);
}

/**
 * Keep the published ring order. Keys are compressed points encoded as hex.
 */
export function sign(message, ring, secretKeyHex) {
    const points = parseRing(ring);
    const x = toBigInt(hexToBytes(secretKeyHex));
    if (!isScalar(x)) throw new Error('Invalid secret key');

    const own = G.multiply(x);
    const pi = points.findIndex((P) => P.equals(own));
    if (pi < 0) throw new Error('The signer is not in the ring');

    const n = points.length;
    const m = asBytes(message);
    const digest = ringDigest(points);
    const hp = points.map(hashToPoint);
    const I = hp[pi].multiply(x);

    const c = new Array(n);
    const s = new Array(n);
    const alpha = randomScalar();
    c[(pi + 1) % n] = challenge(digest, I, m, G.multiply(alpha), hp[pi].multiply(alpha));

    for (let k = 1; k < n; k++) {
        const i = (pi + k) % n;
        s[i] = randomScalar(); // published as is, so no secret flows through these products
        const L = G.multiplyUnsafe(s[i]).add(points[i].multiplyUnsafe(c[i]));
        const R = hp[i].multiplyUnsafe(s[i]).add(I.multiplyUnsafe(c[i]));
        c[(i + 1) % n] = challenge(digest, I, m, L, R);
    }
    s[pi] = mod(alpha - c[pi] * x);

    return {
        c0: bytesToHex(scalarBytes(c[0])),
        s: s.map((k) => bytesToHex(scalarBytes(k))),
        keyImage: I.toHex(true),
    };
}

/** True when `signature` is a valid signature of `message` by some member of `ring`. */
export function verify(message, ring, signature) {
    try {
        const points = parseRing(ring);
        if (!signature || !Array.isArray(signature.s) || signature.s.length !== points.length) return false;

        const c0 = toBigInt(hexToBytes(signature.c0));
        const s = signature.s.map((h) => toBigInt(hexToBytes(h)));
        if (!isScalar(c0) || !s.every(isScalar)) return false;

        const I = Point.fromHex(signature.keyImage); // on the curve; secp256k1 has cofactor 1
        if (I.equals(Point.ZERO)) return false;

        const m = asBytes(message);
        const digest = ringDigest(points);
        let c = c0;
        for (let i = 0; i < points.length; i++) {
            const L = G.multiplyUnsafe(s[i]).add(points[i].multiplyUnsafe(c));
            const R = hashToPoint(points[i]).multiplyUnsafe(s[i]).add(I.multiplyUnsafe(c));
            c = challenge(digest, I, m, L, R);
            if (c === 0n) return false;
        }
        return c === c0;
    } catch (e) {
        return false;
    }
}

/** True when both signatures were made with the same secret key. */
export function linked(a, b) {
    return Point.fromHex(a.keyImage).equals(Point.fromHex(b.keyImage));
}

/** SHA-256 of the canonical signature encoding, the value committed in OP_RETURN. */
export function signatureHash(signature) {
    return bytesToHex(sha256(concatBytes(hexToBytes(signature.c0), ...signature.s.map(hexToBytes), hexToBytes(signature.keyImage))));
}
