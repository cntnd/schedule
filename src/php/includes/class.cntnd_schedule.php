<?php
/**
 * cntnd_schedule Class
 */

class CntndSchedule {

    private $db;

    private $vereinsname;
    private $vereinsnummer;
    private $orderBlockOne;
    private $orderBlockTwo;

    function __construct(string $vereinsname, string $vereinsnummer, string $rawOrderBlockOne, string $rawOrderBlockTwo) {
        setlocale(LC_ALL, 'de_DE@euro', 'de_DE', 'deu_deu');

        $this->db = new cDb;

        $this->vereinsname = $vereinsname;
        $this->vereinsnummer = $vereinsnummer;
        $this->orderBlockOne = json_decode(html_entity_decode($rawOrderBlockOne,ENT_QUOTES), true);;
        $this->orderBlockTwo = json_decode(html_entity_decode($rawOrderBlockTwo,ENT_QUOTES), true);;
    }

    public function blockOne() : array {
        return $this->block($this->orderBlockOne);
    }

    public function blockTwo() : array {
        return $this->block($this->orderBlockTwo);
    }

    private function block(array $block) : array {
        $game = array();
        foreach ($block as $value){
            $data = array();
            $home = $value['firstTeam'] ? false : true;

            // todo when there is no team!!
            if (!empty($value['team'])) {
                $data[$value['team']] = $this->game($value['team'], $value['name'], $home);
                $data[$value['team']]['data_team'] = $value['name'];
                $data[$value['team']]['data_url'] = $value['url'];
                $data[$value['team']]['noData'] = "false";

                $game[] = $data[$value['team']];
            }
        }
        return $game;
    }

    private function game(string $Team, string $CustomTeam, bool $home = true) : array {
        if ($home){
            $sql = "SELECT * FROM spielplan WHERE Team = ':team' AND VereinsnummerA = ':vereinsnummer' AND Spieldatum >= ':spieldatum' AND Spielstatus IS NULL ORDER BY Spieldatum ASC LIMIT 0, 1";
            $values = array(
                "team" => $Team,
                "vereinsnummer" => $this->vereinsnummer,
                "spieldatum" => date("Y-m-d"));
        }
        else {
            $sql = "SELECT * FROM spielplan WHERE Team = ':team' AND Spieldatum >= ':spieldatum' AND Spielstatus IS NULL ORDER BY Spieldatum ASC LIMIT 0, 1";
            $values = array(
                "team" => $Team,
                "spieldatum" => date("Y-m-d"));
        }
        $this->db->query($sql, $values);
        $spiel = array();

        while ($this->db->next_record()) {
            // datum better
            $TagKurz = $this->db->f("TagKurz");
            $Spieldatum = $this->db->f("Spieldatum");
            $Spielzeit = $this->db->f("Spielzeit");
            $spiel_datum = $TagKurz . " " . date('d.m.Y', strtotime($Spieldatum));
            $spiel_zeit = '';
            if (!empty($Spielzeit) and $Spielzeit != "00:00:00") {
                $spiel_zeit = date('H:i', strtotime($Spielzeit));
            }

            $TeamA = $this->db->f("TeamnameA");
            $TeamnameA = $this->db->f("TeamnameA");
            $TeamB = $this->db->f("TeamnameB");
            $TeamnameB = $this->db->f("TeamnameB");
            if ($this->db->f("VereinsnummerA") == '10330') {
                $TeamA = $CustomTeam;
                $TeamnameA = $this->vereinsname;
            }
            if ($this->db->f("VereinsnummerB") == '10330') {
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
                'Team' => $this->db->f("Team"),
                'data_first_team' => !$home,
                'data_full_date' => strftime('%A, %e. %B %G', strtotime($Spieldatum)) . ' ' . $spiel_zeit . ' Uhr',
                'data_datum' => $spiel_datum,
                'data_zeit' => $spiel_zeit,
                'data_ort' => $this->db->f("Spielort"),
                'Spielort' => $this->db->f("Spielort"),
                'SpielTyp' => $SpielTyp,
                'Bezeichnung' => $this->db->f("Bezeichnung"),
                'TagKurz' => $TagKurz,
                'Spieldatum' => $Spieldatum,
                'Spielzeit' => $this->db->f("Spielzeit"),
                'Spielnummer' => $this->db->f("Spielnummer"),
                'TeamA' => $TeamnameA,
                'CustomTeamA' => $TeamA,
                'TeamnameA' => $this->db->f("TeamnameA"),
                'VereinsnummerA' => $this->db->f("VereinsnummerA"),
                'TeamB' => $TeamnameB,
                'CustomTeamB' => $TeamB,
                'TeamnameB' => $this->db->f("TeamnameB"),
                'VereinsnummerB' => $this->db->f("VereinsnummerB")
            );
        }

        return $spiel;
    }
}