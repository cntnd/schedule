<div class="erste_mannschaft">
    {if !isset($team1.teamData)}
        {$team1.data_ort}<br/>
        {$team1.data_full_date}<br/>
        {$team1.Bezeichnung}<br/>
        <a href="{$team1.data_url}" target="_blank" class="erste-mannschaft-titel">
            {$team1.TeamnameA}<br/>
            vs.<br/>
            {$team1.TeamnameB}
        </a>
    {else}
        <p>
            Momentan keine Spiele
        </p>
    {/if}
</div>
{if $count>0}
    <div class="naechste-spiele row">
        <div class="col-md-6 col-sm-6 left-content">
            {foreach from=$spieleLeft item=spiel name=spieleLeft}
            {if $spiel.Spieldatum!=$oldSpieldatumLeft}
            {if !$smarty.foreach.spieleLeft.first}</ul>{/if}
            <h5>{$spiel.data_datum}</h5><ul>
                {elseif !$spiel.teamData && $smarty.foreach.spieleLeft.first}
                <ul>
                    {/if}
                    <!-- LEFT -->
                    <li>
                        {if !isset($spiel.teamData)}
                            <span class="naechste-spiele-titel {if $spiel.data_zeit==''}no-spiel-zeit{/if}">
							{$spiel.data_zeit}
                                {if isset($spiel.data_url)}
                                    <a href="{$spiel.data_url}" target="_blank">{$spiel.data_team}</a>
                                {else}
                                    {$spiel.data_team}
                                {/if}
					</span>
                            {$spiel.TeamnameA} &#8211; {$spiel.TeamnameB}
                        {else}
                            <span class="naechste-spiele-titel">{$spiel.data_team}</span>
                            Keine Spiele
                        {/if}
                    </li>
                    {assign var="oldSpieldatumLeft" value="{$spiel.Spieldatum}"}
                    {/foreach}
                </ul>
        </div>
        <div class="col-md-6 col-sm-6 right-content">
            {foreach from=$spieleRight item=spiel name=spieleRight}
            {if $spiel.Spieldatum!=$oldSpieldatumRight}
            {if !$smarty.foreach.spieleRight.first}</ul>{/if}
            <h5>{$spiel.data_datum}</h5><ul>
                {elseif !$spiel.teamData && $smarty.foreach.spieleRight.first}
                <ul>
                    {/if}
                    <!-- RIGHT -->
                    {if {$spiel.data_team}}
                        <li>
                            {if !isset($spiel.teamData)}
                                <span class="naechste-spiele-titel {if $spiel.data_zeit==''}no-spiel-zeit{/if}">
						{$spiel.data_zeit}
                                    {if isset($spiel.data_url)}
                                        <a href="{$spiel.data_url}" target="_blank">{$spiel.data_team}</a>
                                    {else}
                                        {$spiel.data_team}
                                    {/if}
					</span>
                                {$spiel.TeamnameA} &#8211; {$spiel.TeamnameB}
                            {else}
                                <span class="naechste-spiele-titel">{$spiel.data_team}</span>
                                Keine Spiele
                            {/if}
                        </li>
                    {/if}
                    {assign var="oldSpieldatumRight" value="{$spiel.Spieldatum}"}
                    {/foreach}
                    <!-- KIFU -->
                    {foreach from=$spieleKifu item=spiel name=spieleKifu}
                    {if $spiel.Spieldatum!=$oldSpieldatumRight}
                </ul><h5>{$spiel.data_datum}</h5><ul>
                    {/if}
                    <li>
				<span class="naechste-spiele-titel">
					{$spiel.data_zeit}
                    {if $spiel.VereinsnummerA=='10311'}
                        <a href="/fcl/nachwuchs/aufgebote/kifu-aufgebote.html">{$spiel.data_team}</a>
					{else}
						<span>{$spiel.data_team}</span>
                    {/if}
				</span>
                        Turnier {$spiel.TeamnameA}
                        {if $spiel.Sportanlage!='' && $spiel.VereinsnummerA!='10311'}<br />{$spiel.Sportanlage}{/if}
                    </li>
                    {assign var="oldSpieldatumRight" value="{$spiel.Spieldatum}"}
                    {/foreach}
                </ul>
        </div>
    </div>
{/if}

<!-- kalender inline -->
<div id="fclCalendar">
    <strong>FCL-Kalender</strong>
    <p>Die gewünschten Teams auswählen und anschliessend auf "Generieren" klicken. Den Kalender herunterladen (rechte Maustaste, speichern) und in ihren Kalender importieren.<br />Die Termine müssen manuell angepasst werden.</p>
    <form>
        <div class="row" id="formCalendar">
            <div class="col-md-4">
                <div class="checkbox">
                    <label class="label-title">
                        <input type="checkbox" class="fcl-cal-title-checkbox" name="aktive_all" id="aktive"> Aktive
                    </label>
                </div>
                <!-- Aktive -->
                {foreach from=$aktivTeams item=team name=aktivTeams}
                    <div class="checkbox">
                        <label>
                            <input type="checkbox" class="fcl-cal-checkbox" name="aktive" value="{$team.Team}"> {$team.data_team}
                        </label>
                    </div>
                {/foreach}
            </div>
            <div class="col-md-4">
                <div class="checkbox">
                    <label class="label-title">
                        <input type="checkbox" class="fcl-cal-title-checkbox" name="junioren_all" id="junioren"> Junioren
                    </label>
                </div>
                <!-- Junioren -->
                {foreach from=$juniorenTeams item=team name=juniorenTeams}
                    <div class="checkbox">
                        <label>
                            <input type="checkbox" class="fcl-cal-checkbox" name="junioren" value="{$team.Team}"> {$team.data_team}
                        </label>
                    </div>
                {/foreach}
            </div>
            <div class="col-md-4">
                <div class="checkbox">
                    <label class="label-title">
                        <input type="checkbox" class="fcl-cal-title-checkbox" name="kifu_all" id="kifu"> KiFu
                    </label>
                </div>
                <!-- KiFu -->
                {foreach from=$spieleKifu item=team name=spieleKifu}
                    <div class="checkbox">
                        <label>
                            <input type="checkbox" class="fcl-cal-checkbox" name="kifu" value="{$team.Team}"> {$team.data_team}
                        </label>
                    </div>
                {/foreach}
            </div>
        </div>
        <div id="generatedCalendar" style="display:none;">
            <p><strong><a href="http://www.fclaenggasse.ch/spielplan/calendar.php" class="calendar" id="calendarLink" target="_blank">Kalender-Link</a></strong> (rechte Maustaste, speichern)</p>
        </div>
        <button type="button" class="btn btn-primary" id="doCalendar">Generieren</button>
        <button type="button" class="btn btn-danger" id="doReset">Zurücksetzen</button>
    </form>
</div>