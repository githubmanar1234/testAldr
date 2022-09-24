<?php



namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;
use App\Models\User;


class UserResource extends JsonResource

{

    public function toArray($request)
    {

        $accessToken = $this->createToken('authToken')->accessToken;

        if($this->avatar !== null){
            $image = $this->avatar;
        }
        else{
            $image = "#";
        }
        if($this->role_id){
            $role = $this->role->name;
        }
        else{
            $role = "";
        }

        return [

            'id' => $this->id,
            'name' => $this->name ,
            'username' => $this->username,
            'role' => $role,
            'email' => $this->email ,
            'image' => $image,
            'accessToken' =>  $accessToken,

        ];

    }

}

