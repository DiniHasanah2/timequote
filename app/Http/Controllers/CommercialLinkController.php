<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\CommercialLinkService;

class CommercialLinkController extends Controller
{

  public function createPinned(Request $r, CommercialLinkService $svc) {
    $data = $r->validate([
      'quoteId'=>'required|integer',
      'versionId'=>'required|integer',
      'ttl'=>'nullable|integer' 
    ]);
    $out = $svc->createPinned($data['quoteId'], $data['versionId'], $data['ttl'] ?? 86400);
    return response()->json(['ok'=>true] + $out);
  }

  // buat link "always latest"
  public function createLatest(Request $r, CommercialLinkService $svc) {
    $data = $r->validate([
      'quoteId'=>'required|integer',
      'ttl'=>'nullable|integer'
    ]);
    $out = $svc->createLatest($data['quoteId'], $data['ttl'] ?? 86400);
    return response()->json(['ok'=>true] + $out);
  }

  public function revoke($id, CommercialLinkService $svc) {
    $svc->revoke((int)$id);
    return response()->json(['ok'=>true]);
  }
}
