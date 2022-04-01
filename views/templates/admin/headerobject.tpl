{*
* Project : everpsseo
* @author Team EVER
* @copyright Team EVER
* @license   Tous droits réservés / Le droit d'auteur s'applique (All rights reserved / French copyright law applies)
* @link https://www.team-ever.com
*}
{if isset($headerObjectName) && $headerObjectName && isset($objectGSearch) && $objectGSearch}
    <div class="panel everheader">
        <div class="panel-heading">
            <i class="icon icon-smile"></i> {l s='Ever SEO' mod='everpsseo'}
        </div>
        <div class="panel-body">
            <div class="col-xs-12 col-lg-6">
                <h3>{l s='You are currenctly seeing : ' mod='everpsseo'}{$headerObjectName|escape:'htmlall':'UTF-8'}</h3>
                <a href="#everbottom" id="evertop">
                   <img id="everlogo" src="{$image_dir|escape:'htmlall':'UTF-8'}/ever.png" style="max-width: 120px;float: left;">
                </a>
                <h4>{l s='Current note :' mod='everpsseo'}<span class="badge {$colorNotation|escape:'htmlall':'UTF-8'}">{$keywordsQlty|escape:'htmlall':'UTF-8'} / 100</span></h4>
                <p><strong>{l s='Inactive elements won\'t be set on sitemap' mod='everpsseo'}</strong></p>

                <a href="https://www.google.fr/search?q={$objectGSearch|escape:'htmlall':'UTF-8'}" target="_blank" class="btn btn-default">{l s='Search for' mod='everpsseo'} {$headerObjectName|escape:'htmlall':'UTF-8'} {l s=' on Google' mod='everpsseo'}</a>
                <a href="{$editUrl|escape:'htmlall':'UTF-8'}" target="_blank" class="btn btn-default">{l s='Edit ' mod='everpsseo'} {$headerObjectName|escape:'htmlall':'UTF-8'}</a>
                <a href="{$objectUrl|escape:'htmlall':'UTF-8'}" target="_blank" class="btn btn-default">{l s='View ' mod='everpsseo'} {$headerObjectName|escape:'htmlall':'UTF-8'}</a>
                {if isset($moduleConfUrl) && $moduleConfUrl}
                    <a href="{$moduleConfUrl|escape:'htmlall':'UTF-8'}" target="_blank" class="btn btn-success">{l s='Direct link to module configuration' mod='everpsseo'}</a>
                {/if}
                {if isset($errors) && $errors}
                <div class="dropdown">
                  <button class="btn btn-secondary dropdown-toggle" type="button" id="dropdownMenuButton" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                    {l s='See all advices' mod='everpsseo'}
                  </button>
                  <div class="dropdown-menu alert alert-warning" aria-labelledby="dropdownMenuButton">
                    {foreach from=$errors item=error}
                        <p>{$error|escape:'htmlall':'UTF-8'}</p>
                    {/foreach}
                  </div>
                </div>
                {/if}
            </div>
            <div class="col-xs-12 col-lg-6">
                <h3>{l s='Advices on SEO quality' mod='everpsseo'}</h3>
                <ul>
                    <li>{l s='Content MUST be unique' mod='everpsseo'}</li>
                    <li>{l s='Use minimum 300 words per page, no maximal limit' mod='everpsseo'}</li>
                    <li>{l s='Add more keywords in page content, in phrases, not in a list' mod='everpsseo'}</li>
                    <li>{l s='Do NOT use h1 tag on pages' mod='everpsseo'}</li>
                    <li>{l s='Be sure that there is only ONE h1 tag per page' mod='everpsseo'}</li>
                    <li>{l s='Use at least two h2 tags, anf if you use h3 or others title tags, be sure that they are not orphans hn tags' mod='everpsseo'}</li>
                    <li>{l s='Make important words bold' mod='everpsseo'}</li>
                    <li>{l s='Add internal linking' mod='everpsseo'}</li>
                    <li>{l s='Copy-paste from Microsoft is not allowed, please use only HTML tags' mod='everpsseo'}</li>
                    <li>{l s='Title should not be more than 65 characters' mod='everpsseo'}</li>
                    <li>{l s='Meta description should not be more than 165 characters' mod='everpsseo'}</li>
                    <li>{l s='In order to use Ever SEO native canonical, your theme canonical URL must be removed from your theme' mod='everpsseo'}</li>
                </ul>
            </div>
        </div>
    </div>
{/if}
