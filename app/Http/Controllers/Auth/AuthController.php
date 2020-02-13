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

    public function ssoCallback(Request $request) {
        $redirect = '/';
        $token = $request->get('token');
        $authUrl = 'https://id.megaads.vn/sso/auth?token=' . $token;
        $getUserResult = NULL;
        // $getUserResult = $this->sendRequest($authUrl);
        exec("curl $authUrl", $getUserResult);
        if ($getUserResult) {
            $getUserResult = $getUserResult[0];
            $data = json_decode($getUserResult);
            if ($data->status == 'success') { 
                $configAccessible = config('auth.accessible');
                $userEmail = $data->user->email;
                if (in_array($userEmail, $configAccessible)) {
                    if ($request->session()->has('redirect_url')) {
                        $redirect = $request->session()->get('redirect_url');
                    }
                    $request->session()->put('user', $data->user);
                } else {
                    return response('Unauthorized. You don\'t have permission to access this page', 401);
                }
            }   
        }
        return redirect()->to($redirect);
    }

    public function sendRequest ($url) {
        $retval = false;
        $curl = curl_init();

        curl_setopt_array($curl, array(
          CURLOPT_URL => $url,
          CURLOPT_RETURNTRANSFER => true,
          CURLOPT_ENCODING => "",
          CURLOPT_MAXREDIRS => 10,
          CURLOPT_TIMEOUT => 30,
          CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
          CURLOPT_CUSTOMREQUEST => "GET",
          CURLOPT_HTTPHEADER => array(
            "Accept: */*",
            "Cache-Control: no-cache",
            "Connection: keep-alive",
            "accept-encoding: gzip, deflate",
            "cache-control: no-cache",
          ),
        ));

        $response = curl_exec($curl);
        $err = curl_error($curl);

        curl_close($curl);

        if ($err) {

        } else {
            $retval = $response;
        }

        return $retval;
    }
}