<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Models\Zone;

class ZoneController extends Controller
{
    
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
     * @param  int  $type
     * @param  Float  $priceUnit
     * @param  Float  $priceUnitConstruction
     * @param  Int  $itemsQuantity
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


    /**
     * Receives a request, evaluates the parameters and returns a method
     *
     * @param  Request $request
     * @return Method
     */
    public function showOperator(Request $request)
    {

        $constructionTypeCode = $request->construction_type;

        if(!isset($constructionTypeCode) or !is_numeric($constructionTypeCode)){

            return $this->errors(400, 'construction_type paramater is required and must to be of type numeric.');
        }

        switch($constructionTypeCode){

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

        if(!isset($request->zip_code) or !is_numeric($request->zip_code)){

            return $this->errors(400, 'zip_code paramater is required and must to be of type numeric.');
        }

        $zone = Zone::
        where("codigo_postal","=", $request->zip_code)
        ->where("uso_construccion","=", $constructionType)
        ->get();

        $itemsQuantity = count($zone->all());

        
        $itemsQuantityVerify = $this->validateQuantity($itemsQuantity);

        if($itemsQuantityVerify === false){
            return $this->errors(200, 'It Was found 0 items.');  
        }


        $result = json_decode($zone, true);
        
        switch($request->operator){

            case 'max': return $this->max($result); break;

            case 'min': return $this->min($result); break;

            case 'avg': return $this->avg($result); break;

            default:
                return $this->errors(400, 'the paramater aggregate must to be max, min or avg.');
            break;

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

    /**
     * Return messages with true or false after validate if quantity is major than zero
     *
     * @param  int  $quantity
     * @return Bool
     */
    public static function validateQuantity(Int $quantity){

        if($quantity > 0){
            return true;
        }else{
            return false;
        }

    }



}
