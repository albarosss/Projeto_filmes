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
            'resumo' => ['required', 'min:20'],
            'urlimg' => ['required', 'image', 'mimes:jpeg,png,jpg,gif', 'max:2048'], // Adicione regras para a imagem
            'fk_ator_principal' => ['required'],
            'fk_diretor' => ['required'],
        ];
    }

}
