?><?php
// cntnd_schedule_input
$cntnd_module = "cntnd_schedule";

// input/vars
$hasCustomTeams   = (bool) "CMS_VALUE[9]";
$orig_blockOne   = "CMS_VALUE[10]";
$orig_blockTwo  = "CMS_VALUE[11]";
$orig_blockThree  = "CMS_VALUE[12]";
$blockOne        = html_entity_decode($orig_blockOne,ENT_QUOTES);
$blockTwo       = html_entity_decode($orig_blockTwo,ENT_QUOTES);
$blockThree       = html_entity_decode($orig_blockThree,ENT_QUOTES);
$tables = array(
    "default" => "spielplan",
    "custom" => "spielplan_kifu");
$custom_csv = "CMS_VALUE[20]";
$csv_file  = "CMS_VALUE[21]";
$separator = "CMS_VALUE[22]";
if (empty($separator)){
    $separator=',';
}

// includes
cInclude('module', 'includes/script.cntnd_schedule_input.php');
cInclude('module', 'includes/style.cntnd_schedule.php');
cInclude('module', 'includes/class.cntndutil.php');
cInclude('module', 'includes/class.cntnd_schedule_input.php');

// classes
$conDb = new cDb;
$util = new CntndUtilLegacy();
$cntndSchedule = new CntndScheduleInput($tables, $hasCustomTeams, $custom_csv, $csv_file, $separator, $client);
$module = new cModuleHandler($cCurrentModule);
$absolutePath = $module->getModulePath();

// init all teams
$scheduleTeams=$cntndSchedule->scheduleTeams();

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
echo '<script type="text/javascript">';
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

    <fieldset class="d-flex">
        <legend><?= mi18n("VEREIN") ?></legend>

        <div class="form-group">
            <label for="vereinsname"><?= mi18n("VEREINSNAME") ?></label>
            <input id="vereinsname" type="text" name="CMS_VAR[4]" value="CMS_VALUE[4]" />
        </div>

        <div class="form-group">
            <label for="vereinsnummer"><?= mi18n("VEREINSNUMMER") ?></label>
            <input id="vereinsnummer" type="text" name="CMS_VAR[5]" value="CMS_VALUE[5]" />
        </div>

        <div class="form-group w-100">
            <div class="form-check form-check-inline">
                <input id="custom_teams" class="form-check-input disable-dependent" data-disable-dependent="custom_teams_csv" type="checkbox" name="CMS_VAR[9]" value="true" <?php if("CMS_VALUE[9]"=='true'){ echo 'checked'; } ?> />
                <label for="custom_teams" class="form-check-label"><?= mi18n("ACTIVATE_CUSTOM_TEAMS") ?></label>
            </div>

            <fieldset <?php if (!$hasCustomTeams){ echo "disabled"; } ?> id="custom_teams_csv">
                <legend><?= mi18n("CUSTOM_TEAMS_CSV") ?></legend>

                <div class="form-check form-check-inline w-100">
                    <input id="csv_parser" class="form-check-input custom_teams_csv" type="radio" name="CMS_VAR[20]" value="parser" <?php if("CMS_VALUE[20]"=='parser'){ echo 'checked'; } ?> />
                    <label for="csv_parser" class="form-check-label"><?= mi18n("CUSTOM_TEAMS_CSV_PARSER") ?></label>
                </div>

                <div class="form-check form-check-inline w-100">
                    <input id="csv_editor" class="form-check-input custom_teams_csv" type="radio" name="CMS_VAR[20]" value="editor" <?php if("CMS_VALUE[20]"=='editor'){ echo 'checked'; } ?> />
                    <label for="csv_editor" class="form-check-label"><?= mi18n("CUSTOM_TEAMS_CSV_EDITOR") ?></label>
                </div>

                <fieldset class="d-flex" id="csv_file" <?php if ($custom_csv!="editor"){ echo "disabled"; } ?>>
                    <legend><?= mi18n("LABEL_FILE") ?></legend>

                    <div class="form-group" style="margin-right: 12px;">
                        <label for="filename"><?= mi18n("LABEL_FILE") ?></label>
                        <select name="CMS_VAR[21]" id="filename" size="1" onchange="this.form.submit()" style="width: auto !important;">
                            <option value="false"><?= mi18n("SELECT_CHOOSE") ?></option>
                            <?php
                            foreach ($cntndSchedule->customTeamsCSVFiles() as $value) {
                                $selected='';
                                if ($csv_file==$value['filepath']){
                                    $selected='selected="selected"';
                                }
                                echo '<option value="'.$value['filepath'].'" '.$selected.'>'.$value['filename'].'</option>';
                            }
                            ?>
                        </select>
                    </div>

                    <div class="form-group">
                        <label for="filename"><?= mi18n("LABEL_SEPARATOR") ?></label>
                        <input type="text" maxlength="1" name="CMS_VAR[22]" value="<?= $separator ?>"/>
                    </div>
                </fieldset>

            </fieldset>
        </div>
    </fieldset>


    <fieldset>
        <legend><?= mi18n("DATERANGE") ?></legend>
        <p><?= mi18n("DATERANGE_HELP") ?></p>
        <div class="d-flex space-between">
            <div class="form-group w-30">
                <label for="daterange_block_one"><?= mi18n("DATERANGE_BLOCK_ONE") ?></label>
                <input id="daterange_block_one" type="number" name="CMS_VAR[6]" value="CMS_VALUE[6]" />
            </div>

            <div class="form-group w-30">
                <label for="daterange_block_two"><?= mi18n("DATERANGE_BLOCK_TWO") ?></label>
                <input id="daterange_block_two" type="number" name="CMS_VAR[7]" value="CMS_VALUE[7]" />
            </div>

            <div class="form-group w-30">
                <label for="daterange_block_custom"><?= mi18n("DATERANGE_BLOCK_CUSTOM") ?></label>
                <input id="daterange_block_custom" type="number" name="CMS_VAR[8]" value="CMS_VALUE[8]" />
            </div>
        </div>
    </fieldset>

    <button data-bind="click: eraseTeams" class="btn btn-light" type="submit"><?= mi18n("MODULE_RESET") ?></button>
</div>

<hr />

<div class="w-30 config-container">
    <div class="card bg-light">
        <div class="card-body">
            <div class="form-group">
                <label for="newTeamText"><strong><?= mi18n("NEW_TEAM") ?></strong></label>
                <input type="text" class="form-control form-control-sm" id="newTeamText" placeholder="<?= mi18n("NEW_TEAM") ?>" data-bind="value: newTeamText" />
            </div>
            <button data-bind="click: addTeam" class="btn btn-primary"><?= mi18n("ADD") ?></button>
            <button data-bind="click: resetTeams" class="btn"><?= mi18n("RESET") ?></button>
        </div>
    </div>
</div>

<div class="d-flex space-between">

    <div class="w-30">
        <p class="col-title"><?= mi18n("BLOCK_ONE_TITLE") ?><p>
        <div class="card sortable list-group" id="sortable-left" data-bind="sortable: { data: blockOne, afterMove: myDropCallback }">
            <div class="card">
            <div class="card-body">
                <strong>
                    Team <span data-bind="text: name"></span>
                </strong>
                <div class="expand">
                    <div class="form-group">
                        <select data-bind="options: $root.availableTeams, value: team, optionsValue: 'team', optionsCaption: '<?= mi18n("CHOOSE_TEAM") ?>', optionsText: 'label'" class="form-control form-control-sm"></select>
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
                    <button class="btn btn-light" data-bind="click: $parent.removeTeamOne"><?= mi18n("REMOVE") ?></button>
                </div>
            </div>
        </div>
        </div>
    </div>

    <div class="w-30">
        <p class="col-title"><?= mi18n("BLOCK_TWO_TITLE") ?><p>
        <div class="card sortable list-group" id="sortable-right" data-bind="sortable: { data: blockTwo, afterMove: myDropCallback }">
            <div class="card">
            <div class="card-body">
                <strong>
                    Team <span data-bind="text: name"></span>
                    <!-- <span class="expand-button">expand</span> -->
                </strong>
                <div class="expand">
                    <div class="form-group">
                        <select class="form-control form-control-sm" data-bind="options: $root.availableTeams, value: team, optionsValue: 'team', optionsCaption: '<?= mi18n("CHOOSE_TEAM") ?>', optionsText: 'label'"></select>
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
                            <input class="form-check-input" type="checkbox" name="customTeam" value="true" data-bind="checked: customTeam">
                            <label class="form-check-label" for="customTeam"><?= mi18n("CUSTOM_TEAM") ?></label>
                        </div>
                    </div>
                    <a href="#" class="btn btn-light" data-bind="click: $parent.removeTeamTwo"><?= mi18n("REMOVE") ?></a>
                </div>
            </div>
        </div>
        </div>
    </div>

    <div class="w-30">
        <p class="col-title"><?= mi18n("BLOCK_THREE_TITLE") ?><p>
        <div class="card sortable list-group" id="sortable-right" data-bind="sortable: { data: blockThree, afterMove: myDropCallback }">>
            <div class="card">
            <div class="card-body">
                <strong>
                    Team <span data-bind="text: name"></span>
                    <!-- <span class="expand-button">expand</span> -->
                </strong>
                <div class="expand">
                    <div class="form-group">
                        <select class="form-control form-control-sm" data-bind="options: $root.availableTeams, value: team, optionsValue: 'team', optionsCaption: '<?= mi18n("CHOOSE_TEAM") ?>', optionsText: 'label'"></select>
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
                            <input class="form-check-input" type="checkbox" name="customTeam" value="true" data-bind="checked: customTeam">
                            <label class="form-check-label" for="customTeam"><?= mi18n("CUSTOM_TEAM") ?></label>
                        </div>
                    </div>
                    <a href="#" class="btn btn-light" data-bind="click: $parent.removeTeamThree"><?= mi18n("REMOVE") ?></a>
                </div>
            </div>
        </div>
        </div>
    </div>
</div>

<!-- data for Contenido -->
<input type="hidden" name="CMS_VAR[10]" id="blockOne" value="<?php echo $orig_blockOne; ?>" data-bind="value: $root.saveBlockOne()" />
<input type="hidden" name="CMS_VAR[11]" id="blockTwo" value="<?php echo $orig_blockTwo; ?>" data-bind="value: $root.saveBlockTwo()" />
<input type="hidden" name="CMS_VAR[12]" id="blockThree" value="<?php echo $orig_blockThree; ?>" data-bind="value: $root.saveBlockThree()" />
<?php