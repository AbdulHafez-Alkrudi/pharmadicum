<?php

namespace App\Http\Controllers;

use App\Http\Resources\MedicineResource;
use App\Models\Category;
use App\Models\Company;
use App\Models\ExpirationMedicine;
use App\Models\FavoriteMedicine;
use App\Models\Medicine;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class MedicineController extends BaseController
{
    /**
     * Display a listing of the resource.
     */
    public function index(): JsonResponse
    {
        $lang = request('lang');
        $medicines = $this->get_medicine($lang);

        return $this->sendResponse($medicines, "medicines");
    }



    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        /* TODO: checking if the given medicine is already in the stock
         there are two important things, the first one is:
         when the admin wants to add a new medicine that doesn't exist in the stock
         then he should write all the information about it
         but when the medicine is already in the stock,
         here he should just write the amount and the expiration date
        */

        $validator = Validator::make($request->all(), [
            "category_id" => 'required',
            "company_name_EN" => 'required',
            "company_name_AR" => 'required',
            "scientific_name_EN" => 'required',
            "economic_name_EN" => 'required',
            "scientific_name_AR" => 'required',
            "economic_name_AR" => 'required',
            "amount" => 'required',
            "expiration_date" => 'required|date',
            "unit_price" => 'required',
            'image' => ['image' , 'mimes:jpeg,png,bmp,jpg,gif,svg']
        ]);
        if ($validator->fails()) {
            return $this->sendError($validator->errors());
        }
        $image= $request->file('image');

        $medicine_image = null;
        if($request->hasFile('image')){
            $medicine_image = time().'.'.$image->getClientOriginalExtension();
            $image->move(public_path('image'),$medicine_image);
            $medicine_image='image/'.$medicine_image ;
        }


        $company = DB::table('companies')
            ->where('name_EN', 'regexp', $request['company_name_EN'])
            ->first();
        if (is_null($company)) {
            $company = Company::create([
                'name_EN' => $request['company_name_EN'],
                'name_AR' => $request['company_name_AR']
            ]);
        }
        // TODO: creating the medicine expiration table and see if anything should change right here

        //$medicine = Medicine::create($request->all());
        $medicine = Medicine::create([
            "category_id" => $request['category_id'],
            "company_id" => $company->id,
            "scientific_name_EN" => $request['scientific_name_EN'],
            "scientific_name_AR" => $request['scientific_name_AR'],
            "economic_name_EN" => $request['economic_name_EN'],
            "economic_name_AR" => $request['economic_name_AR'],
            "unit_price" => $request["unit_price"],
            "image" => $medicine_image
        ]);
        ExpirationMedicine::create([
            'medicine_id' => $medicine->id,
            'amount' => $request['amount'],
            'expiration_date' => $request['expiration_date']
        ]);
        return $this->show($medicine->id);
    }


    /**
     * Display the specified resource.
     */
    public function show($id)
    {
        // this function return the information to the user
        // TODO: i should make a function to return all the medicines to the admin
        $lang = request('lang');
        $medicine = $this->get_medicine($lang, $id);
        return $this->sendResponse($medicine, "medicine");
    }


    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        // the data that I want to update is in the request and i have the medicine id
        // so everything is under control

        // doing s simple validation to the medicine_id,category_id and company_id
        if (!Medicine::where('id', $id)->exists())
            return $this->sendError("The medicine doesn't found");
        if ($request['category_id'] != null && !Category::where('id', $request['category_id'])->exists()) {
            return $this->sendError("the category id isn't valid");
        }
        if ($request['company_id'] != null && !Company::where('id', $request['company_id'])->exists()) {
            return $this->sendError("the company id isn't valid");
        }
        $medicine = Medicine::find($id);
        $medicine->update($request->except('lang'));
        return $this->show($id);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Medicine $medicine)
    {
        //
    }

    /**
     * @param mixed $lang
     */
    protected function get_medicine(mixed $lang, $id = null): MedicineResource|AnonymousResourceCollection
    {
        $medicines = Medicine::query()
            ->when(
                $lang == 'ar',
                function ($query) {
                    return $query
                        ->select(
                            'id',
                            'category_id',
                            'company_id',
                            'scientific_name_AR as scientific_name',
                            'economic_name_AR as economic_name',
                            'image',
                            'unit_price'
                        )
                        ->with([
                            'category:id,name_AR as name',
                            'company:id,name_AR as name',
                            'batches:medicine_id,amount,expiration_date'
                        ])
                        ->filter(request(['category', 'search']));
                },
                function ($query) {
                    return $query
                        ->select(
                            'id',
                            'category_id',
                            'company_id',
                            'scientific_name_EN as scientific_name',
                            'economic_name_EN as economic_name',
                            'image',
                            'unit_price'
                        )
                        ->with([
                            'category:id,name_EN as name',
                            'company:id,name_EN as name',
                            'batches:medicine_id,amount,expiration_date'
                        ])
                        ->filter(request(['category', 'search']));
                }
            )
            ->withCount('favorite_users as popularity')
            ->OrderBy('popularity', 'DESC')
            ->when(
                $id == null,
                function ($query) {
                    return $query->paginate(5)
                        ->withQueryString();
                },
                function ($query) use ($id) {
                    return $query->find($id);
                }
            );
        if ($id != null)
            return new MedicineResource($medicines);
        return MedicineResource::collection($medicines);
    }
}
