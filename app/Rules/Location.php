<?php

namespace App\Rules;

use Exception;
use Illuminate\Contracts\Validation\Rule;

class Location implements Rule
{

    private $msg;

    /**
     * Create a new rule instance.
     *
     * @return void
     */
    public function __construct()
    {
        //
        $this->msg = array();
    }

    /**
     * Determine if the validation rule passes.
     *
     * @param  string  $attribute
     * @param  mixed  $value
     * @return bool
     */
    public function passes($attribute, $value)
    {
        // validate format
        try{
            $lat = $value["lat"];
            $lng = $value["lng"];

            // validate field input
            if (empty($lat) || !is_numeric($lat)){
                array_push($this->msg, "invalid location.lat");
                return false;
            }
            if (empty($lng) || !is_numeric($lng)){
                array_push($this->msg, "invalid location.lng");
                return false;
            }


        } catch (Exception $err) {
            array_push($this->msg, "invalid item format");
            return false;
        }
        return true;
    }

    /**
     * Get the validation error message.
     *
     * @return string
     */
    public function message()
    {
        return $this->msg;
    }
}
