<?php

namespace App\Http\Controllers\Api\Admin\Auth;

use App\Http\Controllers\Controller;
use App\Http\Resources\UserCollection;
use App\Helper\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;
use App\Models\Admin\User;

/**
 * Class AuthController
 *
 * @package App\Http\Controllers\Api\Admin\Auth
 */
class AuthController extends Controller
{
    /**
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function login(Request $request)
    {
        $credentials = $request->only('email', 'password');
        $admin = Auth::guard('admin')->attempt($credentials);
        if($admin){
            $admin = User::where('email',$request->email)->firstOrFail();
            $token = $admin->createToken('token-name',[$request->token_name])->plainTextToken;
            return response()->json(new JsonResponse(['token'=>$token] ), Response::HTTP_OK);
        }
        return response()->json(new JsonResponse([], 'Login failed'), Response::HTTP_UNAUTHORIZED);
    }

    /**
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function logout(Request $request)
    {
        Auth::guard('admin')->logout();
        return response()->json((new JsonResponse())->success([]), Response::HTTP_OK);
    }
    /**
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function info(Request $request)
    {
        return new UserCollection($request->user());
    }
}
