<?php

namespace App\Http\Controllers;

use App\Models\ApiResponse;

class ClassificationController extends Controller {

    /**
     * Classify movement type for one observation
     *
     * @return \Illuminate\Http\JsonResponse
     *
     * @SWG\Post(
     *     summary="Classify movement type for one observation",
     *     path="/observations/classify",
     *     description="Classify movement type for one observation",
     *     produces={"application/json"},
     *     tags={"classification", "observation", "movement"},
     *     @SWG\Parameter(
     *        name="speed",
     *        description="Current speed",
     *        required=true,
     *        type="string",
     *        in="query"
     *     ),
     *     @SWG\Parameter(
     *       name="alt",
     *       description="Altitude",
     *       required=true,
     *       type="string",
     *       in="query"
     *     ),
     *     @SWG\Parameter(
     *       name="date",
     *       description="Date",
     *       required=true,
     *       type="string",
     *       in="query"
     *     ),
     *     @SWG\Response(
     *         response=200,
     *         description="Success",
     *         @SWG\Schema(ref="#/definitions/apiResponse")
     *     ),
     *     @SWG\Response(
     *         response=400,
     *         description="Malformed data",
     *     )
     * )
     */
    public function classify() {
        $date = \Carbon\Carbon::parse(\Request::get('date'));
        $speed = \Request::get('speed');
        $alt = \Request::get('alt');
        $day = "Sun";
        $work = "no";
        
        switch ($date->dayOfWeek) {
            case \Carbon\Carbon::MONDAY:
                $day = "Mon";
                $work = "yes";
                break;
            case \Carbon\Carbon::TUESDAY:
                $day = "Tue";
                $work = "yes";
                break;
            case \Carbon\Carbon::WEDNESDAY:
                $day = "Wed";
                $work = "yes";
                break;
            case \Carbon\Carbon::THURSDAY:
                $day = "Thu";
                $work = "yes";
                break;
            case \Carbon\Carbon::FRIDAY:
                $day = "Fri";
                $work = "yes";
                break;
            case \Carbon\Carbon::SATURDAY:
                $day = "Sat";
                $work = "no";
                break;            
        }
        $res = [];
        $val = -1;
        
        $temp = tempnam('.', 'mc_');
        $name = str_replace(".tmp", ".arff", $temp);
        rename($temp, $name);
        $handle = fopen($name, 'w');
        
        fwrite($handle, "@relation observation_classification\n\n"
                . "@attribute CurrentSpeed numeric\n"
                . "@attribute Altitude numeric\n"
                . "@attribute DayOfWeek {Mon,Tue,Wed,Thu,Fri,Sat,Sun}\n"
                . "@attribute IsWorkingDay {yes,no}\n"
                . "@attribute MoveType {Walking,Running,Biking,Driving,Metro,Bus,Motionless}\n\n"
                . "@data\n"
                . $speed . "," . $alt . "," . $day . "," . $work . ",?\n");
        $cmnd = "java -cp ./weka.jar weka.classifiers.trees.J48 -T " . $name . " -l movement_type_tree.j48.model -p 0 2>&1";
        exec($cmnd, $res, $val);
        foreach ($res as $line) {
            $accept = false;
            $toks = preg_split("/\s+/", $line);
            foreach ($toks as $tokid => $tok) {
                if ($tokid === 1 && $tok === "1") {
                    $accept = true;
                } else if ($accept && $tokid === 3) {
                    $resp = explode(':', $tok)[1];
                    break;
                }
            }
            if ($accept) {
                break;
            }
        }
        fclose($handle);
        unlink($name);
        $response = new ApiResponse();
        $response->status = 'success';
        $response->message = $resp;
        return \Response::json($response);
    }

}
