<?php

namespace App\Http\Requests;

use App\Rules\MaximumLength;
use App\Rules\UniqueInAdgroup;
use App\Rules\NoDoublePunctuation;
use Illuminate\Foundation\Http\FormRequest;
use App\Rules\OnlyPermittedCharactersInPath;

class StoreAdvert extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true; //the user can only do this from a place they're authorised to
    }

    public function rules()
    {
        return [
            'adgroup_id'    => 'required',
            'final_urls'     => ['required', 'url'],
            'headline_1'    => $this->headlineRules(),
            'headline_2'    => $this->headlineRules(),
            'description'   => $this->descriptionRules(),
            'path_1'        => $this->path1Rules(),
            'path_2'        => $this->path2Rules(),

        ];
    }

    protected function headlineRules()
    {
        return [
            'required',
            new MaximumLength,
            new NoDoublePunctuation,
            new UniqueInAdgroup,
        ];
    }

    protected function descriptionRules()
    {
        return [
            'required',
            new MaximumLength,
            new NoDoublePunctuation,
        ];
    }

    protected function path1Rules()
    {
        return [
            'required_with:path_2',
            new MaximumLength,
            new OnlyPermittedCharactersInPath,
            new NoDoublePunctuation,
        ];
    }

    protected function path2Rules()
    {
        return [
            new MaximumLength,
            new OnlyPermittedCharactersInPath,
            new NoDoublePunctuation,
        ];
    }
}
