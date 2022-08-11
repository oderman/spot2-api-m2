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


    
    public function showOperator(Request $request)
    {

        if(!isset($_GET['construction_type']) or !is_numeric($_GET['construction_type'])){

            return response()->json([
                'status' => false,
                'msg' => 'construction_type paramater is required and must to be of type numeric. ',
            ], 400);

        }

        if($_GET['construction_type'] <1 or $_GET['construction_type']>7){

            return response()->json([
                'status' => false,
                'msg' => 'construction_type must to be one number between 1 and 7',
            ], 400);

        }

        switch($_GET['construction_type']){
            case 1:
                $constructionType = 'Areas verdes';
            break;

            case 2:
                $constructionType = 'Centro de barrio';
            break; 

            case 3:
                $constructionType = 'Equipamiento';
            break; 

            case 4:
                $constructionType = 'Habitacional';
            break; 

            case 5:
                $constructionType = 'Habitacional y comercial';
            break; 

            case 6:
                $constructionType = 'Industrial';
            break; 

            case 7:
                $constructionType = 'Sin Zonificacion';
            break; 
        }

        $zone = Zone::
        where("codigo_postal","=", $request->zip_code)
        ->where("uso_construccion","=", $constructionType)
        ->get();

        $itemsQuantity = count($zone->all());

        if($itemsQuantity === 0){
            return response()->json([
                'status' => false,
                'msg' => 'It Was found 0 items.',
            ], 200);
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

            $priceUnit = $value["superficie_terreno"]  / ($value["valor_suelo"] - $value["subsidio"]);
            $priceUnitConstruction = $value["superficie_construccion"]  / ($value["valor_suelo"] - $value["subsidio"]);

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


        return response()->json([
            'status' => true,
            'payload' => [
                "type" => "max",
                "price_unit" => $maxPriceUnit,
                "price_unit_construction" => $maxPriceUnitConstruction,
                "elements" => $itemsQuantity
            ]
        ], 200);

    }

    public function min(array $zones = [])
    {
        
        $itemsQuantity = count($zones);

        foreach($zones as $key => $value)
        {

            $priceUnit = $value["superficie_terreno"]  / ($value["valor_suelo"] - $value["subsidio"]);
            $priceUnitConstruction = $value["superficie_construccion"]  / ($value["valor_suelo"] - $value["subsidio"]);

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


        return response()->json([
            'status' => true,
            'payload' => [
                "type" => "min",
                "price_unit" => $minPriceUnit,
                "price_unit_construction" => $minPriceUnitConstruction,
                "elements" => $itemsQuantity
            ]
        ], 200);

    }
    

    public function avg(array $zones = [])
    {
        $itemsQuantity = count($zones);
        $avgPriceUnit = 0;
        $avgPriceUnitConstruction = 0;

        foreach($zones as $key => $value)
        {
            $avgPriceUnit += $value["superficie_terreno"]  / ($value["valor_suelo"] - $value["subsidio"]);
            $avgPriceUnitConstruction += $value["superficie_construccion"]  / ($value["valor_suelo"] - $value["subsidio"]);
        }

        $avgPriceUnit = ($avgPriceUnit/$itemsQuantity);
        $avgPriceUnitConstruction = ($avgPriceUnitConstruction/$itemsQuantity);


        return response()->json([
            'status' => true,
            'payload' => [
                "type" => "avg",
                "price_unit" => $avgPriceUnit,
                "price_unit_construction" => $avgPriceUnitConstruction,
                "elements" => $itemsQuantity
            ]
        ], 200);
    }



}
