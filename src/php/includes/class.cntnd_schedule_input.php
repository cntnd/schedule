<?php

class CntndScheduleInput
{

    private $db;
    private $tables;
    private $hasCustomTeams;

    function __construct(array $tables, bool $hasCustomTeams, int $client)
    {
        setlocale(LC_ALL, 'de_DE@euro', 'de_DE', 'deu_deu');

        $this->db = new cDb;
        $this->tables = $tables;
        $this->hasCustomTeams = $hasCustomTeams;
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
                $scheduleTeams = $scheduleTeams . '{team:"' . $team . '",label:"KiFu: ' . $team . '",side:"one",customTeam:true},';
            }
        }
        $scheduleTeams = '[' . substr($scheduleTeams, 0, -1) . ']';

        return $scheduleTeams;
    }

    private function customTeams(): array
    {
        $customTeams = array();
        $sql = "SELECT DISTINCT Team FROM " . $this->tables['custom'] . " ORDER BY Team";
        $this->db->query($sql);
        while ($this->db->next_record()) {
            $customTeams[] = $this->db->f('Team');
        }
        return $customTeams;
    }
}