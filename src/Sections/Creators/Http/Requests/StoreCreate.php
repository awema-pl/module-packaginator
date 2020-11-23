<?php

namespace AwemaPL\Packaginator\Sections\Creators\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreCreate extends FormRequest
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
            'name_package' => 'required|max:255|string|regex:/^[a-zA-Z]+$/u'
        ];
    }

    /**
     * Get custom attributes for validator errors.
     *
     * @return array
     */
    public function attributes()
    {
        return [
            'name_package' => _p('packaginator::requests.creator.attributes.name_package', 'name package'),
        ];
    }

    /**
     * Get the error messages for the defined validation rules.
     *
     * @return array
     */
    public function messages()
    {
        return [
            'name_package.regex' => _p('packaginator::requests.creator.messages.name_package_regex', 'Only letters allowed.'),
        ];
    }
}
