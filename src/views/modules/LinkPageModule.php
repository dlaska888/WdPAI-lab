<?php

namespace modules;

class LinkPageModule
{
    public static function render(string $pageId, bool $hidden = false): string
    {
        $searchIcon = IconModule::render('search');
        $addIcon = IconModule::render('add');
        $hidden = $hidden ? "hidden" : "";

        return
            <<<HTML
            <section id="{$pageId}" class="page-links flex-column {$hidden}">
                <div class="search-container flex flex-center hide-mobile">
                    <input type="text" name="search" class="input" placeholder="Search">
                    <div id="btn-search" class="btn-menu flex flex-center">
                        {$searchIcon}
                    </div>
                    <div id="btn-add" class="btn-menu flex flex-center">
                        {$addIcon}
                    </div>
                </div>
                <div class="groups-container"></div>
            </section>
            HTML;
    }
}