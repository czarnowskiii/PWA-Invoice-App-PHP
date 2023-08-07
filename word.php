<?php 
//Convert Number to Indian Currency Format
class IndianCurrency{
	function NumberToWords($number) {
    if ($number == 0)
        return "zero";

    $units = array("", "jeden", "dwa", "trzy", "cztery", "pięć", "sześć", "siedem", "osiem", "dziewięć", "dziesięć", "jedenaście", "dwanaście", "trzynaście", "czternaście", "piętnaście", "szesnaście", "siedemnaście", "osiemnaście", "dziewiętnaście");
    $tens = array("", "", "dwadzieścia", "trzydzieści", "czterdzieści", "pięćdziesiąt", "sześćdziesiąt", "siedemdziesiąt", "osiemdziesiąt", "dziewięćdziesiąt");
    $hundreds = array("", "sto", "dwieście", "trzysta", "czterysta", "pięćset", "sześćset", "siedemset", "osiemset", "dziewięćset");
    $thousands = array("", "tysiąc", "milion", "miliard", "bilion", "biliard", "trylion", "tryliard", "kwadrylion", "kwadryliard");

    $scale = 0;
    $thousandsCount = 0;
    $result = "";

    while ($number > 0) {
        $segment = $number % 1000;
        $hundredsValue = (int)($segment / 100);
        $tensValue = (int)($segment % 100 / 10);
        $unitsValue = (int)($segment % 10);

        $segmentResult = "";

        if ($hundredsValue > 0)
            $segmentResult .= $hundreds[$hundredsValue] . " ";

        if ($tensValue > 1)
            $segmentResult .= $tens[$tensValue] . " ";

        if ($tensValue == 1)
            $segmentResult .= $units[$tensValue * 10 + $unitsValue] . " ";
        else if ($unitsValue > 0)
            $segmentResult .= $units[$unitsValue] . " ";

        if ($segmentResult != "") {
            if ($thousandsCount > 0)
                $segmentResult .= $thousands[$thousandsCount] . " ";

            $result = $segmentResult . $result;
        }

        $thousandsCount++;
        $number /= 1000;
        $scale++;
    }

    return trim($result);
}

}
?>