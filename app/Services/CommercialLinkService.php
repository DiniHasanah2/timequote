<?php
// app/Services/CommercialLinkService.php
namespace App\Services;

use App\Models\PublicViewToken;
use App\Support\JwtLink;
use Illuminate\Support\Str;

class CommercialLinkService {
  public function createPinned(int $quoteId, int $versionId, int $ttlSec = 86400): array {
    $jwt = JwtLink::makeHs256(['quoteId'=>$quoteId, 'versionId'=>$versionId], $ttlSec);
    $rec = PublicViewToken::create([
      'quote_id'=>$quoteId, 'version_id'=>$versionId, 'is_latest'=>false,
      'token'=>$jwt, 'expires_at'=>now()->addSeconds($ttlSec), 'created_by'=>auth()->id()
    ]);
    return ['token'=>$jwt, 'url'=>$this->portalUrl($jwt), 'id'=>$rec->id];
  }

  public function createLatest(int $quoteId, int $ttlSec = 86400): array {
    $jwt = JwtLink::makeHs256(['quoteId'=>$quoteId, 'latest'=>true], $ttlSec);
    $rec = PublicViewToken::create([
      'quote_id'=>$quoteId, 'version_id'=>null, 'is_latest'=>true,
      'token'=>$jwt, 'expires_at'=>now()->addSeconds($ttlSec), 'created_by'=>auth()->id()
    ]);
    return ['token'=>$jwt, 'url'=>$this->portalUrl($jwt), 'id'=>$rec->id];
  }

  public function revoke(int $id): void {
    $t = PublicViewToken::findOrFail($id);
    $t->update(['revoked_at'=>now()]);
  }

  private function portalUrl(string $jwt): string {
    // Second System URL — letak env PORTAL_PUBLIC_BASE
    $base = rtrim(env('PORTAL_PUBLIC_BASE', 'http://192.168.77.4:8001'), '/');
    return $base.'/v/'.$jwt;
  }
}
