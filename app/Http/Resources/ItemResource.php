<?php



namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;
use App\Models\User;
use App\Models\Category;
use App\Http\Resources\OrderResource;

class ItemResource extends JsonResource
{

    public function toArray($request)
    {

        if($this->category_id){
            $category = Category::where('id',$this->category_id)->first();
            if($category){
                $category = $category->title;
            }
            else{
                $category = "";
            }
        }
        else{
            $order = [];
        }

        $menu_cat_id = 1;
        if($this->menu_cat_id){
            $menu_cat_id = $this->menu_cat_id;
        }
        $menu_order = 1;
        if($this->$menu_order){
            $menu_order = $this->$menu_order;
        }

        $order= 0;
        if($this->order){
            $order = $this->$order;
        }
    
        return [

            'id' => $this->id,
            'title' => $this->title ,
            'description' => $this->description,
            'category' => $category,
            'is_available' => $this->is_available ,
            'in_orderes' => $this->in_orderes,
            'order' => $order,
            'menu_order' => $menu_order	,
            'menu_cat_id' => $menu_cat_id ,
            'monthly_avg' => $this->monthly_avg,
            'rate_star' => $this->rate_star,
            'sell_price' => $this->sell_price,

        ];

    }

}

