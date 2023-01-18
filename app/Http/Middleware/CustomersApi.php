<?php

namespace App\Http\Middleware;


use App\Models\User;
use Illuminate\Support\Facades\Auth;

use Closure;
use Illuminate\Http\Request;

class CustomersApi
{

  public function handle(Request $request, Closure $next)
  {
    
      $apiToken = request("api_token");
      if($apiToken){
      $user = User::where('api_token', $apiToken)->first();
      if ($user != null){
        return $next($request);
      }
    }
    return response()->json([
          'status' => False,
          'code' => 401,
          'message' => 'Unauthorized',
          'data' => null,
      ], 401);
  }
}
?>