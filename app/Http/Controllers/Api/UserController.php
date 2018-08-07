<?php

namespace App\Http\Controllers\Api;

use App\User;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Auth;

class UserController extends Controller
{
    use apiResponseTrait;

    public function updateUserEmail(Request $request)
    {
        $user = $this->userTokenAuth($request);

        /*
         * Validation Request Parameters
         */
        $validator_required = Validator::make($request->all(),
            [
                'email' => 'required|email|max:255|unique:users',
            ]);

        if ($validator_required->fails())
        {
            response()->json($this->apiResponse(false,null,$validator_required->errors()->first() ),200)->send();
            die();
        }

        /*
         * Update User Image
         */
        $user->email = $request->email;
        $user->save();

        $array = [
            'id' => $user->id,
            'name' => $user->name,
            'email' => $user->email,
            'mobile' => $user->mobile,
            'image' => url('/') . '/uploads/' . $user->image,
            'socail_media_token' => $user->socail_media_token,
            'rating' => $user->rating,
            'wallet' => $user->wallet,
            'is_auth' => $user->is_auth,
            'user_token' => $user->token,
        ];

        response()->json($this->apiResponse(true,$array,'تم تحديث البريد الالكتروني بنجاح'),200)->send();
    }
    public function updateUserPassword(Request $request)
    {
        $user = $this->userTokenAuth($request);

        /*
         * Validation Request Parameters
         */
        $validator_required = Validator::make($request->all(),
            [
                'old_password' => 'required',
                'password' => 'required|min:6|confirmed',
            ]);

        if ($validator_required->fails())
        {
            response()->json($this->apiResponse(false,null,$validator_required->errors()->first() ),200)->send();
            die();
        }

        /*
         * Change the user Passowrd
         */

        if($user->password != null)
        {
            /*
             * Regular User
             */

            if(Hash::check($request->old_password, $user->password)){
                $user->password =  Hash::make($request->password);
                $user->save();
            }else {
                response()->json($this->apiResponse(false,null,'كلمة المرور القديمة لا تتطابق مع سجلاتنا'),200)->send();
                die();
            }

        }else
        {
            /*
             * Socail Media User
             */

            $user->password = bcrypt($request->password);
            $user->save();

        }

        $array = [
            'id' => $user->id,
            'name' => $user->name,
            'email' => $user->email,
            'mobile' => $user->mobile,
            'image' => url('/') . '/uploads/' . $user->image,
            'socail_media_token' => $user->socail_media_token,
            'rating' => $user->rating,
            'wallet' => $user->wallet,
            'is_auth' => $user->is_auth,
            'user_token' => $user->token,
        ];

        response()->json($this->apiResponse(true,$array,'تم تحديث كلمة المرور بنجاح'),200)->send();
    }
    public function updateUserImage(Request $request)
    {
        $user = $this->userTokenAuth($request);

        /*
         * Validation Request Parameters
         */
        $validator_required = Validator::make($request->all(),
            [
                'image' => 'required',
            ]);

        if ($validator_required->fails())
        {
            response()->json($this->apiResponse(false,null,$validator_required->errors()->first() ),200)->send();
            die();
        }

        /*
         * Update User Image
         */

        if($request->has('image') && $request->image != null){
            $featured = $request->image;
            $featured_new_name = 'image_'.time().'.png';
            $featured = base64_decode(preg_replace('#^data:image/\w+;base64,#i', '', $featured));
            file_put_contents(public_path('uploads/').$featured_new_name, $featured);

            $user->image =  $featured_new_name;
        }
        $user->save();

        $array = [
            'id' => $user->id,
            'name' => $user->name,
            'email' => $user->email,
            'mobile' => $user->mobile,
            'image' => url('/') . '/uploads/' . $user->image,
            'socail_media_token' => $user->socail_media_token,
            'rating' => $user->rating,
            'wallet' => $user->wallet,
            'is_auth' => $user->is_auth,
            'user_token' => $user->token,
        ];

        response()->json($this->apiResponse(true,$array,'تم رفع الصورة بنجاح'),200)->send();
    }
    public function getMyProfile(Request $request)
    {
        $user = $this->userTokenAuth($request);

        $array = [
            'id' => $user->id,
            'name' => $user->name,
            'email' => $user->email,
            'mobile' => $user->mobile,
            'image' => url('/') . '/uploads/' . $user->image,
            'socail_media_token' => $user->socail_media_token,
            'rating' => $user->rating,
            'wallet' => $user->wallet,
            'is_auth' => $user->is_auth,
            'user_token' => $user->token,
        ];

        response()->json($this->apiResponse(true,$array,'تم جلب بيانات البروفايل بنجاح'),200)->send();
    }
    public function registerWithSocailMedia(Request $request)
    {
        $this->ApplicationHeaderAuth($request);
        /*
         * Validation Request Parameters
         */
        $validator_required = Validator::make($request->all(),
            [
                'name' => 'required|max:255',
                'social_media_token' => 'required|max:255',
                'email' => 'required|email|max:255',

            ]);

        if ($validator_required->fails())
        {
            response()->json($this->apiResponse(false,null,$validator_required->errors()->first() ),200)->send();
            die();
        }

        /*
         * Check the User Data if Exist
         */
        $user = User::where('email', $request->email)
                    ->where('social_media_token',$request->social_media_token)
                    ->where('password','null')
                    ->get()
                    ->first();

        if($user != null)
        {
            /*
             * User is Exsits and get To Login
             */
            $user->token = User::generateUserToken();
            $user->save();
        }
        else
        {
            /*
             * New User and Register it
             */

            if(User::where('email',$request->email)->get()->first() == null)
            {
                $user = new User;
                $user->name = $request->name;
                $user->email = $request->email;
                $user->social_media_token = $request->social_media_token;
                $user->token = User::generateUserToken();
                $user->save();
            }else
            {
                response()->json($this->apiResponse(false,null, 'هذا الايميل مسجل مسبقاً ولديه كلمة مرور' ),200)->send();
                die();
            }

        }

        /*
         * Send the Data
         */
        $user = User::find($user->id);
        $array = [
            'id' => $user->id,
            'name' => $user->name,
            'email' => $user->email,
            'mobile' => $user->mobile,
            'image' => url('/') . '/uploads/' . $user->image,
            'socail_media_token' => $user->socail_media_token,
            'rating' => $user->rating,
            'wallet' => $user->wallet,
            'is_auth' => $user->is_auth,
            'user_token' => $user->token,
        ];

        response()->json($this->apiResponse(true,$array,'تم تسجيل الاشتراك بنجاح'),200)->send();
    }
    public function forgotPassword(Request $request)
    {
        $this->ApplicationHeaderAuth($request);

        /*
         * Validation Request Parameters
         */
        $validator_required = Validator::make($request->all(),
            [
                'email' => 'required',
            ]);

        if ($validator_required->fails())
        {
            response()->json($this->apiResponse(false,null,$validator_required->errors()->first() ),200)->send();
            die();
        }

        /*
        * Check The User is Exsits
        */
        $user = User::where('email', $request->email)->get()->first();
        if($user != null)
        {
            /*
             * Send Forgot Password Email
             */

            app('App\Http\Controllers\Auth\ForgotPasswordController')->sendResetLinkEmail($request);
        }
        else
        {
            /*
             * No Founded User
             */

            response()->json($this->apiResponse(false,null,'البريد الالكتروني غير مسجل مسبقاً' ),200)->send();
            die();
        }
    }
    public function login(Request $request)
    {
        $this->ApplicationHeaderAuth($request);

        /*
         * Validation Request Parameters
         */
        $validator_required = Validator::make($request->all(),
            [
                'email' => 'required',
                'password' => 'required',
            ]);

        if ($validator_required->fails())
        {
            response()->json($this->apiResponse(false,null,$validator_required->errors()->first() ),200)->send();
            die();
        }

        /*
         * Check if the Data of User is Correct
         */

        if(Auth::attempt(['email' => $request->email, 'password' => $request->password]))
        {
            /*
             * Check the Status of User is Available Or Blocked
             */
            $user = User::where('email', $request->email)->get()->first();
            if($user->status != 0)
            {
                /*
                 * Available User
                 */
                $array = [
                    'id' => $user->id,
                    'name' => $user->name,
                    'email' => $user->email,
                    'mobile' => $user->mobile,
                    'image' => url('/') . '/uploads/' . $user->image,
                    'socail_media_token' => $user->socail_media_token,
                    'rating' => $user->rating,
                    'wallet' => $user->wallet,
                    'is_auth' => $user->is_auth,
                    'user_token' => $user->token,
                ];

                response()->json($this->apiResponse(true,$array,'تم تسجيل الدخول بنجاح'),200)->send();
            }
            else{
                /*
                 * Blocked User
                 */
                response()->json($this->apiResponse(false,null,'هذا الحساب تم حظره من قبل مسؤول النظام' ),200)->send();
                die();
            }
        }else
        {
            response()->json($this->apiResponse(false,null,'البريد الالكتروني او كلمة المرور غير صحيحة' ),200)->send();
            die();
        }
    }
    public function register(Request $request)
    {
        $this->ApplicationHeaderAuth($request);

        /*
         * Validation Request Parameters
         */
        $validator_required = Validator::make($request->all(),
            [
                'name' => 'required|max:255',
                'mobile' => 'required|max:255',
                'email' => 'required|email|max:255|unique:users',
                'password' => 'required|min:6|confirmed',
            ]);

        if ($validator_required->fails())
        {
            response()->json($this->apiResponse(false,null,$validator_required->errors()->first() ),200)->send();
            die();
        }

        /*
         * Register User
         */

        $user = new User;
        $user->name = $request->name;
        $user->mobile = $request->mobile;
        $user->email = $request->email;
        $user->password = bcrypt($request->password);
        $user->token = User::generateUserToken();
        $user->save();

        $user = User::find($user->id);
        $array = [
            'id' => $user->id,
            'name' => $user->name,
            'email' => $user->email,
            'mobile' => $user->mobile,
            'image' => url('/') . '/uploads/' . $user->image,
            'socail_media_token' => $user->socail_media_token,
            'rating' => $user->rating,
            'wallet' => $user->wallet,
            'is_auth' => $user->is_auth,
            'user_token' => $user->token,
        ];

        response()->json($this->apiResponse(true,$array,'تم تسجيل الاشتراك بنجاح'),200)->send();

    }
}
