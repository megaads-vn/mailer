<?php
namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Firebase\JWT\JWT;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{

    private $request;

    public function __construct(Request $request)
    {
        $this->request = $request;
    }


    /**
     * @param User $user
     * @return mixed
     */
    protected function jwt(User $user)
    {
        $payload = [
            'iss' => "lumen-jwt", // Issuer of the token
            'sub' => $user->id, // Subject of the token
            'iat' => time(), // Time when JWT was issued.
            'exp' => time() + 60*60 // Expiration time
        ];

        // As you can see we are passing `JWT_SECRET` as the second parameter that will
        // be used to decode the token in the future.
        return JWT::encode($payload, env('JWT_SECRET'));
    }


    public function authenticate(User $user)
    {
        $this->validate($this->request, [
           'email' => 'required|email|max:255',
            'password' => 'required'
        ]);

        $user = User::query()->where('email', $this->request->input('email'))->first();
        if ( !$user ) {

            return response()->json([
                'error' => 'Email does not exist.'
            ], 400);
        }

        if ( Hash::check($this->request->input('password'), $user->password) ) {
            return response()->json([
                'token' => $this->jwt($user)
            ]);
        }

        return response()->json([
            'error' => 'Email or password is wrong.'
        ], 400);
    }
}