<?php
namespace App\Http\Controllers;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;
use App\User;
use Intervention\Image\ImageManagerStatic as Image;
use File;

class AuthController extends Controller
{
    /**
     * Create user
     *
     * @param  [string] name
     * @param  [string] email
     * @param  [string] password
     * @param  [string] password_confirmation
     * @return [string] message
     */
    public function signup(Request $request)
    {
        $request->validate([
            'name' => 'required|string',
            'email' => 'required|string|email|unique:users',
            'password' => 'required|string'
        ]);
        $user = new User([
            'name' => $request->name,
            'email' => $request->email,
            'password' => bcrypt($request->password),
            'photo_profile' => "avatar.png"
        ]);
        $user->save();
        return response()->json([
            'message' => 'Successfully created user!'
        ], 201);
    }
  
    /**
     * Login user and create token
     *
     * @param  [string] email
     * @param  [string] password
     * @param  [boolean] remember_me
     * @return [string] access_token
     * @return [string] token_type
     * @return [string] expires_at
     */
    public function login(Request $request)
    {
        $request->validate([
            'email' => 'required|string|email',
            'password' => 'required|string'
        ]);
        $credentials = request(['email', 'password']);
        if(!Auth::attempt($credentials))
            return response()->json([
                'message' => 'Unauthorized'
            ], 401);
        $user = $request->user();
        $tokenResult = $user->createToken('Personal Access Token');
        $token = $tokenResult->token;
        $token->save();
        return response()->json([
            'access_token' => $tokenResult->accessToken,
            'token_type' => 'Bearer',

        ]);
    }
  
    /**
     * Logout user (Revoke the token)
     *
     * @return [string] message
     */
    public function logout(Request $request)
    {
        $request->user()->token()->revoke();
        return response()->json([
            'message' => 'Successfully logged out'
        ]);
    }
  
    /**
     * Get the authenticated User
     *
     * @return [json] user object
     */
    public function user(Request $request)
    {
        // return response()->json($request->user());
        return response()->json([
            'idUser' => $request->user()->id,
            'name' => $request->user()->name,
            'email' => $request->user()->email,
            'fcm_token' => $request->user()->fcm_token,
            'weight' => $request->user()->weight,
            'height' => $request->user()->height,
            'photo_profile' => "https://thenic-api.herokuapp.com/images/".$request->user()->photo_profile,

        ]);
    }

    public function updateUser(Request $request)
    {
        // $url = 'http://192.168.43.74:8000/images/';
        $this->validate($request, [
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
        ]);

        $idUser= $request->user()->id;
        // return $idUser;
        $user = User::findOrFail($idUser);
        // return $request->name;
        $user->name = $request->input('name');
        $user->weight = $request->input('weight');
        $user->height = $request->input('height');
        if ($request->hasFile('image')) {
            $oldFilename = substr($user->photo_profile,26);
            File::delete($oldFilename);
            $image = $request->file('image');
            $namePath = str_slug($request->input('name')).time().'.'.$image->getClientOriginalExtension();
            $name = str_slug($request->input('name')).time().'.'.$image->getClientOriginalExtension();
            $path = base_path('/public/images/' . $namePath);
            // dd($path);
            // This will generate an image with transparent background
            // If you need to have a background you can pass a third parameter (e.g: '#000000')
            $canvas = Image::canvas(500, 500);
            $imageUser  = Image::make($image->getRealPath())->resize(500, 500, function($constraint)
            {
                $constraint->aspectRatio();
            });
            $canvas->insert($imageUser, 'center');
            $canvas->save($path);

            $user->photo_profile = $name;
        }
        $user->save();

        return response()->json([
            'succes' => 'Sukses mengupdate data user'
        ]);
    }

    public function pushFCMToken(Request $request)
    {
        $idUser= $request->user()->id;
        $user = User::findOrFail($idUser);
        $user->fcm_token = $request->input('fcm_token');
        $user->save();

        return response()->json([
            'success' => 'Sukses mengupdate fcm token'
        ]);
    }
}