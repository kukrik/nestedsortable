<style>
    .text-success {
        color: #4caf50;
        font-size: 1.33333333em;
        line-height: 0.5em;
        vertical-align: -15%;
    }
    .text-error {
        color: #ff0000;
        font-size: 1.33333333em;
        line-height: 0.5em;
        vertical-align: -15%;
    }
    .disk-space-wrapper {
        display: block;
        margin: 30px auto;
        text-align: center;
    }
</style>
<div class="form-horizontal">
    <div class="form-body">
        <div class="row">
            <div class="col-md-6" style="border-right: 1px solid #ccc;">
                <div class="form-group">
                    <?= _r($this->lblPhpVersion); ?>
                    <div class="col-md-8">
                        <?= _r($this->lblPhpVersionInfo); ?>
                    </div>
                </div>
                <div class="form-group">
                    <?= _r($this->lblDatabase); ?>
                    <div class="col-md-8">
                        <?= _r($this->lblDatabaseInfo); ?>
                    </div>
                </div>
                <div class="form-group">
                    <?= _r($this->lblDatabaseCharset); ?>
                    <div class="col-md-8">
                        <?= _r($this->lblDatabaseCharsetInfo); ?>
                    </div>
                </div>
                <div class="form-group">
                    <?= _r($this->lblDatabaseCollation); ?>
                    <div class="col-md-8">
                        <?= _r($this->lblDatabaseCollationInfo); ?>
                    </div>
                </div>
                <div class="form-group">
                    <?= _r($this->lblWebServer); ?>
                    <div class="col-md-8">
                        <?= _r($this->lblWebServerInfo); ?>
                    </div>
                </div>
                <div class="col-md-12" style="height: 20px;"></div>
                <div class="form-group">
                    <?= _r($this->lblGDSupport); ?>
                    <div class="col-md-8">
                        <?= _r($this->lblGDSupportInfo); ?>
                    </div>
                </div>
                <div class="form-group">
                    <?= _r($this->lblMbstring); ?>
                    <div class="col-md-8">
                        <?= _r($this->lblMbstringInfo); ?>
                    </div>
                </div>
                <div class="form-group">
                    <?= _r($this->lblZipArchive); ?>
                    <div class="col-md-8">
                        <?= _r($this->lblZipArchiveInfo); ?>
                    </div>
                </div>
                <div class="col-md-12" style="height: 20px;"></div>

                <div class="form-group">
                    <?= _r($this->lblMemoryLimit); ?>
                    <div class="col-md-8">
                        <?= _r($this->lblMemoryLimitInfo); ?>
                    </div>
                </div>
                <div class="form-group">
                    <?= _r($this->lblFileUploads); ?>
                    <div class="col-md-8">
                        <?= _r($this->lblFileUploadsInfo); ?>
                    </div>
                </div>
                <div class="form-group">
                    <?= _r($this->lblUploadMaxFilesize); ?>
                    <div class="col-md-8">
                        <?= _r($this->lblUploadMaxFilesizeInfo); ?>
                    </div>
                </div>
                <div class="form-group">
                    <?= _r($this->lblPostMaxSize); ?>
                    <div class="col-md-8">
                        <?= _r($this->lblPostMaxSizeInfo); ?>
                    </div>
                </div>
                <div class="form-group">
                    <?= _r($this->lblMaxFileUploads); ?>
                    <div class="col-md-8">
                        <?= _r($this->lblMaxFileUploadsInfo); ?>
                    </div>
                </div>
            </div>
            <div class="col-md-6">
                <div class="col-md-12">
                    <div class="disk-space-wrapper">
                        <?= _r($this->objPieChart); ?>
                    </div>
                    <div class="col-md-12" style="height: 20px;"></div>
                    <div class="form-group">
                        <?= _r($this->lblTotalDiskSpace); ?>
                        <div class="col-md-8">
                            <?= _r($this->lblTotalDiskSpaceInfo); ?>
                        </div>
                    </div>
                    <div class="form-group">
                        <?= _r($this->lblDiskUsedSpace); ?>
                        <div class="col-md-8">
                            <?= _r($this->lblDiskUsedSpaceInfo); ?>
                        </div>
                    </div>
                    <div class="form-group">
                        <?= _r($this->lblDiskFreeSpace); ?>
                        <div class="col-md-8">
                            <?= _r($this->lblDiskFreeSpaceInfo); ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>