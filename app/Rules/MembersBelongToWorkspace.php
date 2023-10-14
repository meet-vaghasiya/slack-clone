<?php

namespace App\Rules;

use App\Models\Member;
use Closure;
use Illuminate\Contracts\Validation\ValidationRule;

class MembersBelongToWorkspace implements ValidationRule
{
    /**
     * Run the validation rule.
     *
     * @param  \Closure(string): \Illuminate\Translation\PotentiallyTranslatedString  $fail
     */
    public $workspace;
    public function __construct($param)
    {
        $this->workspace = $param;
    }



    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        $isAllvalid = Member::where('workspace_id', $this->workspace->id)->whereIn('id', $value)->count() == count($value);
        if (!$isAllvalid) {
            $fail('All member should belong to this workspace.');
        }
    }
}
