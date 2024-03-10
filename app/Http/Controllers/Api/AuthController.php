<?php

namespace App\Http\Controllers\Api;

use App\Enum\Role;
use App\Helpers\RequestHelper;
use App\Http\Requests\LoginRequest;
use App\Http\Requests\SignupRequest;
use App\Models\User;
use http\Env\Response;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Crypt;

class AuthController extends Controller
{
    public function signup(SignupRequest $request)
    {
        $data = $request->validated();
        /** @var \App\Models\User $user */
        $user = User::create([
            'name' => $data['name'],
            'email' => $data['email'],
            'password' => bcrypt($data['password']),
        ]);

        $token = $user->createToken('main')->plainTextToken;
        return response(compact('user', 'token'));
    }

    public function login(LoginRequest $request)
    {
        $credentials = $request->validated();
        if (!Auth::attempt($credentials)) {
            return response([
                'message' => 'Provided email or password is incorrect'
            ], 422);
        }

        /** @var \App\Models\User $user */
        $user = Auth::user();
        $token = $user->createToken('main')->plainTextToken;
        return response(compact('user', 'token'));
    }

    public function logout(Request $request)
    {
        /** @var \App\Models\User $user */
        $user = $request->user();
        $user->currentAccessToken()->delete();
        return response('', 204);
    }

    public function getUser(Request $request){
        $user = $request->user();
        return response()->json([
           'id' => $user->id,
           'email' => $user->email,
           'name' => $user->name,
           'role' => Role::getRoleByNumber($user->role_id)->name,
           'placeholder_api' => $this->preparePlaceholderApi($user, 'open_ai_key'),
           'placeholder_pinecone_api' => $this->preparePlaceholderApi($user, 'pinecone_api_key'),
           'placeholder_pinecone_url' => $this->preparePlaceholderApi($user, 'pinecone_api_url')
        ]);
    }

    public function updatePassword(Request $request){
        $user = $request->user();
        $password = $request->get('password');
        $passwordRepeat = $request->get('passwordRepeat');
        if(!empty($passwordRepeat) && !empty($password)){
            if($password == $passwordRepeat){
                $user->password = bcrypt($password);
                $user->save();
                return \response()->json(RequestHelper::generateSuccessResponse('Password was successfully changed'));

            }
            return \response()->json(RequestHelper::generateErrorResponse('Passwords do not match'));
        }

        return \response()->json(RequestHelper::generateErrorResponse('Both passwords were not given'));
    }

    public function updateApiKey(Request $request){
        $user = $request->user();
        $apiKey = $request->get('apiKey');
        $pineconeKey = $request->get('pineconeKey');
        $pineconeUrl = $request->get('pineconeUrl');
        $status = 0;

        if(!empty($apiKey)){
            $user->open_ai_key = Crypt::encryptString($apiKey) ?? null;
            $status = 1;
        }

        if(!empty($pineconeKey)){
            $user->pinecone_api_key = Crypt::encryptString($pineconeKey) ?? null;
            $status = 1;
        }

        if(!empty($pineconeUrl)){
            $user->pinecone_api_url = Crypt::encryptString($pineconeUrl) ?? null;
            $status = 1;
        }

        $user->save();

        if($status){
            return \response()->json(RequestHelper::generateSuccessResponse('Api key was successfully changed'));
        }

        return \response()->json(RequestHelper::generateErrorResponse('No api key provided'));
    }

    private function preparePlaceholderApi(User $user, $key){
        try{
            return $this->stringToPlaceholder(Crypt::decryptString($user->$key));
        }catch (\Exception $e){
            return '';
        }

    }

    private function stringToPlaceholder(string $input): string
    {
        $inputLength = strlen($input);

        if ($inputLength <= 7) {
            return $input;
        }

        $firstThreeLetters = substr($input, 0, 3);
        $lastFourLetters = substr($input, -4);

        return $firstThreeLetters . '...' . $lastFourLetters;
    }
}
