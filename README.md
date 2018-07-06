**cntnd_schedule**

* includes in "php" files: `cInclude('module', 'includes/class.module.mparticleinclude.php');`
* includes in "includes" php files: `include_once($moduleHandler->getModulePath() . 'vendor/xyz.php');`

*contenido php functions*

* `$client = cRegistry::getClientId();`
* `$lang = cRegistry::getLanguageId();`  
* `mi18n("SELECT_ARTICLE")`
* `buildArticleSelect("CMS_VAR[2]", $oModule->cmsCatID, $oModule->cmsArtID);`

`$module = new cModuleHandler($cCurrentModule);
echo $module->getModulePath();`

*load js files in input_php*

`$.getScript("my_lovely_script.js", function() {
alert("Script loaded but not necessarily executed.");
});`

// for util class
$module = new cModuleHandler($cCurrentModule);
$absolutePath = $module->getModulePath(); 
$filename = $absolutePath."js/cntnd_schedule.js";
$handle = fopen($filename, "r");
$contents = fread($handle, filesize($filename));
fclose($handle);
?>
<script language="javascript" type="text/javascript"><?= $contents ?></script>

*api*

* url: http://localhost:3080/vendor/api/api.php/
* method: post 
* json:
{
    "username": "admin",
    "password": "admin"
}
* then geth csrf token back
* method: get (whatever) 
* url: http://localhost:3080/vendor/api/api.php/spielplan?csrf=CSRF-TOKEN