<?php
namespace App\Support;

class JwtLink
{
    private static function b64url(string $raw): string {
        return rtrim(strtr(base64_encode($raw), '+/', '-_'), '=');
    }

    public static function makeHs256(array $claims, ?int $ttlSeconds = 86400): string {
        $secret = env('PORTAL_LINK_JWT_SECRET');
        $aud    = env('PORTAL_LINK_JWT_AUD', 'CommercialPortal');
        if (!$secret) throw new \RuntimeException('PORTAL_LINK_JWT_SECRET missing');

        $now = time();
        $payload = array_merge($claims, ['aud'=>$aud, 'iat'=>$now]);
        if ($ttlSeconds) $payload['exp'] = $now + $ttlSeconds;

        $header  = self::b64url(json_encode(['alg'=>'HS256','typ'=>'JWT']));
        $body    = self::b64url(json_encode($payload));
        $sig     = self::b64url(hash_hmac('sha256', "$header.$body", $secret, true));
        return "$header.$body.$sig";
    }
}
