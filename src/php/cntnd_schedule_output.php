<?php
// cntnd_schedule_output

// setlocale
$loc_de = setlocale(LC_ALL, 'de_DE@euro', 'de_DE', 'deu_deu');

// contenido vars
$orig_orderLeft   = "CMS_VALUE[10]";
$orig_orderRight  = "CMS_VALUE[11]";
$orig_teams       = "CMS_VALUE[12]";
$moduleActive     = "CMS_VALUE[13]";



// laden der daten
// nächstes spiel auslesen
// $sql        =   "SELECT * FROM spielplan WHERE Team = '".$$mannschaft."' AND Spieldatum >= '".date("Y-m-d")."'  ORDER BY Spieldatum ASC LIMIT 0, 1";
$sql = "SELECT * FROM spielplan WHERE Spieldatum >= '".date("Y-m-d")."' GROUP BY Team ORDER BY Team, Spieldatum ASC";
$result = $db->query($sql);
$count = $db->num_rows();

if ($result!==false) {
    $i = 0;
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
        if ($i > 0) {
            if ($db->f("VereinsnummerA") == '10311') {
                $TeamnameA = 'FCL';
            }
            if ($db->f("VereinsnummerB") == '10311') {
                $TeamnameB = 'FCL';
            }
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
        $i++;
    }
}



$smarty = cSmartyFrontend::getInstance();
$smarty->assign('spieleLeft', $spieleLeft);
$smarty->assign('spieleRight', $spieleRight);
$smarty->assign('spieleKifu', $spieleKifu);
$smarty->assign('aktivTeams', $aktivTeams);
$smarty->assign('juniorenTeams', $juniorenTeams);
$smarty->assign('team1', $team1);
$smarty->assign('count', $count);
$smarty->display('get.tpl');
?>