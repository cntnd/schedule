<?php
// cntnd_schedule_output

// setlocale
$loc_de = setlocale(LC_ALL, 'de_DE@euro', 'de_DE', 'deu_deu');

// contenido vars
$orig_orderLeft   = "CMS_VALUE[10]";
$orig_orderRight  = "CMS_VALUE[11]";
$orderLeft        = json_decode(html_entity_decode($orig_orderLeft,ENT_QUOTES), true);
$orderRight       = json_decode(html_entity_decode($orig_orderRight,ENT_QUOTES), true);

$moduleActive     = "CMS_VALUE[12]";

// laden der daten
// nächstes spiel auslesen
// $sql        =   "SELECT * FROM spielplan WHERE Team = '".$$mannschaft."' AND Spieldatum >= '".date("Y-m-d")."'  ORDER BY Spieldatum ASC LIMIT 0, 1";
$sql = "SELECT * FROM spielplan WHERE Spieldatum >= '".date("Y-m-d")."' GROUP BY Team ORDER BY Team, Spieldatum ASC";
$result = $db->query($sql);
$count = $db->num_rows();

if ($result!==false) {
    while ($db->next_record()) {
        // datum
        $TagKurz = $db->f("TagKurz");
        $Spieldatum = $db->f("Spieldatum");
        $Spielzeit = $db->f("Spielzeit");
        $spiel_datum = $TagKurz . " " . date('d.m.Y', strtotime($Spieldatum));
        $spiel_zeit = '';
        if (!empty($Spielzeit) AND $Spielzeit != "00:00:00") {
            $spiel_zeit = date('H:i', strtotime($Spielzeit));
        }
        $TeamnameA = $db->f("TeamnameA");
        $TeamnameB = $db->f("TeamnameB");
        if ($db->f("VereinsnummerA") == '10311') {
            $TeamnameA = 'FCL';
        }
        if ($db->f("VereinsnummerB") == '10311') {
            $TeamnameB = 'FCL';
        }

        $spiel = array(
            'Team' => $db->f("Team"),
            'data_full_date' => strftime('%A, %e. %B %G', strtotime($Spieldatum)) . ' ' . $spiel_zeit . ' Uhr',
            'data_datum' => $spiel_datum,
            'data_zeit' => $spiel_zeit,
            'data_ort' => $db->f("Spielort"),
            'SpielTyp' => $db->f("SpielTyp"),
            'Bezeichnung' => $db->f("Bezeichnung"),
            'TagKurz' => $TagKurz,
            'Spieldatum' => $Spieldatum,
            'Spielzeit' => $db->f("Spielzeit"),
            'TeamnameA' => $TeamnameA,
            'VereinsnummerA' => $db->f("VereinsnummerA"),
            'TeamnameB' => $TeamnameB,
            'VereinsnummerB' => $db->f("VereinsnummerB")
        );
        $data[$db->f('Team')] = $spiel;
    }
}

foreach ($orderLeft as $value){
    $data[$value['team']]['data_team']=$value['name'];
    $data[$value['team']]['data_url']=$value['url'];
    $data[$value['team']]['noData']="false";

    $spieleLeft[] = $data[$value['team']];
}

foreach ($orderRight as $value){
    $data[$value['team']]['data_team']=$value['name'];
    $data[$value['team']]['data_url']=$value['url'];
    $data[$value['team']]['noData']="false";

    $spieleRight[] = $data[$value['team']];
}

$smarty = cSmartyFrontend::getInstance();
$smarty->assign('spieleLeft', $spieleLeft);
$smarty->assign('spieleRight', $spieleRight);
$smarty->display('get.tpl');
?>