<?php

require_once "base.php";

$ucid = "jjc88"; // <-- set your ucid


$array1 = [0.1, 0.2, 0.3, 0.4, 0.5, 0.6];
$array2 = [1.0000001, 1.0000002, 1.0000003, 1.0000004, 1.0000005];
$array3 = [1.0 / 3.0, 2.0 / 3.0, 4.0 / 3.0, 8.0 / 3.0, 8.0 / 3.0];
$array4 = [1e16, 1.0, -1e16, 2.0, -2.0, 1e-16];
$array5 = [M_PI, M_E, sqrt(2), sqrt(3), sqrt(5), log(2), log10(3)];


function sumValues($arr, $arrayNumber)
{
    // Only make edits between the designated "Start" and "End" comments
    printArrayInfoDouble($arr, $arrayNumber);

    // Challenge 1: Sum all the values of the passed in array and assign to `total`
    // Challenge 2: Have the sum be represented as a number with exactly 2 decimal places, assign to `modifiedTotal`
    // Example: 0.1 would be shown as 0.10, 1 would be shown as 1.00, etc
    // Step 1: sketch out plan using comments (include ucid and date)
    // Step 2: Add/commit your outline of comments (required for full credit)
    // Step 3: Add code to solve the problem (add/commit as needed)

    $total = 0;
    // Start Solution Edits
    // Solve Challenge 1 here
    // jjc88 02-24-2025, This code will go through the array and scan each element within. It will then add up the element into a total variable. 
    // Then the total is number formated to the second decimal place.
    //This code will go through the arrays and go through each element, then adding the element to the total. 
    foreach($arr as $element){
        $total += $element;

    }
    // Solve Challenge 2 here
    //This code will format the total variable into only outputing to the second decimal place
    $modifiedTotal = number_format($total,2);

    // End Solution Edits
    echo "<p>Total Raw Value: {$total}</p>";
    echo "<p>Total Modified Value: {$modifiedTotal}</p>";
    echo "<br>______________________________________<br>";
}

// Run the problem
printHeader($ucid, 2);
sumValues($array1, 1);
sumValues($array2, 2);
sumValues($array3, 3);
sumValues($array4, 4);
sumValues($array5, 5);
printFooter($ucid, 2);