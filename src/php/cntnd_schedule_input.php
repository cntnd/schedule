?>
<script type='text/javascript' src='https://cdnjs.cloudflare.com/ajax/libs/knockout/3.4.2/knockout-min.js'></script>
<script src="https://cdn.jsdelivr.net/npm/lodash@4.17.10/lodash.min.js" integrity="sha256-/GKyJ0BQJD8c8UYgf7ziBrs/QgcikS7Fv/SaArgBcEI=" crossorigin="anonymous"></script>
<script src="https://cdn.jsdelivr.net/npm/sortablejs@1.6.1/Sortable.min.js"></script>
<link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.1.1/css/bootstrap.min.css"
      integrity="sha384-WskhaSGFgHYWDcbwN70/dfYBj47jz9qbsMId/iRN3ewGhXQFZCSftd1LZCfmhktB" crossorigin="anonymous">
<?php
// todo
// - collapsable

// cntnd_schedule_output
$orig_orderLeft   = "CMS_VALUE[10]";
$orig_orderRight  = "CMS_VALUE[11]";
$orderLeft        = html_entity_decode($orig_orderLeft,ENT_QUOTES);
$orderRight       = html_entity_decode($orig_orderRight,ENT_QUOTES);

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
while ($conDb->next_record()) {
    $scheduleTeams = $scheduleTeams.'{team:"'.$conDb->f('Team').'",side:"left"},';
}
$scheduleTeams = '['.substr($scheduleTeams,0,-1).']';

$teamsLeftJson='[]';
if ($util->isJson($orderLeft)){
    $teamsLeftJson = $orderLeft;
}
$teamsRightJson='[]';
if ($util->isJson($orderRight)){
    $teamsRightJson = $orderRight;
}

// JS Vars
echo '<script language="javascript" type="text/javascript">';
echo 'var teamsLeftJson='.$teamsLeftJson.';'."\n";
echo 'var teamsRightJson='.$teamsRightJson.';'."\n";
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

        <div data-bind="foreach: teamsLeft">
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
    echo '<div class="col-sm">';
    echo '<p class="col-title">Linke Seite<p>';
    echo '<ul class="card sortable list-group" id="sortable-left" data-bind="sortable: { data: teamsLeft, afterMove: myDropCallback }">';
    echo '<li class="list-group-item">Team: <strong data-bind="text: name"></strong> <span data-bind="text: team"></span> (<span data-bind="text: side"></span>)</li>';

    //echo '<li class="list-group-item" data-id="' . $team . '"><strong>Team: ' . $team . '</strong><i class="js-remove">✖</i></li>';

    echo '</ul>';
    echo '</div>';

    echo '<div class="col-sm">';
    echo '<p class="col-title">Rechte Seite<p>';
    echo '<ul class="card sortable list-group" id="sortable-right" data-bind="sortable: { data: teamsRight, afterMove: myDropCallback }">';
    echo '<li class="list-group-item">Team: <strong data-bind="text: name"></strong> <span data-bind="text: team"></span> (<span data-bind="text: side"></span>)</li>';
    echo '</ul>';
    echo '</div>';
    ?>
</div>

<!-- data for Contenido -->
<input type="text" name="CMS_VAR[10]" id="orderLeft" value="<?php echo $orig_orderLeft; ?>" data-bind="value: $root.saveTeamsLeft()" />
<input type="text" name="CMS_VAR[11]" id="orderRight" value="<?php echo $orig_orderRight; ?>" data-bind="value: $root.saveTeamsRight()" />
<?php