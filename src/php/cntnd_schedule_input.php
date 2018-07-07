?>
<script type='text/javascript' src='https://cdnjs.cloudflare.com/ajax/libs/knockout/3.4.2/knockout-min.js'></script>
<script src="https://cdn.jsdelivr.net/npm/sortablejs@1.6.1/Sortable.min.js"></script>
<link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.1.1/css/bootstrap.min.css"
      integrity="sha384-WskhaSGFgHYWDcbwN70/dfYBj47jz9qbsMId/iRN3ewGhXQFZCSftd1LZCfmhktB" crossorigin="anonymous">
<?php
// todo
// - collapsable
// - sorting
// - add teams without scheduleTeam to left/right sortable
// - delete sorting and/or element in sorting

// cntnd_schedule_output
$orig_orderLeft   = "CMS_VALUE[10]";
$orig_orderRight  = "CMS_VALUE[11]";
$orig_teams       = "CMS_VALUE[12]";
$moduleActive     = "CMS_VALUE[13]";

// includes
cInclude('module', 'includes/class.cntndutil.php');

// classes
$conDb = cRegistry::getDb();
$util = new CntndUtil();
$module = new cModuleHandler($cCurrentModule);
$absolutePath = $module->getModulePath();

// init all teams
$sql = "SELECT DISTINCT Team FROM spielplan ORDER BY Team";
$ret = $conDb->query($sql);
$scheduleTeams='';
$teamsJson='';
while ($conDb->next_record()) {
    $teams[$conDb->f('Team')] = $conDb->f('Team');
    $scheduleTeams = $scheduleTeams.'{team:"'.$conDb->f('Team').'"},';
}
$scheduleTeams = '['.substr($scheduleTeams,0,-1).']';
$teamsJson=$scheduleTeams;
if (!empty($orig_teams) && $util->isJson(json_decode($orig_teams))){
    $teamsJson = html_entity_decode($orig_teams,ENT_QUOTES);
}

// init order
$orderLeft   = explode("|", $orig_orderLeft);
$orderRight  = explode("|", $orig_orderRight);

// JS Vars
echo '<script language="javascript" type="text/javascript">';
echo 'var teamJson='.$teamsJson.';'."\n";
echo 'var scheduleTeams = '.$scheduleTeams.';'."\n";
echo '</script>';

// CSS
$cssFiles = $module->getAllFilesFromDirectory('css');
$util->getAllCss($absolutePath, $cssFiles);
// JS
$jsFiles = $module->getAllFilesFromDirectory('js');
$util->getAllJs($absolutePath, $jsFiles);

?>
<div class="row">
    <div class="col-sm config-container">
        <div class="card">
            <div class="card-body">
                <div class="form-group">
                    <label for="newTeamText">Neues Team</label>
                    <input type="text" class="form-control form-control-sm" id="newTeamText" placeholder="Neues Team" data-bind="value: newTeamText" />
                </div>
                <a href="#" data-bind="click: addTeam" class="btn btn-sm">Hinzufügen</a>
                <a href="#" data-bind="click: resetTeams" class="btn btn-sm btn-warning">Zurücksetzen</a>
                <a href="#" data-bind="click: eraseTeams" class="btn btn-sm btn-danger">Löschen</a>
            </div>
        </div>

        <div data-bind="foreach: teams">
            <div class="card">
                <div class="card-body">
                    <strong>
                        Team <span data-bind="text: name"></span>
                        <!-- <span class="expand-button">expand</span> -->
                    </strong>
                    <div class="expand">
                        <select class="form-control form-control-sm" data-bind="options: $root.availableTeams, value: team, optionsValue: 'team', optionsCaption: '-Bitte Team auswählen-', optionsText: 'team'"></select>
                        <div class="form-group">
                            <label for="team">Name</label>
                            <input type="text" class="form-control form-control-sm" id="team" placeholder="Name" data-bind="value: name"  />
                        </div>
                        <div class="form-group">
                            <label for="url">URL</label>
                            <input type="text" class="form-control form-control-sm" id="url" placeholder="URL" data-bind="value: url" />
                        </div>
                        <a href="#" class="btn btn-sm btn-warning" data-bind="click: $parent.removeTeam">Löschen</a>
                    </div>
                </div>
            </div>
        </div>

    </div>
    <?php

    $configTeams = json_decode(html_entity_decode($orig_teams,ENT_QUOTES), true);

    echo '<div class="col-sm">';
    echo '<p class="col-title">Linke Seite<p>';
    echo '<ul class="card sortable list-group" id="sortable-left">';
    foreach($orderLeft as $team) {
        if (!empty($team)) {
            $obsolet = '';
            if (!array_key_exists($team, $teams)) {
                $obsolet = ' (obsolet)';
            }
            echo '<li class="list-group-item" data-id="' . $team . '"><strong>Team: ' . $team . $obsolet . '</strong><i class="js-remove">✖</i></li>';
        }
    }
    foreach($configTeams as $configTeam) {
        if (!array_key_exists($configTeam['team'],$teams) &&
            !in_array($configTeam['name'],$orderLeft) &&
            !in_array($configTeam['name'],$orderRight)){
            echo '<li class="list-group-item" data-id="' . $configTeam['name'] . '"><strong>Team: '.$configTeam['name'].'</strong><i class="js-remove">✖</i></li>';
        }
    }
    foreach($teams as $team) {
        if (!in_array($team,$orderLeft) &&
            !in_array($team,$orderRight)) {
            echo '<li class="list-group-item" data-id="' . $team . '"><strong>Team: ' . $team . '</strong><i class="js-remove">✖</i></li>';
        }
    }
    echo '</ul>';
    echo '</div>';

    echo '<div class="col-sm">';
    echo '<p class="col-title">Rechte Seite<p>';
    echo '<ul class="card sortable list-group" id="sortable-right">';
    foreach($orderRight as $team){
        if (!empty($team)) {
            $obsolet = '';
            if (!array_key_exists($team, $teams)) {
                $obsolet = ' (obsolet)';
            }
            echo '<li class="list-group-item" data-id="' . $team . '"><strong>Team: ' . $team . $obsolet . '</strong><i class="js-remove">✖</i></li>';
        }
    }
    echo '</ul>';
    echo '</div>';
    ?>
</div>
<!-- data for Contenido -->
<input type="text" name="CMS_VAR[10]" id="orderLeft" value="<?php echo $orig_orderLeft; ?>" />
<input type="text" name="CMS_VAR[11]" id="orderRight" value="<?php echo $orig_orderRight; ?>" />
<input type="hidden" name="CMS_VAR[12]" id="teams" value="<?php echo $orig_teams; ?>" data-bind="value: $root.saveTeams()" />
<?php