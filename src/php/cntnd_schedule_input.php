?><?php
// cntnd_schedule_input

// input/vars
$hasCustomTeams   = "CMS_VALUE[9]";
if (!is_bool($hasCustomTeams)){
    $hasCustomTeams=false;
}
$orig_blockOne   = "CMS_VALUE[10]";
$orig_blockTwo  = "CMS_VALUE[11]";
$orig_blockThree  = "CMS_VALUE[12]";
$blockOne        = html_entity_decode($orig_blockOne,ENT_QUOTES);
$blockTwo       = html_entity_decode($orig_blockTwo,ENT_QUOTES);
$blockThree       = html_entity_decode($orig_blockThree,ENT_QUOTES);
$tables = array(
    "default" => "spielplan",
    "custom" => "spielplan_kifu");

// includes
cInclude('module', 'includes/script.cntnd_schedule_input.php');
cInclude('module', 'includes/class.cntndutil.php');

// classes
$conDb = new cDb;
$util = new CntndUtilLegacy();
$module = new cModuleHandler($cCurrentModule);
$absolutePath = $module->getModulePath();

// init all teams
$sql = "SELECT DISTINCT Team FROM ".$tables['default']." ORDER BY Team";
$ret = $conDb->query($sql);
$scheduleTeams='';
while ($conDb->next_record()) {
    $scheduleTeams = $scheduleTeams.'{team:"'.$conDb->f('Team').'",side:"one"},';
}
// custom teams
$customTeams = array();
if ($hasCustomTeams) {
    $sql = "SELECT DISTINCT Team FROM " . $tables['custom'] . " ORDER BY Team";
    $ret = $conDb->query($sql);
    while ($conDb->next_record()) {
        $team = $conDb->f('Team');
        $scheduleTeams = $scheduleTeams . '{team:"' . $team . '",side:"one",customTeam:true},';
        $customTeams[] = $team;
    }
}

$scheduleTeams = '[' . substr($scheduleTeams, 0, -1) . ']';

$teamsBlockOne='[]';
if ($util->isJson($blockOne)){
    $teamsBlockOne = $blockOne;
}
$teamsBlockTwo='[]';
if ($util->isJson($blockTwo)){
    $teamsBlockTwo = $blockTwo;
}
$teamsBlockThree='[]';
if ($util->isJson($blockThree)){
    $teamsBlockThree = $blockThree;
}

// JS Vars
echo '<script language="javascript" type="text/javascript">';
echo 'var teamsBlockOne='.$teamsBlockOne.';'."\n";
echo 'var teamsBlockTwo='.$teamsBlockTwo.';'."\n";
echo 'var teamsBlockThree='.$teamsBlockThree.';'."\n";
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

    <div class="form-group">
        <div class="form-check form-check-inline">
            <input id="activate_module" class="form-check-input" type="checkbox" name="CMS_VAR[9]" value="true" <?php if("CMS_VALUE[9]"=='true'){ echo 'checked'; } ?> />
            <label for="activate_module" class="form-check-label"><?= mi18n("ACTIVATE_CUSTOM_TEAMS") ?></label>
        </div>
    </div>

    <hr />

    <div class="form-group">
        <label for="daterange_block_one"><?= mi18n("DATERANGE_BLOCK_ONE") ?></label>
        <input id="daterange_block_one" type="number" name="CMS_VAR[6]" value="CMS_VALUE[6]" />
        <small><?= mi18n("DATERANGE_HELP") ?></small>
    </div>

    <div class="form-group">
        <label for="daterange_block_two"><?= mi18n("DATERANGE_BLOCK_TWO") ?></label>
        <input id="daterange_block_two" type="number" name="CMS_VAR[7]" value="CMS_VALUE[7]" />
        <small><?= mi18n("DATERANGE_HELP") ?></small>
    </div>

    <div class="form-group">
        <label for="daterange_block_custom"><?= mi18n("DATERANGE_BLOCK_CUSTOM") ?></label>
        <input id="daterange_block_custom" type="number" name="CMS_VAR[8]" value="CMS_VALUE[8]" />
        <small><?= mi18n("DATERANGE_HELP") ?></small>
    </div>

    <button data-bind="click: eraseTeams" class="btn btn-light" type="submit"><?= mi18n("MODULE_RESET") ?></button>
</div>

<hr />

<div class="d-flex" style="width: 1000px;">
    <div class="w-25 config-container">
        <div class="card">
            <div class="card-body">
                <div class="form-group">
                    <label for="newTeamText"><?= mi18n("NEW_TEAM") ?></label>
                    <input type="text" class="form-control form-control-sm" id="newTeamText" placeholder="<?= mi18n("NEW_TEAM") ?>" data-bind="value: newTeamText" />
                </div>
                <button data-bind="click: addTeam" class="btn btn-sm btn-primary"><?= mi18n("ADD") ?></button>
                <button data-bind="click: resetTeams" class="btn btn-sm"><?= mi18n("RESET") ?></button>
            </div>
        </div>

        <div data-bind="foreach: blockOne">
            <div class="card">
                <div class="card-body">
                    <strong>
                        Team <span data-bind="text: name"></span>
                    </strong>
                    <div class="expand">
                        <div class="form-group">
                            <select data-bind="options: $root.availableTeams, value: team, optionsValue: 'team', optionsCaption: '<?= mi18n("CHOOSE_TEAM") ?>', optionsText: 'team'"></select>
                        </div>

                        <div class="form-group">
                            <label for="team"><?= mi18n("TEAM_NAME") ?></label>
                            <input type="text" class="form-control form-control-sm" id="team" placeholder="<?= mi18n("TEAM_NAME") ?>" data-bind="value: name"  />
                        </div>

                        <div class="form-group">
                            <label for="url"><?= mi18n("TEAM_URL") ?></label>
                            <input type="text" class="form-control form-control-sm" id="url" placeholder="<?= mi18n("TEAM_URL") ?>" data-bind="value: url" />
                        </div>
                        <div class="form-group">
                            <div class=" form-check form-check-inline">
                                <input class="form-check-input" type="checkbox" name="homeOnly" value="true" data-bind="checked: homeOnly">
                                <label class="form-check-label" for="homeOnly"><?= mi18n("TEAM_HOME_ONLY") ?></label>
                            </div>
                        </div>
                        <div class="form-group">
                            <div class=" form-check form-check-inline">
                                <input class="form-check-input" type="checkbox" name="firstTeam" value="true" data-bind="checked: firstTeam">
                                <label class="form-check-label" for="firstTeam"><?= mi18n("FIRST_TEAM") ?></label>
                            </div>
                        </div>
                        <button class="btn btn-sm btn-light" data-bind="click: $parent.removeTeamOne"><?= mi18n("REMOVE") ?></button>
                    </div>
                </div>
            </div>
        </div>

        <div data-bind="foreach: blockTwo">
            <div class="card">
                <div class="card-body">
                    <strong>
                        Team <span data-bind="text: name"></span>
                        <!-- <span class="expand-button">expand</span> -->
                    </strong>
                    <div class="expand">
                        <select class="form-control form-control-sm" data-bind="options: $root.availableTeams, value: team, optionsValue: 'team', optionsCaption: '-Bitte Team auswählen-', optionsText: 'team'"></select>
                        <div class="form-group">
                            <label for="team"><?= mi18n("TEAM_NAME") ?></label>
                            <input type="text" class="form-control form-control-sm" id="team" placeholder="<?= mi18n("TEAM_NAME") ?>" data-bind="value: name"  />
                        </div>
                        <div class="form-group">
                            <label for="url"><?= mi18n("TEAM_URL") ?></label>
                            <input type="text" class="form-control form-control-sm" id="url" placeholder="<?= mi18n("TEAM_URL") ?>" data-bind="value: url" />
                        </div>
                        <div class="form-group">
                            <div class=" form-check form-check-inline">
                                <input class="form-check-input" type="checkbox" name="homeOnly" value="true" data-bind="checked: homeOnly">
                                <label class="form-check-label" for="homeOnly"><?= mi18n("TEAM_HOME_ONLY") ?></label>
                            </div>
                        </div>
                        <div class="form-group">
                            <div class=" form-check form-check-inline">
                                <input class="form-check-input" type="checkbox" name="customTeam" value="true" data-bind="checked: customTeam">
                                <label class="form-check-label" for="customTeam"><?= mi18n("CUSTOM_TEAM") ?></label>
                            </div>
                        </div>
                        <a href="#" class="btn btn-sm btn-light" data-bind="click: $parent.removeTeamTwo"><?= mi18n("REMOVE") ?></a>
                    </div>
                </div>
            </div>
        </div>

        <div data-bind="foreach: blockThree">
            <div class="card">
                <div class="card-body">
                    <strong>
                        Team <span data-bind="text: name"></span>
                        <!-- <span class="expand-button">expand</span> -->
                    </strong>
                    <div class="expand">
                        <select class="form-control form-control-sm" data-bind="options: $root.availableTeams, value: team, optionsValue: 'team', optionsCaption: '-Bitte Team auswählen-', optionsText: 'team'"></select>
                        <div class="form-group">
                            <label for="team"><?= mi18n("TEAM_NAME") ?></label>
                            <input type="text" class="form-control form-control-sm" id="team" placeholder="<?= mi18n("TEAM_NAME") ?>" data-bind="value: name"  />
                        </div>
                        <div class="form-group">
                            <label for="url"><?= mi18n("TEAM_URL") ?></label>
                            <input type="text" class="form-control form-control-sm" id="url" placeholder="<?= mi18n("TEAM_URL") ?>" data-bind="value: url" />
                        </div>
                        <div class="form-group">
                            <div class=" form-check form-check-inline">
                                <input class="form-check-input" type="checkbox" name="homeOnly" value="true" data-bind="checked: homeOnly">
                                <label class="form-check-label" for="homeOnly"><?= mi18n("TEAM_HOME_ONLY") ?></label>
                            </div>
                        </div>
                        <div class="form-group">
                            <div class=" form-check form-check-inline">
                                <input class="form-check-input" type="checkbox" name="customTeam" value="true" data-bind="checked: customTeam">
                                <label class="form-check-label" for="customTeam"><?= mi18n("CUSTOM_TEAM") ?></label>
                            </div>
                        </div>
                        <a href="#" class="btn btn-sm btn-light" data-bind="click: $parent.removeTeamThree"><?= mi18n("REMOVE") ?></a>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="w-25">
        <p class="col-title"><?= mi18n("BLOCK_ONE_TITLE") ?><p>
        <ul class="card sortable list-group" id="sortable-left" data-bind="sortable: { data: blockOne, afterMove: myDropCallback }">
            <li class="list-group-item">Team: <strong data-bind="text: name"></strong> <span data-bind="text: team"></span></li>
        </ul>
    </div>

    <div class="w-25">
        <p class="col-title"><?= mi18n("BLOCK_TWO_TITLE") ?><p>
        <ul class="card sortable list-group" id="sortable-right" data-bind="sortable: { data: blockTwo, afterMove: myDropCallback }">
            <li class="list-group-item">Team: <strong data-bind="text: name"></strong> <span data-bind="text: team"></span></li>
        </ul>
    </div>

    <div class="w-25">
        <p class="col-title"><?= mi18n("BLOCK_THREE_TITLE") ?><p>
        <ul class="card sortable list-group" id="sortable-right" data-bind="sortable: { data: blockThree, afterMove: myDropCallback }">
            <li class="list-group-item">Team: <strong data-bind="text: name"></strong> <span data-bind="text: team"></span></li>
        </ul>
    </div>
</div>

<!-- data for Contenido -->
<input type="hidden" name="CMS_VAR[10]" id="blockOne" value="<?php echo $orig_blockOne; ?>" data-bind="value: $root.saveBlockOne()" />
<input type="hidden" name="CMS_VAR[11]" id="blockTwo" value="<?php echo $orig_blockTwo; ?>" data-bind="value: $root.saveBlockTwo()" />
<input type="hidden" name="CMS_VAR[12]" id="blockThree" value="<?php echo $orig_blockThree; ?>" data-bind="value: $root.saveBlockThree()" />
<?php