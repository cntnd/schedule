<?php

class CntndScheduleInput
{

    private $db;
    private $tables;
    private $hasCustomTeams;
    private $hasCustomCSV;
    private $customCSVFile;
    private $separator;
    private $csvFiles;

    function __construct(array $tables, bool $hasCustomTeams, bool $hasCustomCSV, string $customCSVFile, string $separator, int $client)
    {
        setlocale(LC_ALL, 'de_DE@euro', 'de_DE', 'deu_deu');

        $this->db = new cDb;
        $this->tables = $tables;
        $this->hasCustomTeams = $hasCustomTeams;
        $this->hasCustomCSV = $hasCustomCSV;
        $this->customCSVFile = $customCSVFile;
        $this->separator = $separator;
        $this->csvFiles = $this->csvFiles($client);
    }

    private function csvFiles(int $client): array
    {
        $cfg = cRegistry::getConfig();
        $cfgClient = cRegistry::getClientConfig();
        $uploadDir = $cfgClient[$client]['upl']['path'];
        $files = array();

        $sql = "SELECT * FROM :table WHERE idclient=:idclient AND filetype IN ('csv') ORDER BY dirname ASC, filename ASC";
        $values = array(
            'table' => $cfg['tab']['upl'],
            'idclient' => cSecurity::toInteger($client)
        );
        $this->db->query($sql, $values);
        while ($this->db->nextRecord()) {
            $files[$this->db->f('idupl')] = array('filename' => $this->db->f('dirname') . $this->db->f('filename'), 'filepath' => $uploadDir . $this->db->f('dirname') . $this->db->f('filename'));
        }
        return $files;
    }

    public function customTeamsCSVFiles() : array {
        return $this->csvFiles;
    }

    public function scheduleTeams(): string
    {
        $sql = "SELECT DISTINCT Team FROM " . $this->tables['default'] . " ORDER BY Team";
        $this->db->query($sql);
        $scheduleTeams = '';
        while ($this->db->next_record()) {
            $scheduleTeams = $scheduleTeams . '{team:"' . $this->db->f('Team') . '",label:"' . $this->db->f('Team') . '",side:"one"},';
        }
        // custom teams
        if ($this->hasCustomTeams) {
            $customTeams = $this->customTeams();
            foreach ($customTeams as $team) {
                $scheduleTeams = $scheduleTeams . '{team:"' . $team . '",label:"KiFu: '.$team.'",side:"one",customTeam:true},';
            }
        }
        $scheduleTeams = '[' . substr($scheduleTeams, 0, -1) . ']';

        return $scheduleTeams;
    }

    private function customTeams() : array {
        $customTeams=array();
        $sql = "SELECT DISTINCT Team FROM " . $this->tables['custom'] . " ORDER BY Team";
        $this->db->query($sql);
        while ($this->db->next_record()) {
            $customTeams[] = $this->db->f('Team');
        }
        return $customTeams;
    }

    private function loadCSVCustomTeams() {
        $i=0;
        foreach ($this->loadRows() as $row) {
            if ($i>0) {
                $headers = str_getcsv($row, $this->separator);
            }
            $i++;
        }
    }

    private function loadRows() : array {
        $file = $this->loadFile();
        return str_getcsv($file,"\n");
    }

    private function loadFile() : string {
        $file = file_get_contents($this->customCSVFile, FILE_USE_INCLUDE_PATH);
        if (!self::isUTF8($file)){
            return utf8_encode($file);
        }
        return $file;
    }

    private static function isUTF8(string $string) : bool {
        return mb_detect_encoding($string, 'UTF-8', true);
    }
}