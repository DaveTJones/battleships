<?php
include 'battleships2.php';

$Doombringer = new Destroyer("Doombringer", new Crew());

$Valkyrie = new Carrier("Valkyrie", new Crew());

$Valkyrie->crew()->recruit($pirates);

$Doombringer->crew()->recruit($pirates);

$Valkyrie->crew()->crew_list();

$Doombringer->crew()->crew_list();

while (true) {
	if ($Doombringer->attacks($Valkyrie) || $Valkyrie->attacks($Doombringer)) {
		break;
	}
}
?>
