<?php

namespace App\Http\Requests;

use App\Rules\WorkspaceExists;
use Illuminate\Contracts\Database\Query\Builder;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class ChannelCreateRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        $workspace = $this->route('workspace');

        return [
            'name' => ['required', 'max:80',  Rule::unique('channels', 'name')->where(function ($query) use ($workspace) {
                return $query->where('workspace_id', $workspace->id);
            })],
            'is_private' => ['required', 'boolean']
        ];
    }
}
