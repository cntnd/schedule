<?php
/**
 * cntnd_schedule Class
 */

class CntndSchedule {

    private $db;

    private $tables;
    private $dateRanges;
    private $vereinsname;
    private $vereinsnummer;
    private $hasCustomTeams;

    private $orderBlockOne;
    private $orderBlockTwo;
    private $orderBlockThree;

    function __construct(array $tables, array $dateRanges, string $vereinsname, string $vereinsnummer, string $rawOrderBlockOne, string $rawOrderBlockTwo, string $rawOrderBlockThree, bool $hasCustomTeams) {
        setlocale(LC_ALL, 'de_DE@euro', 'de_DE', 'deu_deu');

        $this->db = new cDb;

        $this->tables = $tables;
        $this->dateRanges = $this->doDateRange($dateRanges);
        $this->vereinsname = $vereinsname;
        $this->vereinsnummer = $vereinsnummer;
        $this->hasCustomTeams = $hasCustomTeams;
        $this->orderBlockOne = json_decode(html_entity_decode($rawOrderBlockOne,ENT_QUOTES), true);
        $this->orderBlockTwo = json_decode(html_entity_decode($rawOrderBlockTwo,ENT_QUOTES), true);
        $this->orderBlockThree = json_decode(html_entity_decode($rawOrderBlockThree,ENT_QUOTES), true);
    }

    private function doDateRange(array $ranges) : array {
        $dateRanges = [];
        foreach ($ranges as $key => $range){
            if (is_numeric($range) && $range>0){
                $dateRanges[$key]=date("Y-m-d", strtotime("+".$range." days"));
            }
            else {
                $dateRanges[$key]="";
            }
        }
        return $dateRanges;
    }

    public function blockOne() : array {
        return $this->block($this->orderBlockOne, $this->dateRanges['dateRangeBlockOne']);
    }

    public function blockTwo() : array {
        return $this->block($this->orderBlockTwo, $this->dateRanges['dateRangeBlockTwo']);
    }

    public function blockThree() : array {
        return $this->block($this->orderBlockThree, $this->dateRanges['dateRangeBlockCustom']);
    }

    private function block(array $block, string  $dateRange = "") : array {
        $game = array();
        foreach ($block as $value){
            $data = array();
            $firstTeam = $value['firstTeam'] ? true : false;
            $home = $value['homeOnly'] ? true : false;
            $custom = false;
            if ($this->hasCustomTeams){
                $custom = $value['customTeam'] ? true : false;
            }

            if (!empty($value['team'])) {
                if (!$custom) {
                    $data = $this->game($value['team'], $value['name'], $firstTeam, $home, $dateRange);
                }
                else {
                    $data = $this->customGame($value['team'], $value['name'], $home, $dateRange);
                }
                $noData = false;
                if (count($data)==0){
                    $noData=true;
                }
                $data['data_team'] = $value['name'];
                $data['data_url'] = $value['url'];
                $data['noData'] = $noData;

                $game[] = $data;
            }
            else {
                $data['data_team'] = $value['name'];
                $data['data_url'] = $value['url'];
                $data['noData'] = true;

                $game[] = $data;
            }
        }
        return $game;
    }

    private function game(string $Team, string $CustomTeam, bool $firstTeam = false, bool $home = true, string  $dateRange = "") : array {
        $sql = $this->statement($home, false, !empty($dateRange));

        $values = array(
            "team" => $Team,
            "spieldatum" => date("Y-m-d"));

        if ($home){
            $values['vereinsnummer'] = $this->vereinsnummer;
        }

        if (!empty($dateRange)){
            $values['datum_bis']=$dateRange;
        }

        $this->db->query($sql, $values);
        $spiel = array();

        while ($this->db->next_record()) {
            // datum better
            $date = strtotime($this->db->f("Spieldatum")." ".$this->db->f("Spielzeit"));
            $TagKurz = self::get2CharDay(date('N', $date));
            $Spieldatum = date('d.m.Y', $date);
            $Spielzeit = date('H:i', $date);
            $spiel_datum = $TagKurz . " " . $Spieldatum;

            $TeamA = $this->db->f("TeamnameA");
            $TeamnameA = $this->db->f("TeamnameA");
            $TeamB = $this->db->f("TeamnameB");
            $TeamnameB = $this->db->f("TeamnameB");
            if ($this->db->f("VereinsnummerA") == $this->vereinsnummer) {
                $TeamA = $CustomTeam;
                $TeamnameA = $this->vereinsname;
            }
            if ($this->db->f("VereinsnummerB") == $this->vereinsnummer) {
                $TeamB = $CustomTeam;
                $TeamnameB = $this->vereinsname;
            }

            $spiel_typ = $this->db->f("SpielTyp");
            $SpielTyp = "";
            if ($spiel_typ == "Trainingsspiele") {
                $SpielTyp = "*";
            } else if ($spiel_typ == "Cup") {
                $SpielTyp = "(C)";
            }

            $spiel = array(
                'data_first_team' => $firstTeam,
                'data_custom_team' => false,
                'data_full_date' => strftime('%A, %e. %B %G', $date) . ' ' . $Spielzeit . ' Uhr',
                'data_datum' => $spiel_datum,
                'data_zeit' => $Spielzeit,
                'data_spiel_typ' => $SpielTyp,
                'Team' => $this->db->f("Team"),
                'SpielTyp' => $spiel_typ,
                'Spielstatus' => $this->db->f("Spielstatus"),
                'Bezeichnung' => $this->db->f("Bezeichnung"),
                'Spielnummer' => $this->db->f("Spielnummer"),
                'TagKurz' => $TagKurz,
                'Spieldatum' => $Spieldatum,
                'Spielzeit' => $Spielzeit,
                'TeamnameA' => $this->db->f("TeamnameA"),
                'TeamLigaA' => $this->db->f("TeamLigaA"),
                'VereinsnummerA' => $this->db->f("VereinsnummerA"),
                'TeamA' => $TeamnameA,
                'CustomTeamA' => $TeamA,
                'TeamnameB' => $this->db->f("TeamnameB"),
                'TeamLigaB' => $this->db->f("TeamLigaB"),
                'VereinsnummerB' => $this->db->f("VereinsnummerB"),
                'TeamB' => $TeamnameB,
                'CustomTeamB' => $TeamB,
                'Spielort' => $this->db->f("Spielort"),
                'Sportanlage' => $this->db->f("Sportanlage"),
                'Ort' => $this->db->f("Ort"),
                'Wettspielfeld' => $this->db->f("Wettspielfeld")
            );
        }

        return $spiel;
    }

    private function customGame(string $Team, string $CustomTeam, bool $home = true, string  $dateRange = "") : array {
        $sql = $this->statement($home, true, !empty($dateRange));

        $values = array(
            "team" => $Team,
            "spieldatum" => date("Y-m-d"));

        if ($home){
            $values['vereinsnummer'] = $this->vereinsnummer;
        }

        if (!empty($dateRange)){
            $values['datum_bis']=$dateRange;
        }

        $this->db->query($sql, $values);
        $spiel = array();

        while ($this->db->next_record()) {
            $date = strtotime($this->db->f("Spieldatum")." ".$this->db->f("Spielzeit"));
            $TagKurz = self::get2CharDay(date('N', $date));
            $Spieldatum = date('d.m.Y', $date);
            $Spielzeit = date('H:i', $date);
            $spiel_datum = $TagKurz . " " . $Spieldatum;

            $TeamA = $this->db->f("TeamnameA");
            $TeamnameA = $this->db->f("TeamnameA");
            $TeamB = $this->db->f("TeamnameB");
            $TeamnameB = $this->db->f("TeamnameB");
            if ($this->db->f("VereinsnummerA") == $this->vereinsnummer) {
                $TeamA = $CustomTeam;
                $TeamnameA = $this->vereinsname;
            }
            if ($this->db->f("VereinsnummerB") == $this->vereinsnummer) {
                $TeamB = $CustomTeam;
                $TeamnameB = $this->vereinsname;
            }

            $spiel_typ = $this->db->f("SpielTyp");
            $SpielTyp = "";
            if ($spiel_typ == "Trainingsspiele") {
                $SpielTyp = "*";
            } else if ($spiel_typ == "Cup") {
                $SpielTyp = "(C)";
            }

            $spiel = array(
                'data_first_team' => false,
                'data_custom_team' => true,
                'data_full_date' => strftime('%A, %e. %B %G', $date) . ' ' . $Spielzeit . ' Uhr',
                'data_datum' => $spiel_datum,
                'data_zeit' => $Spielzeit,
                'data_ort' => $this->db->f("Spielort"),
                'data_spiel_typ' => $SpielTyp,
                'Team' => $this->db->f("Team"),
                'SpielTyp' => $spiel_typ,
                'Spielstatus' => $this->db->f("Spielstatus"),
                'Bezeichnung' => $this->db->f("Bezeichnung"),
                'Spielnummer' => $this->db->f("Spielnummer"),
                'TagKurz' => $TagKurz,
                'Spieldatum' => $Spieldatum,
                'Spielzeit' => $Spielzeit,
                'TeamnameA' => $this->db->f("TeamnameA"),
                'TeamLigaA' => $this->db->f("TeamLigaA"),
                'VereinsnummerA' => $this->db->f("VereinsnummerA"),
                'TeamA' => $TeamnameA,
                'CustomTeamA' => $TeamA,
                'TeamnameB' => $this->db->f("TeamnameB"),
                'TeamLigaB' => $this->db->f("TeamLigaB"),
                'VereinsnummerB' => $this->db->f("VereinsnummerB"),
                'TeamB' => $TeamnameB,
                'CustomTeamB' => $TeamB,
                'Spielort' => $this->db->f("Spielort"),
                'Sportanlage' => $this->db->f("Sportanlage"),
                'Ort' => $this->db->f("Ort"),
                'Wettspielfeld' => $this->db->f("Wettspielfeld"),
                'bemerkungen' => $this->db->f("bemerkungen")
            );
        }

        return $spiel;
    }

    private function statement(bool $home, bool $isCustom, bool $dateRange = false) : string {
        $table = $this->tables['default'];
        if ($isCustom){
            $table = $this->tables['custom'];
        }

        $sql = "SELECT * FROM ".$table." WHERE Team = ':team' AND Spieldatum >= ':spieldatum' ";

        if ($dateRange){
            $sql .= " AND Spieldatum < ':datum_bis' ";
        }

        if ($home && !$isCustom){
            $sql .= " AND VereinsnummerA = ':vereinsnummer' ";
        }

        $sql .= " AND Spielstatus IS NULL ORDER BY Spieldatum ASC LIMIT 0, 1";

        return $sql;
    }

    private static function get2CharDay($N){
        switch ($N){
            case '1':
                return "Mo";
                break;
            case '2':
                return "Di";
                break;
            case '3':
                return "Mi";
                break;
            case '4':
                return "Do";
                break;
            case '5':
                return "Fr";
                break;
            case '6':
                return "Sa";
                break;
            default:
                return "So";
        }
    }
}