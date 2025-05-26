<?php

namespace App\Rules;

use Illuminate\Contracts\Validation\Rule;
use Illuminate\Support\Facades\DB;

class UniqueJsonValue implements Rule
{
    private $validValues, $table, $id, $type;

    public function __construct($table, $id, array $validValues, $type)
    {
        $this->validValues = $validValues;
        $this->table = $table;
        $this->id = $id;
        $this->type = $type;
    }

    public function passes($attribute, $value)
    {
        if (json_last_error() !== JSON_ERROR_NONE) {
            return false;
        }

        foreach ($this->validValues as $validValue) {
            if ($value === json_decode(json_encode($validValue), true)) {
                $query = DB::table($this->table)
                    ->whereJsonContains($attribute, $validValue)
                    ->where('type', $this->type)
                    ->where('id', '<>', $this->id);

                return !$query->exists();
            }
        }

        return true;
    }

    public function message()
    {
        return 'This :attribute must not have more than one per type (sms/email) in the template list!';
    }
}