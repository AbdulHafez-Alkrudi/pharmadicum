<?php

namespace App\Http\Controllers;

use App\Models\ExpirationMedicine;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class ExpirationMedicineController extends BaseController
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        // when i want to add more medicines to the stock, i need the quantity,expiration_date,medicine_id
        $validator = Validator::make($request->all(),[
            'medicine_id' => 'required',
            'expiration_date' => 'required|date',
            'quantity' => 'required'
        ]);
        if($validator->fails()){
            return $this->sendError($validator->errors());
        }
        // now before i create this batch,i'll check if there is any batch that has the same expiration date
        // if so, i'll just update it

        $checking_batch = ExpirationMedicine::query()
                ->where([
                    ['medicine_id'     , '=' , $request['medicine_id']],
                    ['expiration_date' , '=' , $request['expiration_date']]
                ])->first();
        $batch = null ;
        $batch = is_null($checking_batch) ? ExpirationMedicine::create($request->all()) : $this->update($request, $checking_batch);
        return $this->sendResponse($batch);
    }

    /**
     * Display the specified resource.
     */
    public function show(ExpirationMedicine $medicineExpiration)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, ExpirationMedicine $batch)
    {
        $batch->update($request->all());
        return $batch;
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(ExpirationMedicine $batch)
    {
        //when the quantity is zero in any batch I'll delete it
        $batch->delete();
        return $this->sendResponse();
    }
}
