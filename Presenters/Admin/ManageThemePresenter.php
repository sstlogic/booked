<?php
/**
 * Copyright 2013-2023 Twinkle Toes Software, LLC
 */

require_once(ROOT_DIR . 'Presenters/ActionPresenter.php');

class ManageThemePresenter extends ActionPresenter
{
    /**
     * @var ManageThemePage
     */
    private $page;

    public function __construct(ManageThemePage $page)
    {
        parent::__construct($page);
        $this->page = $page;
        $this->AddAction('update', 'UpdateTheme');
        $this->AddAction('removeLogo', 'RemoveLogo');
        $this->AddAction('removeFavicon', 'RemoveFavicon');
    }

    public function UpdateTheme()
    {
        $logoFile = $this->page->GetLogoFile();
        $cssFile = $this->page->GetCssFile();
        $favicon = $this->page->GetFaviconFile();

        $cache = CustomFileCache::Load($this->page->GetPath());

        if ($logoFile != null) {
            Log::Debug('Replacing logo with ' . $logoFile->OriginalName());

            $this->RemoveLogo();

            $target = ROOT_DIR . 'Web/img/custom-logo.' . $logoFile->Extension();
            $copied = copy($logoFile->TemporaryName(), $target);
            if (!$copied) {
                Log::Error('Could not replace logo. Ensure directory is writable.',
                    ['fileName' => $logoFile->OriginalName(), 'directory' => $target]);
            }
            $cache->UpdateLogo();
        }
        if ($cssFile != null) {
            Log::Debug('Replacing css file with ' . $cssFile->OriginalName());
            $target = ROOT_DIR . 'Web/css/custom-style.css';
            $copied = copy($cssFile->TemporaryName(), $target);
            if (!$copied) {
                Log::Error('Could not replace css. Ensure directory is writable.',
                    ['fileName' => $cssFile->OriginalName(), 'directory' => $target]);
            }
            $cache->UpdateCss();
        }
        if ($favicon != null) {
            Log::Debug('Replacing favicon with ' . $favicon->OriginalName());

            $this->RemoveFavicon();

            $target = ROOT_DIR . 'Web/custom-favicon.' . $favicon->Extension();
            $copied = copy($favicon->TemporaryName(), $target);
            if (!$copied) {
                Log::Error('Could not replace favicon. Ensure directory is writable.',
                    ['fileName' => $favicon->OriginalName(), 'directory' => $target]);
            }
            $cache->UpdateFavicon();
        }

        $cache->Save($this->page->GetPath());
    }

    public function RemoveLogo()
    {
        try {
            $targets = glob(ROOT_DIR . 'Web/img/custom-logo.*');
            foreach ($targets as $target) {
                $removed = unlink($target);
                if (!$removed) {
                    Log::Error('Could not remove existing logo. Ensure directory is writable.', ['directory' => $target]);
                }
            }
        } catch (Exception $ex) {
            Log::Error('Could not remove logos.', ['exception' => $ex]);
        }
    }

    public function RemoveFavicon()
    {
        try {
            $targets = glob(ROOT_DIR . 'Web/custom-favicon.*');
            foreach ($targets as $target) {
                $removed = unlink($target);
                if (!$removed) {
                    Log::Error('Could not remove existing favicon. Ensure directory is writable.', ['directory' => $target]);
                }
            }
        } catch (Exception $ex) {
            Log::Error('Could not remove favicon.', ['exception' => $ex]);
        }
    }

    protected function LoadValidators($action)
    {
        $this->page->RegisterValidator('logoFile', new FileUploadValidator($this->page->GetLogoFile()));
        $this->page->RegisterValidator('logoFileExt', new FileTypeValidator($this->page->GetLogoFile(), array('jpg', 'png', 'gif')));
        $this->page->RegisterValidator('cssFile', new FileUploadValidator($this->page->GetCssFile()));
        $this->page->RegisterValidator('cssFileExt', new FileTypeValidator($this->page->GetCssFile(), 'css'));
        $this->page->RegisterValidator('faviconFile', new FileUploadValidator($this->page->GetFaviconFile()));
        $this->page->RegisterValidator('faviconFileExt', new FileTypeValidator($this->page->GetFaviconFile(), array('ico', 'jpg', 'png', 'gif')));

    }
}
