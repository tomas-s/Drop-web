<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Requests;
use App\Sensor;
use Carbon\Carbon;
use DB;
use App\User;

class UserSensorController extends Controller {
    /*
     * Returns all sensors for selected user
     * 
     * $userEmail email of selected user
     */

    public static function index($user_id) {
        /*$user = User::where("id", $user_id)->first();
        if (isset($user)) {
            return Sensor::where("user", $user->email)->get();
        } else {
            return array();
        }*/
        return Sensor::where('user_id',$user_id)->get();

    }

    /*
     * Returns selected sensor for selected user
     *
     * $userEmail email of selected user
     * $sensorID ID of selected sensor
     */

    public static function show($user_id, $sensor_id) {
        $result = [];
        foreach (UserSensorController::index($user_id) as $sensor) {
            if ($sensor->sensorid == $sensor_id) {
                array_push($result, $sensor);
            }
        }
        return $result->toJson();
        //return response()->json($result);
    }


/*
 * http://localhost/drops/public/api/generateSN
 * {
 *    "email": "t.slizik@centrum.sk"
 * }
 */
    public function generateSN(Request $request){
        $time = Carbon::now()->toDateTimeString();
        $email = $request->input("email");
        $user = User::all()->where("email",$email)->first();
        if($user!=null){
            $hash = bcrypt($email.$time);
            str_replace("\\","P",$hash);
            $sensor = new Sensor();
            $sensor->user_id = $user->id;
            $sensor->sensor_id =$hash;
            $sensor->save();
            return response($sensor, 200);
        }
        else
            return response()->json(['result' => 'zly email']);


    }



}
