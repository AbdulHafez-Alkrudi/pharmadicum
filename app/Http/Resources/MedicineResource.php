<?php

namespace App\Http\Resources;

use App\Models\ExpirationMedicine;
use App\Models\FavoriteMedicine;
use App\Models\Medicine;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class MedicineResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request , $get_favorite = false)
    {

        $user = auth()->id();
        $medicine_id = $this->id ;
        $data = parent::toArray($request);
        $data['is_favorite'] = FavoriteMedicine::query()->where([
                                                                ['medicine_id' , '=' , $medicine_id],
                                                                ['user_id' , '=' , $user]])->exists();
        $data['amount'] = ExpirationMedicine::query()
                    ->where('medicine_id' ,$medicine_id)
                    ->sum('amount');
        if($data['image'] != null){
            $data['image'] = base64_encode(file_get_contents(public_path($data['image'])));
        }
        return $data;
    }
}
