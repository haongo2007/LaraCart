<?php

namespace App\Http\Controllers\Api\Front\Auth;

use App\Http\Controllers\Controller;
use App\Models\Front\ShopCustomer;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use App\Http\Controllers\Api\Front\Auth\AuthTrait;
use Illuminate\Http\Response;
use App\Helper\JsonResponse;

class AuthController extends Controller
{
    use AuthTrait;
    /*
    |--------------------------------------------------------------------------
    | Auth Controller
    |--------------------------------------------------------------------------
    |
    | This controller handles authenticating users for the application and
    | redirecting them to your home screen. The controller uses a trait
    | to conveniently provide its functionality to your applications.
    |
     */
    public function login(Request $request)
    {
        try {
            $request->validate([
                'email' => 'email|required',
                'password' => 'required'
            ]);

            $credentials = request(['email', 'password']);

            if (!Auth::attempt($credentials)) {
                $data['message'] = trans('auth.failed');
                return response()->json(new JsonResponse($data), Response::HTTP_UNAUTHORIZED);
            }
            
            $store_id = request()->header('x-store');
            $user = ShopCustomer::where([['email', $request->email],['store_id',$store_id]])->first();

            if (!Hash::check($request->password, $user->password, [])) {
                throw new \Exception('Error in Login');
            }
            $user->tokens()->delete();
            $tokenResult = $user->createToken('authToken')->plainTextToken;

            $data['access_token'] = $tokenResult;
            $data['token_type'] = 'Bearer';
            $data['message'] = trans('auth.login_success');
            return response()->json(new JsonResponse($data), Response::HTTP_OK);
        } catch (\Exception $error) {
            return response()->json(new JsonResponse([],$error), Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * Get a validator for an incoming registration request.
     *
     * @param  array  $data
     * @return \Illuminate\Contracts\Validation\Validator
     */
    protected function validator(array $data)
    {
        $dataMapping = $this->mappingValidator($data);
        if(bc_captcha_method() && in_array('register', bc_captcha_page())) {
            $data['captcha_field'] = $data[bc_captcha_method()->getField()] ?? '';
            $dataMapping['validate']['captcha_field'] = ['required', 'string', new \BlackCart\Core\Rules\CaptchaRule];
        }
        return Validator::make($data, $dataMapping['validate'], $dataMapping['messages']);
    }

    /**
     * Create a new user instance after a valid registration.
     *
     * @param  array  $data
     * @return \BlackCart\Core\Front\Models\ShopCustomer
     */
    protected function createNewCustomer(array $data)
    {
        $data['country'] = strtoupper($data['country'] ?? '');
        $dataMap = $this->mappDataInsert($data);

        $user = ShopCustomer::createCustomer($dataMap);
        if ($user) {
            if (bc_config('welcome_customer')) {

                $checkContent = (new ShopEmailTemplate)->where('group', 'welcome_customer')->where('status', 1)->first();
                if ($checkContent) {
                    $content = $checkContent->text;
                    $dataFind = [
                        '/\{\{\$title\}\}/',
                        '/\{\{\$first_name\}\}/',
                        '/\{\{\$last_name\}\}/',
                        '/\{\{\$email\}\}/',
                        '/\{\{\$phone\}\}/',
                        '/\{\{\$password\}\}/',
                        '/\{\{\$address1\}\}/',
                        '/\{\{\$address2\}\}/',
                        '/\{\{\$address3\}\}/',
                        '/\{\{\$country\}\}/',
                    ];
                    $dataReplace = [
                        trans('email.welcome_customer.title'),
                        $dataMap['first_name'] ?? '',
                        $dataMap['last_name'] ?? '',
                        $dataMap['email'] ?? '',
                        $dataMap['phone'] ?? '',
                        $dataMap['password'] ?? '',
                        $dataMap['address1'] ?? '',
                        $dataMap['address2'] ?? '',
                        $dataMap['address3'] ?? '',
                        $dataMap['country'] ?? '',
                    ];
                    $content = preg_replace($dataFind, $dataReplace, $content);
                    $dataView = [
                        'content' => $content,
                    ];

                    $config = [
                        'to' => $data['email'],
                        'subject' => trans('email.welcome_customer.title'),
                    ];

                    bc_send_mail($this->templatePath . '.mail.welcome_customer', $dataView, $config, []);
                }

            }
        } else {

        }
        return $user;
    }
    


    /**
     * Handle a registration request for the application.
     * User for Front
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Http\JsonResponse
     */
    public function register(Request $request)
    {
        $validator = $this->validator($request->all());
        if ($validator->fails()) {
            return redirect(bc_route('home'))
                        ->withErrors($validator)
                        ->with(['register_state' => true]);
        }
        $data = $request->all();
        $user = $this->createNewCustomer($data);
        $dataMap = $this->mappDataInsert($data);
        if ($user) {
            if (bc_config('welcome_customer')) {

                $checkContent = (new ShopEmailTemplate)->where('group', 'welcome_customer')->where('status', 1)->first();
                if ($checkContent) {
                    $content = $checkContent->text;
                    $dataFind = [
                        '/\{\{\$title\}\}/',
                        '/\{\{\$first_name\}\}/',
                        '/\{\{\$last_name\}\}/',
                        '/\{\{\$email\}\}/',
                        '/\{\{\$phone\}\}/',
                        '/\{\{\$password\}\}/',
                        '/\{\{\$address1\}\}/',
                        '/\{\{\$address2\}\}/',
                        '/\{\{\$address3\}\}/',
                        '/\{\{\$country\}\}/',
                    ];
                    $dataReplace = [
                        trans('email.welcome_customer.title'),
                        $dataMap['first_name'] ?? '',
                        $dataMap['last_name'] ?? '',
                        $dataMap['email'] ?? '',
                        $dataMap['phone'] ?? '',
                        $dataMap['password'] ?? '',
                        $dataMap['address1'] ?? '',
                        $dataMap['address2'] ?? '',
                        $dataMap['address3'] ?? '',
                        $dataMap['country'] ?? '',
                    ];
                    $content = preg_replace($dataFind, $dataReplace, $content);
                    $dataView = [
                        'content' => $content,
                    ];

                    $config = [
                        'to' => $data['email'],
                        'subject' => trans('email.welcome_customer.title'),
                    ];

                    bc_send_mail($this->templatePath . '.mail.welcome_customer', $dataView, $config, []);
                }

            }

            //Send email verify
            $user->sendEmailVerify();

            //Login
            $this->guard()->login($user);
            
            if ($response = $this->registered($request, $user)) {
                return $response;
            }

        } else {
            return back()->withInput();
        }
        return $request->wantsJson()
                    ? new JsonResponse([], 201)
                    : redirect($this->redirectPath());
    }

    /**
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function logout(Request $request)
    {
        auth()->user()->tokens()->delete();

        return response()->json(new JsonResponse(['message' => trans('auth.logout_success')]), Response::HTTP_OK);
    }
}
