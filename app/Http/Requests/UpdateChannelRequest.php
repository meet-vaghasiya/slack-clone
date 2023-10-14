<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateChannelRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;  // todo: only update if user is either him. will create gate for this.
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
            'name' => ['nullable', 'max:80', Rule::unique('channels', 'name')->where(function ($query) use ($workspace) {
                return $query->where('workspace_id', $workspace->id);
            })->ignore($this->channel_id, 'id')],
            'topic' => ['nullable', 'max:255'],
            'description' => ['nullable', 'max:255']
        ];
    }
}
