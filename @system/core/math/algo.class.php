<?php
namespace Math;
class Algo {
    
    /**
     * Нохождение расстояния между строками/массивами методом Levenshtein's
     * @param string|array $Src
     * @param string|array $Dest
     * @param float $CostInsert
     * @param float $CostReplace
     * @param float $CostDelete
     * @return float расстояние между элементами
     */
    static public function Levenshtein($Src,$Dest,$CostInsert=1.,$CostReplace=1.,$CostDelete=1.) {
	$srcLen = is_string($Src) ? strlen($Src) : count($Src);
	$dstLen = is_string($Dest) ? strlen($Dest) : count($Dest);

	if($srcLen == 0) return $dstLen * $CostInsert;
	if ($dstLen == 0) return $srcLen * $CostDelete;

	$dist = array_fill(0, $dstLen + 1, 0);
	for($i=0;$i<=$dstLen;++$i) {
	    $dist[$i] = $i * $CostInsert;
	}

	for ($i = 0; $i < $srcLen; $i++) {
	    $_dist = array($i + 1);
	    $char = $Src[$i];
	    for ($j = 0; $j < $dstLen; $j++) {
		$cost = $char == $Dest[$j] ? 0 : $CostReplace;
		$_dist[$j + 1] = min(
			$dist[$j + 1] + $CostDelete, // deletion
			$_dist[$j] + $CostInsert, // insertion
			$dist[$j] + $cost    // substitution
		);
	    }
	    $dist = $_dist;
	}
	return $dist[$j];
    }
    
    static public function DamerauLevenshtein($Src, $Dest, $CostInsert = 1, $CostReplace = 1, $CostDelete = 1, $CostTransposition = 1) {
	$srcLen = is_string($Src) ? strlen($Src) : count($Src);
	$dstLen = is_string($Dest) ? strlen($Dest) : count($Dest);

	if ($srcLen == 0)
	    return $dstLen * $CostInsert;
	if ($dstLen == 0)
	    return $srcLen * $CostDelete;

	$cost = -1;
	$del = 0;
	$sub = 0;
	$ins = 0;
	$trans = 0;
	$__matrix = array(array());
	for ($i = 0; $i <= $srcLen; $i += 1) {
	    $__matrix[$i][0] = $i > 0 ? $__matrix[$i - 1][0] + $CostDelete : 0;
	}
	for ($i = 0; $i <= $dstLen; $i += 1) {
	    // Insertion actualy
	    $__matrix[0][$i] = $i > 0 ? $__matrix[0][$i - 1] + $CostInsert : 0;
	}
	for ($i = 1; $i <= $srcLen; $i += 1) {
	    // Curchar for the first string
	    $cOne = $Src[$i - 1];
	    for ($j = 1; $j <= $dstLen; $j += 1) {
		// Curchar for the second string
		$cTwo = $Dest[$j - 1];
		// Compute substitution cost
		if ($cOne == $cTwo) {
		    $cost = 0;
		    $trans = 0;
		} else {
		    $cost = $CostReplace;
		    $trans = $CostTransposition;
		}
		// Deletion cost
		$del = $__matrix[$i - 1][$j] + $CostDelete;
		// Insertion cost
		$ins = $__matrix[$i][$j - 1] + $CostInsert;
		// Substitution cost, 0 if same
		$sub = $__matrix[$i - 1][$j - 1] + $cost;
		// Compute optimal
		$__matrix[$i][$j] = min($del, $ins, $sub);
		// Transposition cost
		if (($i > 1) && ($j > 1)) {
		    // Last two
		    $ccOne = $Src[$i - 2];
		    $ccTwo = $Dest[$j - 2];
		    if ($cOne == $ccTwo && $ccOne == $cTwo) {
			// Transposition cost is computed as minimal of two
			$__matrix[$i][$j] = min($__matrix[$i][$j], $__matrix[$i - 2][$j - 2] + $trans);
		    }
		}
	    }
	}
	return $__matrix[$srcLen][$dstLen];
    }
    /**
     * Кластеризация данны по методу k средних
     * @param array $Data массив признаков объектов 
     * @return \Math\Algo\KMeans
     */
    static public function KMeans(array $Data) 
    {
	include_once('algo/kmeans.algo.php');
	return new \Math\Algo\KMeans($Data);
    }
    
    /**
     * Вычисление факториала
     * @param int $N
     * @param bool $Aprox Высичление приблизительного значения факторила по формуле Стирлинга
     */
    static public function Factorial($N,$Aprox=false) {
	if($N == 0) return 0;
	if($N == 1) return 1;
	if(!$Aprox) {
	    $Factorial = 1;
	    for($i=2;$i<=$N;$i++) $Factorial *= $i;
	    return $Factorial;
	}
	else {
	    return intval(sqrt( M_PI * 2 * $N ) * pow($N / M_E,$N) * pow(M_E,1/(12*$N)));
	}
    }
    
    /**
     * Вычисление числа Фибоначчи для заданного $N
     * @param int $N
     * @return int число Фибонначи
     */
    static public function Fibonacci($N) {
	if($N == 0) return 0;
	if ($N <= 2) return 1;
	$x = 1;
	$y = 1;
	$Fibonacci = 0;
	for ($i = 2; $i < $N; $i++)
	{
		$Fibonacci = $x + $y;
		$x = $y;
		$y = $Fibonacci;
	}
	return $Fibonacci;
    }
    /**
     * Евклидово расстояние между объектами $A и $B
     * @param array $A значения признаков объекта A
     * @param array $B значения признаков объекта B
     * @param array $W - весовые коэффициенты i-го признака
     * @return float 
     */
    static public function EuclideanDistance($A,$B,$W=null) {
	$Sum = 0.0;
	if(!empty($W)) {
	    for($i=0;$i<min(count($A),count($B));$i++) {
		$Sum += $W[$i] * pow($A[$i]-$B[$i],2);
	    }
	}
	else {
	    for($i=0;$i<min(count($A),count($B));$i++) {
		$Sum += pow($A[$i]-$B[$i],2);
	    }
	}
	return sqrt($Sum);
    }
    /**
     * Косинусная мера сходства между объектами $A и $B
     * @param array $A значения признаков объекта A
     * @param array $B значения признаков объекта B
     * @param array $W - весовые коэффициенты i-го признака
     * @return float 
     */
    static public function CosineSimilarityMeasure($A,$B,$W=null) {
	$SumX1 = $SumX2 = $Sum = 0.0;
	if(!empty($W)) {
	    for($i=0;$i<min(count($A),count($B));$i++) {
		$Sum += $W[$i] * $A[$i] * $W[$i] * $B[$i];
		$SumX1 += $W[$i] * $A[$i] * $W[$i] * $A[$i];
		$SumX2 += $W[$i] * $B[$i] * $W[$i] * $B[$i];
	    }
	}
	else {
	    for($i=0;$i<min(count($A),count($B));$i++) {
		$Sum += $A[$i] * $B[$i];
		$SumX1 += $A[$i]*$A[$i];
		$SumX2 += $B[$i]*$B[$i];
	    }
	}
	return $Sum / sqrt($SumX1*$SumX2);
    }
    
    /**
     * Манхэттеновское расстояние между объектами $A и $B
     * @param array $A значения признаков объекта A
     * @param array $B значения признаков объекта B
     * @param array $W - весовые коэффициенты i-го признака
     * @return float
     */
    static public function ManhattanDistance($A,$B,$W=null) {
	$Sum = 0.0;
	if(!empty($W)) {
	    for($i=0;$i<min(count($A),count($B));$i++) {
		$Sum += $W[$i] * abs($A[$i]-$B[$i]);
	    }
	}else{
	    for($i=0;$i<min(count($A),count($B));$i++) {
	    $Sum += abs($A[$i]-$B[$i]);
	}
	}
	return $Sum;
    }
}