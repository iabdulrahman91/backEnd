<?php

namespace App\Rules;

use Exception;
use Illuminate\Contracts\Validation\Rule;

class Item implements Rule
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
            $company = $value["company"];
            $category = $value["category"];
            $product = $value["model"];

            // validate field input
            if (empty($company) || !is_string($company)){
                array_push($this->msg, "invalid item.company");
                return false;
            }
            if (empty($category) || !is_string($category)){
                array_push($this->msg, "invalid item.category");
                return false;
            }
            if (empty($product) || !is_string($product)){
                array_push($this->msg, "invalid item.product");
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
