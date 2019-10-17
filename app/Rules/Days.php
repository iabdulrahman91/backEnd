<?php

namespace App\Rules;
use Exception;
use Carbon\Carbon;
use Illuminate\Contracts\Validation\Rule;

class Days implements Rule
{
    private $date;
    private $today;
    private $msg;


    /**
     * Create a new rule instance.
     *
     * @return void
     */
    public function __construct()
    {
        //
        $this->date = new Carbon();
        $this->today = Carbon::today();
        $this->msg = array();
    }

    /**
     * Determine if the validation rule passes.
     *
     * @param string $attribute
     * @param mixed $value
     * @return bool
     */
    public function passes($attribute, $value)
    {
        // validate number of dates : min is 2 to form a sequence
        $value = json_decode($value);
        if (count($value) < 2) {
            array_push($this->msg, "require two dates minimum");
            return false;
        }
        // validate dates format
        $days = array_map(function ($a) {
            try {
                $d = $this->date::parse($a);
                return $d;
            } catch (Exception $err) {
                array_push($this->msg, $a . " doesn't follow correct date format dd-mm-yyyy");
                return false;
            }

        }, $value);

        // ensure all days after today
        sort($days);
        if (min($days) <= $this->today) {
            array_push($this->msg, "dates must be after today");
            return false;
        }

        // validate correct sequence : sequence/s of two dates min
        $d = min($days);
        $c = 0;

        foreach ($days as $day) {

            if ($day == $d) {
                $c++;
                $d->addDays(1);
            } else {
                if ($c < 2) {
                    array_push($this->msg, "invalid dates sequence");
                    return false;
                }
                $c = 1;
                $d = $day;
                $d->addDays(1);
            }
        }

        //for the last date sequence or first if we just have one date sequence
        if ($c < 2) {
            array_push($this->msg, "invalid dates sequence");
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
