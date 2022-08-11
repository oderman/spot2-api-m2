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
        //dd($request);

        $zone = Zone::where("codigo_postal","=", $request->zip_code)->get();

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
        
        $maxPriceUnit = 0;
        $maxPriceUnitConstruction = 0;

        foreach($zones as $key => $value)
        {
            $priceUnit = $value["superficie_terreno"]  / ($value["valor_suelo"] - $value["subsidio"]);
            $priceUnitConstruction = $value["superficie_construccion"]  / ($value["valor_suelo"] - $value["subsidio"]);

            if($priceUnit > $maxPriceUnit ){
                $maxPriceUnit = $priceUnit;
            }

            if($priceUnitConstruction > $priceUnitConstruction ){
                $maxPriceUnitConstruction = $priceUnitConstruction;
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

        $idQuery = 0;

        $price_unit = $zones[$idQuery]["superficie_terreno"]  / ($zones[$idQuery]["valor_suelo"] - $zones[$idQuery]["subsidio"]);
        $price_unit_construction = $zones[$idQuery]["superficie_construccion"]  / ($zones[$idQuery]["valor_suelo"] - $zones[$idQuery]["subsidio"]);

        return response()->json([
            'status' => true,
            'payload' => [
                "type" => "min",
                "price_unit" => $price_unit,
                "price_unit_construction" => $price_unit_construction,
                "elements" => $itemsQuantity

            ]
        ], 200);
    }

    public function avg(array $zones = [])
    {
        $itemsQuantity = count($zones);

        $idQuery = 0;

        $price_unit = $zones[$idQuery]["superficie_terreno"]  / ($zones[$idQuery]["valor_suelo"] - $zones[$idQuery]["subsidio"]);
        $price_unit_construction = $zones[$idQuery]["superficie_construccion"]  / ($zones[$idQuery]["valor_suelo"] - $zones[$idQuery]["subsidio"]);

        return response()->json([
            'status' => true,
            'payload' => [
                "type" => "avg",
                "price_unit" => $price_unit,
                "price_unit_construction" => $price_unit_construction,
                "elements" => $itemsQuantity

            ]
        ], 200);
    }



}
