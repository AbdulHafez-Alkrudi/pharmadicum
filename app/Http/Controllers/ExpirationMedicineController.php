<?php

namespace App\Http\Controllers;

use App\Models\ExpirationMedicine;
use App\Models\Medicine;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
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
        // when i want to add more medicines to the stock,
        // i need the quantity,expiration_date, economic name of the medicine
        $validator = Validator::make($request->all(), [
            'economic_name' => 'required',
            'category_id' => 'required',
            'expiration_date' => 'required|date',
            'amount' => 'required'
        ]);
        if ($validator->fails()) {
            return $this->sendError($validator->errors());
        }
        // checking if the medicine exists or not
        // receiving
        $medicine = Medicine::where([
                ['economic_name_AR', $request['economic_name'],
                ['category_id', $request['category_id']]]
        ])
            ->orWhere([
                ['economic_name_EN', $request['economic_name']],
                ['category_id', $request['category_id']]
            ])->first();

        if (is_null($medicine)) {
            return $this->sendError("This medicine doesn't exist");
        }


        // now before i create this batch,i'll check if there is a batch that has the same expiration date
        // if so, i'll just update it

        $checking_batch = ExpirationMedicine::query()
            ->where([
                ['medicine_id', '=', $medicine->id],
                ['expiration_date', '=', $request['expiration_date']]
            ])->first();
        $request['medicine_id'] = $medicine->id;
        $batch = is_null($checking_batch) ? ExpirationMedicine::create($request->except('economic_name')) : $this->update($request, $checking_batch);
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
    public function update(Request $request, ExpirationMedicine $batch): ExpirationMedicine
    {
        /*  Log::debug('batch :' , ['batch' => $batch]);
        Log::debug('request :' , ['amount' => $request['amount']]);
      */

        $batch['amount'] += $request['amount'];
        $batch->save();
        return $batch;
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(ExpirationMedicine $batch): JsonResponse
    {
        //when the quantity is zero in any batch I'll delete it
        $batch->delete();
        return $this->sendResponse();
    }
}
