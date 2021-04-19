<?php
// cntnd_schedule_output

// assert framework initialization
defined('CON_FRAMEWORK') || die('Illegal call: Missing framework initialization - request aborted.');

// input/vars
$moduleActive     = "CMS_VALUE[3]";
$vereinsname      = "CMS_VALUE[4]";
$vereinsnummer    = "CMS_VALUE[5]";

$dateRanges = array(
    "dateRangeBlockOne"    => "CMS_VALUE[6]",
    "dateRangeBlockTwo"    => "CMS_VALUE[7]",
    "dateRangeBlockCustom" => "CMS_VALUE[8]");

$blockOne         = "CMS_VALUE[10]";
$blockTwo         = "CMS_VALUE[11]";
$blockThree       = "CMS_VALUE[12]";

$tables = array(
    "default" => "spielplan",
    "custom" => "spielplan_kifu");

// includes
cInclude('module', 'includes/class.cntnd_schedule.php');

// other/vars
$schedule = new CntndSchedule($tables, $dateRanges, $vereinsname, $vereinsnummer, $blockOne, $blockTwo, $blockThree);

// laden der daten
$gamesBlockOne = $schedule->blockOne();
$gamesBlockTwo = $schedule->blockTwo();
$gamesBlockThree = $schedule->blockThree();

// smarty
$smarty = cSmartyFrontend::getInstance();
$smarty->assign('gamesBlockOne', $gamesBlockOne);
$smarty->assign('gamesBlockTwo', $gamesBlockTwo);
$smarty->assign('gamesBlockThree', $gamesBlockThree);
$smarty->assign('active', $moduleActive);
$smarty->display('default.html');
?>