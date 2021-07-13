<?php

$tab1['id'] = 4;
$tab1['nazwa'] = "Hubert";

print_r($tab1);

$tab2['nazwisko'] = "Golewski";
$tab2['zawod'] = "Informatyk";

echo "<br>";

print_r($tab2);

echo "<br>";

$tab1 += $tab2;
print_r($tab1);