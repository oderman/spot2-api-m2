<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Models\Zone;

class ZoneController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
        $zones = Zone::all();
        
    }


    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show(Request $request)
    {
        //
        $zone = Zone::findOrFail($request->id);
        
        return $zone;
    }

    /**
     * Return messages with false status.
     *
     * @param  int  $code
     * @param  String  $message
     * @return \Illuminate\Http\Response
     */
    function errors(Int $code, String $message){

        return response()->json([
            'status' => false,
            'message' => $message,
        ], $code);

    }

    /**
     * Return messages with true status and results of operations to max, min and avg.
     *
     * @param  int  $code
     * @param  String  $message
     * @return \Illuminate\Http\Response
     */
    function aggregateResult(String $type, Float $priceUnit, Float $priceUnitConstruction, Int $itemsQuantity){

        return response()->json([
            'status' => true,
            'payload' => [
                "type" => $type,
                "price_unit" => $priceUnit,
                "price_unit_construction" => $priceUnitConstruction,
                "elements" => $itemsQuantity
            ]
        ], 200);

    }


    
    public function showOperator(Request $request)
    {

        if(!isset($_GET['construction_type']) or !is_numeric($_GET['construction_type'])){

            return $this->errors(400, 'construction_type paramater is required and must to be of type numeric.');
        }

        switch($_GET['construction_type']){

            case 1: $constructionType = 'Áreas verdes'; break;

            case 2: $constructionType = 'Centro de barrio'; break; 

            case 3: $constructionType = 'Equipamiento'; break; 

            case 4: $constructionType = 'Habitacional'; break; 

            case 5: $constructionType = 'Habitacional y comercial'; break; 

            case 6: $constructionType = 'Industrial'; break; 

            case 7: $constructionType = 'Sin Zonificación'; break;

            default:
                return $this->errors(400, 'construction_type must to be one number between 1 and 7');
            break;
        }

        $zone = Zone::
        where("codigo_postal","=", $request->zip_code)
        ->where("uso_construccion","=", $constructionType)
        ->get();

        $itemsQuantity = count($zone->all());

        if($itemsQuantity === 0){
            return $this->errors(200, 'It Was found 0 items.');
        }

        $result = json_decode($zone, true);
        

        if($request->operator === 'max'){
            return $this->max($result);
        }

        if($request->operator === 'min'){
            return $this->min($result);
        }

        if($request->operator === 'avg'){
            return $this->avg($result);
        }
        
    }


    public function max(array $zones = [])
    {
        $itemsQuantity = count($zones);

        foreach($zones as $key => $value)
        {

            $subtract = ($value["valor_suelo"] - $value["subsidio"]);
            
            if($subtract > 0){
                $priceUnit = $value["superficie_terreno"]  / $subtract ;
                $priceUnitConstruction = $value["superficie_construccion"]  / $subtract;
            }else{
                continue;
            }
            

            if($key == 0){

                $maxPriceUnit = $priceUnit;
                $maxPriceUnitConstruction = $priceUnitConstruction;
                
            }else{

                if($priceUnit > $maxPriceUnit ){
                    $maxPriceUnit = $priceUnit;
                }
    
                if($priceUnitConstruction > $maxPriceUnitConstruction ){
                    $maxPriceUnitConstruction = $priceUnitConstruction;
                }

            }


        }

        return $this->aggregateResult('max', $maxPriceUnit, $maxPriceUnitConstruction, $itemsQuantity);

    }

    public function min(array $zones = [])
    {
        
        $itemsQuantity = count($zones);

        foreach($zones as $key => $value)
        {

            $subtract = ($value["valor_suelo"] - $value["subsidio"]);
            
            if($subtract > 0){
                $priceUnit = $value["superficie_terreno"]  / $subtract ;
                $priceUnitConstruction = $value["superficie_construccion"]  / $subtract;
            }else{
                continue;
            }

            if($key == 0){

                $minPriceUnit = $priceUnit;
                $minPriceUnitConstruction = $priceUnitConstruction;
                
            }else{

                if($priceUnit < $minPriceUnit ){
                    $minPriceUnit = $priceUnit;
                }
    
                if($priceUnitConstruction < $minPriceUnitConstruction ){
                    $minPriceUnitConstruction = $priceUnitConstruction;
                }

            }


        }

        return $this->aggregateResult('min', $minPriceUnit, $minPriceUnitConstruction, $itemsQuantity);

    }
    

    public function avg(array $zones = [])
    {
        $itemsQuantity = count($zones);
        $avgPriceUnit = 0;
        $avgPriceUnitConstruction = 0;

        foreach($zones as $key => $value)
        {
            $subtract = ($value["valor_suelo"] - $value["subsidio"]);
            
            if($subtract > 0){
                $avgPriceUnit += $value["superficie_terreno"]  / $subtract ;
                $avgPriceUnitConstruction += $value["superficie_construccion"]  / $subtract ;
            }else{
                continue;
            }

        }

        $avgPriceUnit = ($avgPriceUnit/$itemsQuantity);
        $avgPriceUnitConstruction = ($avgPriceUnitConstruction/$itemsQuantity);


        return $this->aggregateResult('avg', $avgPriceUnit, $avgPriceUnitConstruction, $itemsQuantity);
    }



}
