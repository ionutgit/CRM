<?php

namespace ChurchCRM\Tasks;

use ChurchCRM\Authentication\AuthenticationManager;
use ChurchCRM\dto\SystemURLs;
use ChurchCRM\Utils\ChurchCRMReleaseManager;
use ChurchCRM\Utils\LoggerUtils;

class LatestReleaseTask implements TaskInterface
{
    private \ChurchCRM\dto\ChurchCRMRelease $installedVersion;
    private ?\ChurchCRM\dto\ChurchCRMRelease $latestVersion = null;

    public function __construct()
    {
        $this->installedVersion = ChurchCRMReleaseManager::getReleaseFromString($_SESSION['sSoftwareInstalledVersion']);
    }

    public function isActive(): bool
    {
        $isCurrent = ChurchCRMReleaseManager::isReleaseCurrent($this->installedVersion);
        if (!$isCurrent) {
            try {
                // This can fail with an exception if the currently running software is "not current"
                // but there are no more available releases.
                // this exception will really only happen when running development versions of the software
                // or if the ChurchCRM Release on which the current instance is running has been deleted
                $this->latestVersion = ChurchCRMReleaseManager::getNextReleaseStep($this->installedVersion);
            } catch (\Exception $e) {
                LoggerUtils::getAppLogger()->debug($e);

                return false;
            }

            return true;
        }

        return false;
    }

    public function isAdmin(): bool
    {
        return false;
    }

    public function getLink(): string
    {
        if (AuthenticationManager::getCurrentUser()->isAdmin()) {
            return SystemURLs::getRootPath().'/UpgradeCRM.php';
        } else {
            return 'https://github.com/ChurchCRM/CRM/releases/latest';
        }
    }

    public function getTitle(): string
    {
        return gettext('New Release').' '.$this->latestVersion;
    }

    public function getDesc(): string
    {
        return $this->latestVersion->GetReleaseNotes();
    }
}
