<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class FilmesFormRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'nome' => ['required', 'min:2'],
            'descricao' => ['required', 'min:20'],
            'categoria' => ['required'],
            'urlimg' => ['required', 'image', 'mimes:jpeg,png,jpg,gif', 'max:2048'],
            'fk_ator_principal' => ['required'],
            'fk_diretor' => ['required'],
        ];
    }

}
