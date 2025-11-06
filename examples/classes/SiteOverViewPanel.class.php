<?php

    use QCubed as Q;
    use QCubed\Control\Panel;
    use QCubed\Database\Service;
    use QCubed\Exception\Caller;

    /**
     * Represents a panel for displaying an overview of site information,
     * including details about the PHP environment, database, server configurations,
     * and supported features. This class also provides details about disk space usage and allocation.
     */
    class SiteOverViewPanel extends Panel
    {
        protected Q\Plugin\Control\Label $lblPhpVersion;
        protected Q\Plugin\Control\Label $lblPhpVersionInfo;
        protected Q\Plugin\Control\Label $lblDatabase;
        protected Q\Plugin\Control\Label $lblDatabaseInfo;
        protected Q\Plugin\Control\Label $lblDatabaseCharset;
        protected Q\Plugin\Control\Label $lblDatabaseCharsetInfo;
        protected Q\Plugin\Control\Label $lblDatabaseCollation;
        protected Q\Plugin\Control\Label $lblDatabaseCollationInfo;
        protected Q\Plugin\Control\Label $lblWebServer;
        protected Q\Plugin\Control\Label $lblWebServerInfo;

        protected Q\Plugin\Control\Label $lblGDSupport;
        protected Q\Plugin\Control\Label $lblGDSupportInfo;
        protected Q\Plugin\Control\Label $lblMbstring;
        protected Q\Plugin\Control\Label $lblMbstringInfo;
        protected Q\Plugin\Control\Label $lblZipArchive;
        protected Q\Plugin\Control\Label $lblZipArchiveInfo;

        protected Q\Plugin\Control\Label $lblMemoryLimit;
        protected Q\Plugin\Control\Label $lblMemoryLimitInfo;
        protected Q\Plugin\Control\Label $lblFileUploads;
        protected Q\Plugin\Control\Label $lblFileUploadsInfo;
        protected Q\Plugin\Control\Label $lblUploadMaxFilesize;
        protected Q\Plugin\Control\Label $lblUploadMaxFilesizeInfo;
        protected Q\Plugin\Control\Label $lblPostMaxSize;
        protected Q\Plugin\Control\Label $lblPostMaxSizeInfo;
        protected Q\Plugin\Control\Label $lblMaxFileUploads;
        protected Q\Plugin\Control\Label $lblMaxFileUploadsInfo;

        protected DiskSpaceCheck $objDiskSpace;
        protected SimplePieChart $objPieChart;
        protected Q\Plugin\Control\Label $lblTotalDiskSpace;
        protected Q\Plugin\Control\Label $lblTotalDiskSpaceInfo;
        protected Q\Plugin\Control\Label $lblDiskUsedSpace;
        protected Q\Plugin\Control\Label $lblDiskUsedSpaceInfo;
        protected Q\Plugin\Control\Label $lblDiskFreeSpace;
        protected Q\Plugin\Control\Label $lblDiskFreeSpaceInfo;

        protected string $strTemplate = 'SiteOverViewPanel.tpl.php';

        /**
         * Constructor method for initializing the object. Sets up metadata and creates
         * necessary UI elements such as inputs, buttons, modals, and notifications.
         *
         * @param mixed $objParentObject The parent object of the control.
         * @param string|null $strControlId An optional control ID for the created object.
         *
         * @return void
         * @throws Caller
         */
        public function __construct(mixed $objParentObject, ?string $strControlId = null)
        {
            try {
                parent::__construct($objParentObject, $strControlId);
            } catch (Caller $objExc) {
                $objExc->IncrementOffset();
                throw $objExc;
            }

            $this->createInputs();
        }

        ///////////////////////////////////////////////////////////////////////////////////////////

        /**
         * Initializes and creates input controls for metadata management, including alerts, labels, and textboxes for keywords, descriptions, and authors.
         *
         * @return void
         * @throws Caller
         */
        public function createInputs(): void
        {
            $this->lblPhpVersion = new Q\Plugin\Control\Label($this);
            $this->lblPhpVersion->Text = t('PHP version:');
            $this->lblPhpVersion->addCssClass('col-md-4');
            $this->lblPhpVersion->setCssStyle('font-weight', '500');

            $this->lblPhpVersionInfo = new Q\Plugin\Control\Label($this);
            $this->lblPhpVersionInfo->Text = phpversion();
            $this->lblPhpVersionInfo->setCssStyle('font-weight', 'normal');

            ///////////////////////////////////////////////////////////////////////////////////////////

            $db = Service::getDatabase(1);

            $this->lblDatabase = new Q\Plugin\Control\Label($this);
            $this->lblDatabase->Text = t('Database version:');
            $this->lblDatabase->addCssClass('col-md-4');
            $this->lblDatabase->setCssStyle('font-weight', '500');

            $this->lblDatabaseInfo = new Q\Plugin\Control\Label($this);

            $row = $db->query("SELECT VERSION() AS ver, @@version_comment AS comment")->fetchArray();

            $versionRaw = $row[0];
            $comment = $row[1];

            if (stripos($versionRaw, 'mariadb') !== false || stripos($comment, 'mariadb') !== false) {
                $dbType = 'MariaDB';
            } elseif (stripos($versionRaw, 'mysql') !== false || stripos($comment, 'mysql') !== false) {
                $dbType = 'MySQL';
            } else {
                $dbType = $comment ?: '?';
            }

            $this->lblDatabaseInfo->Text = sprintf('%s %s', $dbType, $versionRaw);
            $this->lblDatabaseInfo->setCssStyle('font-weight', 'normal');

            ///////////////////////////////////////////////////////////////////////////////////////////

            $this->lblDatabaseCharset = new Q\Plugin\Control\Label($this);
            $this->lblDatabaseCharset->Text = t('Database charset:');
            $this->lblDatabaseCharset->addCssClass('col-md-4');
            $this->lblDatabaseCharset->setCssStyle('font-weight', '500');

            $this->lblDatabaseCharsetInfo = new Q\Plugin\Control\Label($this);
            $charsetRow = $db->query("SHOW VARIABLES LIKE 'character_set_database'")->fetchArray();
            $this->lblDatabaseCharsetInfo->Text = $charsetRow[1] ?? '?';
            $this->lblDatabaseCharsetInfo->setCssStyle('font-weight', 'normal');

            $this->lblDatabaseCollation = new Q\Plugin\Control\Label($this);
            $this->lblDatabaseCollation->Text = t('Database collation:');
            $this->lblDatabaseCollation->addCssClass('col-md-4');
            $this->lblDatabaseCollation->setCssStyle('font-weight', '500');

            $this->lblDatabaseCollationInfo = new Q\Plugin\Control\Label($this);
            $collationRow = $db->query("SHOW VARIABLES LIKE 'collation_database'")->fetchArray();
            $this->lblDatabaseCollationInfo->Text = $collationRow[1] ?? '?';
            $this->lblDatabaseCollationInfo->setCssStyle('font-weight', 'normal');

            ///////////////////////////////////////////////////////////////////////////////////////////

            $this->lblWebServer = new Q\Plugin\Control\Label($this);
            $this->lblWebServer->Text = t('Web server:');
            $this->lblWebServer->addCssClass('col-md-4');
            $this->lblWebServer->setCssStyle('font-weight', '500');

            $this->lblWebServerInfo = new Q\Plugin\Control\Label($this);
            $this->lblWebServerInfo->Text = $_SERVER["SERVER_SOFTWARE"];
            $this->lblWebServerInfo->setCssStyle('font-weight', 'normal');

            ///////////////////////////////////////////////////////////////////////////////////////////

            $this->lblGDSupport = new Q\Plugin\Control\Label($this);
            $this->lblGDSupport->Text = t('GD support:');
            $this->lblGDSupport->addCssClass('col-md-4');
            $this->lblGDSupport->setCssStyle('font-weight', '500');

            $this->lblGDSupportInfo = new Q\Plugin\Control\Label($this);
            $this->lblGDSupportInfo->Text = extension_loaded('gd') ? '<i class="fa fa-check text-success" aria-hidden="true"></i>' : '<i class="fa fa-times text-error" aria-hidden="true"></i>';
            $this->lblGDSupportInfo->HtmlEntities = false;

            $this->lblMbstring = new Q\Plugin\Control\Label($this);
            $this->lblMbstring->Text = t('Mbstring support:');
            $this->lblMbstring->addCssClass('col-md-4');
            $this->lblMbstring->setCssStyle('font-weight', '500');

            $this->lblMbstringInfo = new Q\Plugin\Control\Label($this);
            $this->lblMbstringInfo->Text = extension_loaded('mbstring') ? '<i class="fa fa-check text-success" aria-hidden="true"></i>' : '<i class="fa fa-times text-error" aria-hidden="true"></i>';
            $this->lblMbstringInfo->HtmlEntities = false;

            $this->lblZipArchive = new Q\Plugin\Control\Label($this);
            $this->lblZipArchive->Text = t('ZipArchive exists:');
            $this->lblZipArchive->addCssClass('col-md-4');
            $this->lblZipArchive->setCssStyle('font-weight', '500');

            $this->lblZipArchiveInfo = new Q\Plugin\Control\Label($this);
            $this->lblZipArchiveInfo->Text = class_exists('ZipArchive') ? '<i class="fa fa-check text-success" aria-hidden="true"></i>' : '<i class="fa fa-times text-error" aria-hidden="true"></i>';
            $this->lblZipArchiveInfo->HtmlEntities = false;

            ///////////////////////////////////////////////////////////////////////////////////////////

            $this->lblMemoryLimit = new Q\Plugin\Control\Label($this);
            $this->lblMemoryLimit->Text = 'memory_limit:';
            $this->lblMemoryLimit->addCssClass('col-md-4');
            $this->lblMemoryLimit->setCssStyle('font-weight', '500');

            $this->lblMemoryLimitInfo = new Q\Plugin\Control\Label($this);
            $this->lblMemoryLimitInfo->Text = ini_get('memory_limit') ?? null;
            $this->lblMemoryLimitInfo->setCssStyle('font-weight', 'normal');

            $this->lblFileUploads = new Q\Plugin\Control\Label($this);
            $this->lblFileUploads->Text = 'file_uploads:';
            $this->lblFileUploads->addCssClass('col-md-4');
            $this->lblFileUploads->setCssStyle('font-weight', '500');

            $this->lblFileUploadsInfo = new Q\Plugin\Control\Label($this);
            $this->lblFileUploadsInfo->Text = ini_get('file_uploads') ? '<i class="fa fa-check text-success" aria-hidden="true"></i>' : '<i class="fa fa-times text-error" aria-hidden="true"></i>';
            $this->lblFileUploadsInfo->HtmlEntities = false;

            $this->lblUploadMaxFilesize = new Q\Plugin\Control\Label($this);
            $this->lblUploadMaxFilesize->Text = 'upload_max_filesize:';
            $this->lblUploadMaxFilesize->addCssClass('col-md-4');
            $this->lblUploadMaxFilesize->setCssStyle('font-weight', '500');

            $this->lblUploadMaxFilesizeInfo = new Q\Plugin\Control\Label($this);
            $this->lblUploadMaxFilesizeInfo->Text = ini_get('upload_max_filesize') ?? null;
            $this->lblUploadMaxFilesizeInfo->setCssStyle('font-weight', 'normal');

            $this->lblPostMaxSize = new Q\Plugin\Control\Label($this);
            $this->lblPostMaxSize->Text = 'post_max_size:';
            $this->lblPostMaxSize->addCssClass('col-md-4');
            $this->lblPostMaxSize->setCssStyle('font-weight', '500');

            $this->lblPostMaxSizeInfo = new Q\Plugin\Control\Label($this);
            $this->lblPostMaxSizeInfo->Text = ini_get('post_max_size') ?? null;
            $this->lblPostMaxSizeInfo->setCssStyle('font-weight', 'normal');

            $this->lblMaxFileUploads = new Q\Plugin\Control\Label($this);
            $this->lblMaxFileUploads->Text = 'max_file_uploads:';
            $this->lblMaxFileUploads->addCssClass('col-md-4');
            $this->lblMaxFileUploads->setCssStyle('font-weight', '500');

            $this->lblMaxFileUploadsInfo = new Q\Plugin\Control\Label($this);
            $this->lblMaxFileUploadsInfo->Text = ini_get('max_file_uploads') ?? null;
            $this->lblMaxFileUploadsInfo->setCssStyle('font-weight', 'normal');

            // Example: $this->objDiskSpace = new DiskSpaceCheck('/var/www/vhosts/');
            $this->objDiskSpace = new DiskSpaceCheck(dirname(__FILE__));

            $this->objPieChart = new SimplePieChart($this);
            $this->objPieChart->Data = [$this->objDiskSpace->used_space, $this->objDiskSpace->free_space];
            $this->objPieChart->Color = ['#ff7043', '#4caf50'];
            $this->objPieChart->Border = ['#ffffff', '#ffffff'];
            $this->objPieChart->BorderWidth = 3;
            $this->objPieChart->Width = 250;
            $this->objPieChart->Height = 250;

            $this->lblTotalDiskSpace = new Q\Plugin\Control\Label($this);
            $this->lblTotalDiskSpace->Text = t('Total disk space:');
            $this->lblTotalDiskSpace->addCssClass('col-md-4');
            $this->lblTotalDiskSpace->setCssStyle('font-weight', '500');

            $this->lblTotalDiskSpaceInfo = new Q\Plugin\Control\Label($this);
            $this->lblTotalDiskSpaceInfo->Text = $this->objDiskSpace->formatBytes($this->objDiskSpace->total_space) . ' / 100%' ?? null;
            $this->lblTotalDiskSpaceInfo->setCssStyle('font-weight', 'normal');

            $this->lblDiskUsedSpace = new Q\Plugin\Control\Label($this);
            $this->lblDiskUsedSpace->Text = t('Used disk space:');
            $this->lblDiskUsedSpace->addCssClass('col-md-4');
            $this->lblDiskUsedSpace->setCssStyle('font-weight', '500');

            $this->lblDiskUsedSpaceInfo = new Q\Plugin\Control\Label($this);
            $this->lblDiskUsedSpaceInfo->Text = $this->objDiskSpace->formatBytes($this->objDiskSpace->used_space) . ' / ' . round($this->objDiskSpace->percent) . '%' ?? null;
            $this->lblDiskUsedSpaceInfo->setCssStyle('font-weight', 'normal');

            $this->lblDiskFreeSpace = new Q\Plugin\Control\Label($this);
            $this->lblDiskFreeSpace->Text = t('Free disk space:');
            $this->lblDiskFreeSpace->addCssClass('col-md-4');
            $this->lblDiskFreeSpace->setCssStyle('font-weight', '500');

            $this->lblDiskFreeSpaceInfo = new Q\Plugin\Control\Label($this);
            $this->lblDiskFreeSpaceInfo->Text = $this->objDiskSpace->formatBytes($this->objDiskSpace->free_space) . ' / ' . round($this->objDiskSpace->free_percent) . '%' ?? null;
            $this->lblDiskFreeSpaceInfo->setCssStyle('font-weight', 'normal');
        }
    }