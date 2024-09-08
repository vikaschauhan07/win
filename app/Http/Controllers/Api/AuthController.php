<?php

namespace App\Http\Controllers\Api;

use App\Helpers\KlaviyoHelper;
use App\Traits\ApiResponses;
use App\Http\Controllers\Controller;
use App\Http\Requests\ForgotPasswordRequest;
use App\Http\Requests\ResetPasswordRequest;
use App\Http\Requests\UserLoginRequest;
use App\Http\Requests\UserRegisterRequest;
use App\Mail\ForgotPasswordMail;
use App\Mail\SignupPromotionMail;
use App\Models\ResetToekns;
use App\Models\User;
use Exception;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;

class AuthController extends Controller
{
    use ApiResponses;

    public function login(UserLoginRequest $request)
    { 
        try{
            $credentials = $request->only('email', 'password');

            if (!Auth::attempt($credentials)) {
                User::newLogin($request->email, $request->getClientIp(), 'fail');
                return $this->error('Invalid credentials', 401);
            }
    
            $user = $request->user();
            $token = $user->createToken('auth_token')->plainTextToken;
            $response = [
                'access_token' => $token,
                'token_type' => 'Bearer',
            ];
            User::newLogin($request->email, $request->getClientIp(), 'success');

            return $this->success($response, 200);
        } catch(Exception $ex){
            return $this->error("Server error", 500);
        }
    }

    public function register(UserRegisterRequest $request){
        try{
            // if (!$this->isValidCaptcha($request->input('captcha'))) {
            //     return response()->json(['errors' => ['valid_captcha' => 'false']], 400);
            // }
            $userId =  User::guidv4();        
            $referral_code = substr(str_shuffle(str_repeat($x = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ', ceil(10 / strlen($x)))), 1, 10);

            $user = new User();

            $user->userid = $userId;
            $user->email = $request->email;
            $user->password = $request->password;
            $user->firstname = $request->firstname;
            $user->lastname = $request->lastname;
            $user->referred_by =$request->referral_code;
            $user->referral_code = $referral_code;
            $user->remember_me = true;

            $user->save();

            // if($referral_code){
            //     User::createInvite($userid, $data['email'], $referred_by);
            // }
            $confiramtionToekn = User::generateConfirmationToken();
            User::createConfirmationToken($user->userid,$confiramtionToekn);
            // Mail::to($user->email)->send(new SignupPromotionMail($user, $confiramtionToekn));

            // KlaviyoHelper::sendSignupEvent([
            //     "email" => $user->eamil,
            //     "firstname" => $user->firstname,
            //     "lastname" => $user->lasttname
            // ]);
            
            return $this->success([], 200);
        } catch(Exception $ex){
            return $this->error("Server error",[], 500);
        }
    }

    public function forgotPassword(ForgotPasswordRequest $request)
    {
        try{
            // if (!app('captcha')->check($request->input('captcha'))) {
            //     return response()->json(['error' => 'invalid_captcha'], 400);
            // }
            $user = User::where('email', $request->input('email'))->first();

            if($user){
                $code = random_int(100000, 999999);
                User::setResetCodeUsed($code,$user->userid);
                Mail::to($user->email)->send(new ForgotPasswordMail($code));
                $this->sucess(['id' => md5($user->userid)],200);
            }
            $this->error("User not found",[],404);
        } catch(Exception $ex){
            return $this->error("Server error",[], 500);
        }
        
    }
    public function resetPassword(ResetPasswordRequest $request,$id){
        try{
            
            $userId = User::whereRaw('md5(id) = ?', [$id])->value('id');
            $user = User::where("userid",$userId)->first();
            if ($user) {
                $resetCode = ResetToekns::where('userid', $userId)
                    ->where('code', $request->input('code'))
                    ->where('used', 0)
                    ->first();

                if ($resetCode) {
                    $user->password = $request->password;
                    $user->save();
                    $resetCode->used =  1;
                    $resetCode->save();
                    return $this->success([],200);
                } else {
                    return $this->error("Invalid reset Toekn",[],404);

                }
            } else {
                return $this->error("invalid user",[],404);
            }
        }catch(Exception $ex){
            return $this->error("Server error",[], 500);
        }
    }
    
}
