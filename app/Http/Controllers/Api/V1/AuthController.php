<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

use App\Models\User;
use App\Http\Resources\UserResource;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class AuthController extends Controller
{
    protected $token_name;

    /**
     * User - Get user account detail
     *
     * @return void
     */
    public function __construct()
    {
        $this->token_name = env('APP_NAME') . '_auth_token';
        $this->middleware('auth:api')->only('user');
    }


    /**
     * Register - Register user
     *
     * @return void
     */
    public function register(Request $request)
    {
        // make validator
        $validator = Validator::make($request->all(), [
            'name' => 'required|max:55',
            'email' => 'email|required|unique:users',
            'pin' => 'required|confirmed',
            'password' => 'required|confirmed',
        ]);

        // validate fails
        if ($validator->fails()) return $this->response(
            $request->all(),
            'Validation Fails',
            false,
            'auth.register.validation',
            $validator->errors(),
            422
        );

        //
        $validatedData = array_merge(
            $request->only('name', 'email', 'pin', 'password'),
            [
                'pin' => Hash::make($request->pin),
                'password' => Hash::make($request->password)
            ]
        );

        // 
        $user = (object) [];
        DB::transaction(function () use ($validatedData, &$user) {
            $user = User::create($validatedData);
        });

        //
        if (count($user) < 1) return $this->response(
            $request->all(),
            'Register Fails',
            false,
            'auth.register.fail',
            [],
            400
        );

        // create token
        $accessToken = $user->createToken($this->token_name)->accessToken;

        // 
        return $this->response(
            (new UserResource($user)), 
            'Register success',
            true,
            null, null,
            201,
            [
                'credentials' => [
                    'token' => $accessToken,
                    'type' => 'Bearer'
                ]
            ]
        );
    }

    /**
     * Login - Login user
     *
     * @return void
     */
    public function login(Request $request)
    {
        // make validator
        $validator = Validator::make($request->all(), [
            'email' => 'email|required',
            'password' => 'required'
        ]);

        // validate fails
        if ($validator->fails()) return $this->response(
            $request->all(),
            'Validation Fails',
            false,
            'auth.login.validation',
            $validator->errors(),
            422
        );

        // 
        if (!Auth::attempt($request->only('email', 'password'))) {
            return $this->response(
                [],
                'This User does not exist',
                false,
                'auth.login.fail', [],
                400
            );
        }

        $accessToken = Auth::user()->createToken($this->token_name)->accessToken;

        // 
        return $this->response(
            (new UserResource(Auth::user())), 
            'Login success',
            true,
            null, null,
            200,
            [
                'credentials' => [
                    'token' => $accessToken,
                    'type' => 'Bearer'
                ]
            ]
        );
    }

    /**
     * User - Get user account detail
     *
     * @return void
     */
    public function user()
    {
        // return Auth::user()->wallet()->getBalance();
        $user = Auth::user();
        $wallet = $user->wallet();
        $additional = [
            'balance' => $wallet->getBalance()
        ];
        $data = array_merge( (new Collection($user))->toArray(), $additional);
        return $this->response(
            $data, 
            'Get data success',
            true,
            null, null,
            200
        );
    }
}
