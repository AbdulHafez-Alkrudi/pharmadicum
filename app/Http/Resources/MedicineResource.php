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
    public function toArray(Request $request , $get_favorite = false): array
    {

        $user = auth()->id();
        $id = $this->id ;
        $data = parent::toArray($request);
        $data['is_favorite'] = FavoriteMedicine::query()->where([
                                                                ['medicine_id' , '=' , $id],
                                                                ['user_id' , '=' , $user]])->exists();
        $data['quantity'] = ExpirationMedicine::query()
                    ->sum('quantity');

        if($data['image'] != null){

            $data['image'] = base64_encode(file_get_contents(public_path($data['image'])));
        }
        return $data;
    }
}
