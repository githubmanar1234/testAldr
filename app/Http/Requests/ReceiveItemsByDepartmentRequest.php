<?php 
namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ReceiveItemsByDepartmentRequest extends FormRequest{
    public function rules():array
    {
        return [
            'department_id' => 'required|exists:departments,id',
        ];
    }
}





?>