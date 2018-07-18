{foreach from=$spieleLeft item=spiel name=spieleLeft}
    {if $smarty.foreach.spieleLeft.first}
    <div class="erste_mannschaft">
        {if $spiel.noData eq 'false'}
            {$spiel.data_ort}
            <br/>
            {$spiel.data_full_date}
            <br/>
            {if $spiel.SpielTyp eq 'Trainingsspiele'}
                Trainingsspiel
                <br/>
            {elseif $spiel.SpielTyp neq 'Meisterschaft'}
                {$spiel.SpielTyp}
                <br/>
            {/if}
            <a href="{$spiel.data_url}" target="_blank" class="erste-mannschaft-titel">
                {if $spiel.VereinsnummerA eq '10311'}FC Länggasse{else}{$spiel.TeamnameA}{/if}
                <br/>vs.<br/>
                {if $spiel.VereinsnummerB eq '10311'}FC Länggasse{else}{$spiel.TeamnameB}{/if}
            </a>
        {else}
            <p>
                Momentan keine Spiele
            </p>
        {/if}
    </div>

<div class="naechste-spiele row">

    <div class="col-md-6 col-sm-6 left-content">
        {/if}
        {if $smarty.foreach.spieleLeft.iteration gt 1}
        {if $spiel.Spieldatum!=$oldSpieldatumLeft}
            <h5>{$spiel.data_datum}</h5>
        {/if}
        <ul>
            <li>
                    <span class="naechste-spiele-titel ">
                        {$spiel.data_zeit}
                        {if $spiel.data_url neq ''}
                        <a href="{$spiel.data_url}" target="_blank">
                        {/if}
                            <span>{$spiel.data_team}</span>
                            {if $spiel.data_url neq ''}
                            </a>
                        {/if}
                    </span>
                {if $spiel.SpielTyp eq 'Trainingsspiele'}
                    <small>Trainingsspiel</small>
                    <br/>
                {elseif $spiel.SpielTyp neq 'Meisterschaft'}
                    <small>{$spiel.SpielTyp}</small>
                    <br/>
                {/if}
                {$spiel.TeamnameA} – {$spiel.TeamnameB}
            </li>
        </ul>
        {if $smarty.foreach.spieleLeft.last}
    </div>
    {/if}
    {assign var="oldSpieldatumLeft" value="{$spiel.Spieldatum}"}
    {/if}
    {/foreach}

    {foreach from=$spieleRight item=spiel name=spieleRight}
    {if $smarty.foreach.spieleRight.first}
    <div class="col-md-6 col-sm-6 right-content">
        {/if}
        {if $spiel.Spieldatum!=$oldSpieldatumLeft || $smarty.foreach.spieleRight.first}
            <h5>{$spiel.data_datum}</h5>
        {/if}
        <ul>
            <li>
                    <span class="naechste-spiele-titel ">
                        {$spiel.data_zeit}
                        {if $spiel.data_url neq ''}
                        <a href="{$spiel.data_url}" target="_blank">
                        {/if}
                            <span>{$spiel.data_team}</span>
                            {if $spiel.data_url neq ''}
                            </a>
                        {/if}
                    </span>
                {if $spiel.SpielTyp eq 'Trainingsspiele'}
                    <small>Trainingsspiel</small>
                    <br/>
                {elseif $spiel.SpielTyp neq 'Meisterschaft'}
                    <small>{$spiel.SpielTyp}</small>
                    <br/>
                {/if}
                {$spiel.TeamnameA} – {$spiel.TeamnameB}
            </li>
        </ul>
        {if $smarty.foreach.spieleRight.last}
    </div>
</div>
    {/if}
{assign var="oldSpieldatumLeft" value="{$spiel.Spieldatum}"}
{/foreach}