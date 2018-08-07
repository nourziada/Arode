<?php
namespace App\Http\Controllers\Api;

use App\User;
use Illuminate\Support\Str;
use Illuminate\Http\Request;

trait apiResponseTrait
{

    /*
     *
     * Response Method
     */

    public function apiResponse($status = true , $data = null , $message = null)
    {
        $array = [
            'status' => $status,
            'data' => $data,
            'message' => $message,
        ];

        return $array;
    }

    /*
     * User Token Auth
     */

    public function userTokenAuth(Request $request)
    {
        $header = $request->header('Authorization');
        if (Str::startsWith($header, 'Bearer ')) {
            $header =  Str::substr($header, 7);
        }else{
            response()->json($this->apiResponse(false,null,'Authorization syntax error'),200)->send();
            die();
        }

        /*
         * Check the User Token
         */
        $user = User::where('token',$header)->get()->first();

        if($user != null){

            if($user->status == 0){

                /*
                 * Blocked User
                 */
                response()->json($this->apiResponse(false,null,'هذا الحساب تم حظره من قبل مسؤول النظام' ),200)->send();
                die();
            }else{

                return $user;

            }

        }else{

            response()->json($this->apiResponse(false,null,'يجب ان تقوم بتسجيل الدخول لكي تتمكن من تصفح التطبيق'),200)->send();
            die();
        }
    }
    /*
     *
     * Header Auth
     *
     */

    public function ApplicationHeaderAuth(Request $request)
    {
        $header = $request->header('Authorization');
        if (Str::startsWith($header, 'Bearer ')) {
            $header =  Str::substr($header, 7);
        }else{
            response()->json($this->apiResponse(false,null,'Authorization syntax error'),200)->send();
            die();
        }

        if($header == '8s2UfFMktJeBksq266LMqnPjmS+KeROfIHHCg/c1CPA='){


        }else{
            response()->json($this->apiResponse(false,null,'Authorization code is in-correct'),200)->send();
            die();
        }
    }
}
