<?php

namespace App\Http\Controllers;

//use App\Http\Requests;
use Illuminate\Http\Request;
use App\Http\Controllers\SensorController;
use DB;
use App\Sensor;
use Auth;
use App\Http\Controllers\MyDropsController;

class DataController extends Controller {
/*
 * TODO: dorobit user_id, aby sa dotiahlo z aktualneho uzivatela
 */
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct() {
        //$this->middleware('auth');
    }

    /**
     * Post data
     *
     * @return void
     */
    public function postData(Request $request) {


        if(!(DataController::validateData($request))){
            return "Invalid data";
        }
        


        
        $sensorId = $request->input('DeviceID');
        $sensorState = $request->input('State');
        $sensorBattery = $request->input('BatteryLife');
        $results = DB::select('select * from sensors where sensor_id = ?', array($sensorId));

        if (count($results) == 0) {
            SensorController::createSensor($request);
            DB::table('sensor_history')->insert(
                ['sensor_id' => $sensorId, 'state' => $sensorState, 'battery' => $sensorBattery]
            );
            return response()->json(['result' => 'Device was created']);
        } else {
            SensorController::updateSensor($request);
            DB::table('sensor_history')->insert(
                ['sensor_id' => $sensorId, 'state' => $sensorState, 'battery' => $sensorBattery]
            );
            return response()->json(['result' => 'Device was updated']);
        }
    }
    
    public static function validateData(Request $request) {
        if ($request->input('BatteryLife') < 0 || $request->input('BatteryLife') > 100){
            return false;
        }
        if ($request->input('State') < 0 || $request->input('State') > 2){
            return false;
        }
        return true;
    }


    
}
