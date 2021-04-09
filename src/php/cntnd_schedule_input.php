?><?php
// cntnd_schedule_input

// input/vars
$orig_orderLeft   = "CMS_VALUE[10]";
$orig_orderRight  = "CMS_VALUE[11]";
$orderLeft        = html_entity_decode($orig_orderLeft,ENT_QUOTES);
$orderRight       = html_entity_decode($orig_orderRight,ENT_QUOTES);

// includes
cInclude('module', 'includes/script.cntnd_schedule_input.php');
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
// custom teams
$sql = "SELECT DISTINCT Team FROM spielplan_kifu ORDER BY Team";
$ret = $conDb->query($sql);
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
<div class="form-vertical">
    <div class="form-group">
        <div class="form-check form-check-inline">
            <input id="activate_module" class="form-check-input" type="checkbox" name="CMS_VAR[3]" value="true" <?php if("CMS_VALUE[3]"=='true'){ echo 'checked'; } ?> />
            <label for="activate_module" class="form-check-label"><?= mi18n("ACTIVATE_MODULE") ?></label>
        </div>
    </div>

    <div class="form-group">
        <label for="vereinsname"><?= mi18n("VEREINSNAME") ?></label>
        <input id="vereinsname" type="text" name="CMS_VAR[4]" value="CMS_VALUE[4]" />
    </div>

    <div class="form-group">
        <label for="vereinsnummer"><?= mi18n("VEREINSNUMMER") ?></label>
        <input id="vereinsnummer" type="text" name="CMS_VAR[5]" value="CMS_VALUE[5]" />
    </div>

    <button class="btn btn-sm">Modul Zurücksetzen</button>
</div>

<hr />

<div class="d-flex" style="width: 1000px;">
    <div class="w-33 config-container">
        <div class="card">
            <div class="card-body">
                <div class="form-group">
                    <label for="newTeamText">Neues Team</label>
                    <input type="text" class="form-control form-control-sm" id="newTeamText" placeholder="Neues Team" data-bind="value: newTeamText" />
                </div>
                <button data-bind="click: addTeam" class="btn btn-sm btn-primary">Hinzufügen</button>
                <button data-bind="click: resetTeams" class="btn btn-sm">Zurücksetzen</button>
                <button data-bind="click: eraseTeams" class="btn btn-sm btn-light">Löschen</button>
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
                        <div class="form-group">
                            <select data-bind="options: $root.availableTeams, value: team, optionsValue: 'team', optionsCaption: '-Bitte Team auswählen-', optionsText: 'team'"></select>
                        </div>

                        <div class="form-group">
                            <label for="team">Name</label>
                            <input type="text" class="form-control form-control-sm" id="team" placeholder="Name" data-bind="value: name"  />
                        </div>

                        <div class="form-group">
                            <label for="url">URL</label>
                            <input type="text" class="form-control form-control-sm" id="url" placeholder="URL" data-bind="value: url" />
                        </div>

                        <div class="form-group">
                            <div class=" form-check form-check-inline">
                                <input class="form-check-input" type="radio" name="firstTeam" value="true" data-bind="checked: firstTeam">
                                <label class="form-check-label" for="firstTeam">1. Mannschaft</label>
                            </div>
                        </div>

                        <button class="btn btn-sm btn-light" data-bind="click: $parent.removeTeam">Löschen</button>
                    </div>
                </div>
            </div>
        </div>

        <div data-bind="foreach: teamsRight">
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
                        <a href="#" class="btn btn-sm btn-light" data-bind="click: $parent.removeTeam">Löschen</a>
                    </div>
                </div>
            </div>
        </div>

    </div>

    <div class="w-33">
        <p class="col-title">Aktiv Teams<p>
        <ul class="card sortable list-group" id="sortable-left" data-bind="sortable: { data: teamsLeft, afterMove: myDropCallback }">
            <li class="list-group-item">Team: <strong data-bind="text: name"></strong> <span data-bind="text: team"></span></li>
        </ul>
    </div>

    <div class="w-33">
        <p class="col-title">Junioren Teams (i18n!!)<p>
        <ul class="card sortable list-group" id="sortable-right" data-bind="sortable: { data: teamsRight, afterMove: myDropCallback }">
            <li class="list-group-item">Team: <strong data-bind="text: name"></strong> <span data-bind="text: team"></span></li>
        </ul>
    </div>
</div>

<!-- data for Contenido -->
<input type="text" name="CMS_VAR[10]" id="orderLeft" value="<?php echo $orig_orderLeft; ?>" data-bind="value: $root.saveTeamsLeft()" />
<input type="text" name="CMS_VAR[11]" id="orderRight" value="<?php echo $orig_orderRight; ?>" data-bind="value: $root.saveTeamsRight()" />
<?php