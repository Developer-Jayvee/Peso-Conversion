<?php

namespace App\Services;

class ConvertServices
{
    CONST _1_19 = [ 'One','Two','Three','Four','Five','Six','Seven','Eight','Nine','Ten' ,'Elevent','Twelve','Thirteen','Fourteen','Fifteen','Sixteen','Seventeen','Eighteen','Nineteen' ];
    CONST TEEN = [ 2 =>'Twenty',3 => 'Thirty',4 => 'Fourty',5 =>'Fifty', 6 =>'Sixty', 7 =>'Seventy',8 => 'Eighty',9 => 'Ninety' ];
    CONST MULT = [
        2 => 'Hundred',
        3 => 'Thousand',
        6 => 'Million',
        9 => 'Billion',
        12 => 'Trillion',
        15 => 'Quadrillion',
        18 => 'Quintillion',
        21 => 'Sextillion',
        24 => 'Septillion',
        27 => 'Octillion'
    ];

    protected CurrencyRateAPIService $currencyAPI;
    protected float $rate = 0;
    public function __construct() {
        $this->currencyAPI = new CurrencyRateAPIService('https://api.frankfurter.app/latest');
    }

    /**
     * Convert amount/value
     *
     * @param  mixed $amount
     * @param  mixed $toString
     * @return string
     */
    public function convertAmount(int | string $amount ,bool $toString = false) : string | int
    {
        $this->rate = $this->currencyAPI->handler()['USD'] ?: 0;

        if(!$toString) return self::convertToInteger($amount);
        return self::convertToString($amount);
    }
    /**
     * Convert value to string
     *
     * @param  mixed $amount
     * @return string
     */
    private function convertToString(string $amount) : string
    {
        try {
            $amount = str_replace(" ","",$amount);
            $integerVal = intval($amount * $this->rate);

            $cents = explode(".",number_format($amount * $this->rate,2))[1] ?? null;
            $cents = self::setTeenString(intval($cents)) ?? "";
            $centString = ($cents ? " And ".$cents." Cents" : "");
            if($integerVal == 0) return "Zero";
            if($integerVal < 20) return self::_1_19[$integerVal - 1]." ".($centString);
            if($integerVal > 19 && $integerVal < 100) return self::setTeenString($integerVal)." ".($centString);

            $arr = array_reverse(explode(",",number_format($integerVal)));
            $index = 0;
            $conversion = "";

            foreach (self::MULT as $key => $value) {
                if($key >= strlen((string) $integerVal)) break;
                $numb = intval($arr[$index]);
                $x = "";
                if($numb == 0){
                    $index++;
                    continue;
                }
                else if($numb < 20) $x = self::_1_19[$numb - 1];
                else if ( $numb < 100) $x = self::setTeenString($numb);
                else if($numb < (10 ** 3))  $x = self::setHundredsString($numb);

                if($key === 2) $conversion = $x;
                else {
                    $conversion = $x. " ".$value.($conversion !== "" ? " And ".$conversion : " ");
                }
                $index++;
            }
            return $conversion.($centString);
        } catch (\Throwable $th) {
            throw new \Exception("Amount must be a numerical value");

        }
    }
    /**
     * Convert value to numeric
     *
     * @param  mixed $amount
     * @return string
     */
    private function convertToInteger(string $amount) : string | int
    {
        if($amount == "0") return 0;
        $amount = (string) $amount;
        $totalInteger = 0;
        $amountArray = explode("|",self::formatStringNumber($amount));
        foreach ($amountArray as $key => $value) {
            $value = ucwords($this->checkNumberFormat($value));
            $multiTotal = self::getMultiValue($value);
            $totalInteger += $multiTotal;
        }
        return  number_format($totalInteger * $this->rate , 2);
    }
    /**
     * Set integer to hundred string
     *
     * @param  mixed $value
     * @return string
     */
    private function setHundredsString(int $value) : string
    {
        $n = number_format($value * 0.01,2);

        $explodeVal = explode(".",(string) $n);
        $hundred = $explodeVal[0];
        $teen = $explodeVal[1];
        return self::_1_19[$hundred - 1]." Hundred ".self::setTeenString(intval($teen));
    }
    /**
     * Set integer to teen string
     *
     * @param  mixed $value
     * @return string
     */
    private function setTeenString(int $value) : string
    {
        if($value <= 0) return "";
        $n = number_format($value * 0.1,1);
        [$x,$y] = explode(".", $n );
        return self::TEEN[$x]." ".ucwords(self::_1_19[$y - 1] ?? "");
    }
    /**
     * Convert value ( Helper )
     *
     * @param  mixed $value
     * @return int
     */
    private function searchEquivalent(string $value) : int
    {
        $_1_19 = array_flip(self::_1_19);
        $teen = array_flip(self::TEEN);
        $mult = array_flip(self::MULT);

        $has1_19 = isset($_1_19[$value])  ? $_1_19[$value] + 1 : null;
        $hasTeen = isset($teen[$value]) ? $teen[$value] * 10  : null ;
        $hasMulti = isset($mult[$value]) ? 10 ** $mult[$value] : null;

        return  $has1_19 ?: $hasTeen ?: $hasMulti ?: 0;
    }
    /**
     * Sum of converted values
     *
     * @param  mixed $amount
     * @return int
     */
    private function getMultiValue(string $amount ) : int
    {
        $arr = explode(" ",trim($amount));

        $total = 0;
        foreach ($arr as $value) {
            $amount = self::searchEquivalent($value);
            if(!$amount) continue;
            if($total > 0 && $amount > $total){
                $total *= $amount;
            }else $total += $amount;
        }
        return $total;
    }
    /**
     * Replace "And" into "|"
     *
     * @param  mixed $value
     * @return string
     */
    private function formatStringNumber(string $value) : string
    {
        return  trim(preg_replace('/\s+/', ' ',str_replace("And","|",ucwords(strtolower($value)))));
    }
    private function checkNumberFormat(string $value)
    {
        $_1_19 = implode("|",self::_1_19);
        $teens = implode("|",self::TEEN);
        $mult = implode("|",self::MULT);
        preg_match_all("/$_1_19/i", $value, $_1_19Arr);
        preg_match_all("/$teens/i", $value, $teensArr);
        preg_match_all("/$mult/i", $value, $multArr);
        return implode(" ",$_1_19Arr[0])." ".implode(" ",$multArr[0])." ".implode(" ",$teensArr[0]);

    }
}
