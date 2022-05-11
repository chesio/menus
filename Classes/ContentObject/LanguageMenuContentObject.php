<?php

declare(strict_types=1);
namespace B13\Menus\ContentObject;

/*
 * This file is part of TYPO3 CMS-based extension "menus" by b13.
 *
 * It is free software; you can redistribute it and/or modify it under
 * the terms of the GNU General Public License, either version 2
 * of the License, or any later version.
 */

use B13\Menus\Compiler\LanguageMenuCompiler;
use B13\Menus\PageStateMarker;
use TYPO3\CMS\Core\Site\Entity\SiteLanguage;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Frontend\ContentObject\AbstractContentObject;
use TYPO3\CMS\Frontend\ContentObject\ContentObjectRenderer;

/**
 * Build a menu out of the available languages
 */
class LanguageMenuContentObject extends AbstractContentObject
{
    /**
     * @param array $conf
     * @return string|void
     */
    public function render($conf = [])
    {
        $pages = GeneralUtility::makeInstance(LanguageMenuCompiler::class)->compile($this->cObj, $conf);
        $content = $this->renderItems($pages, $conf);
        return $this->cObj->stdWrap($content, $conf);
    }

    protected function renderItems(array $pages, array $conf): string
    {
        $content = '';
        $cObjForItems = GeneralUtility::makeInstance(ContentObjectRenderer::class);
        $currentLanguage = $this->getCurrentSiteLanguage();
        foreach ($pages as $page) {
            PageStateMarker::markStates($page);
            if ((int)$page['sys_language_uid'] === $currentLanguage->getLanguageId()) {
                $page['isActiveLanguage'] = true;
            }
            $cObjForItems->start($page, 'pages');
            $content .= $cObjForItems->cObjGetSingle($conf['renderObj'], $conf['renderObj.']);
        }
        return $content;
    }

    protected function getCurrentSiteLanguage(): ?SiteLanguage
    {
        return $GLOBALS['TYPO3_REQUEST']->getAttribute('language');
    }
}
