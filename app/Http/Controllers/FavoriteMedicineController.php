<?php

namespace App\Http\Controllers;

use App\Models\FavoriteMedicine;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class FavoriteMedicineController extends BaseController
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $user = Auth::user();
        return $this->sendResponse($user->favorites);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {

        $validator = Validator::make($request->all() , [
           'medicine_id' => 'required',
        ]);
        if($validator->fails()){
            return $this->sendError($validator->errors());
        }
        // first i should make sure that this medicine doesn't belong to the favorite list of that user
        // if it was there, then I should delete it
        $user = Auth::user();
        $was_favorite = FavoriteMedicine::query()
            ->where([
                ['user_id' , '=' , $user->id],
                ['medicine_id' , '=' , $request['medicine_id']]
            ])->first();

        if(!is_null($was_favorite)){
            return $this->destroy($was_favorite);
        }
        $favorite = FavoriteMedicine::create([
            'user_id' => $user->id,
            'medicine_id' => $request['medicine_id']
        ]);
        return $this->sendResponse($favorite, 'adding_favorite');
    }

    /**
     * Display the specified resource.
     */
    public function show($id)
    {
    }

    /**
     * Update the specified resource in storage.
     */
    public function update()
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(FavoriteMedicine $favoriteMedicine)
    {
        $favoriteMedicine->delete();
        return $this->sendResponse([] , 'destroy_favorite');
    }
}
