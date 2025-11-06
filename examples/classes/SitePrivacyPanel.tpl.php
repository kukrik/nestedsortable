
<div class="form-horizontal">
    <div class="form-body">
        <div class="form-group">
            <?= _r($this->lblPrivacyPolicyTitle); ?>
            <div class="col-md-5">
                <?= _r($this->txtPrivacyPolicyTitle); ?>
            </div>
        </div>

        <div class="form-group">
            <?= _r($this->lblGoogleAnalyticsCode); ?>
            <div class="col-md-5">
                <?= _r($this->txtGoogleAnalyticsCode); ?>
            </div>
        </div>

        <div class="form-group">
            <?= _r($this->lblPrivacyPolicyLink); ?>
            <div class="col-md-9">
                <?= _r($this->btnDocumentLink); ?>
                <?= _r($this->txtDocumentLink); ?>
                <?= _r($this->lblPrivacyFileName); ?>
            </div>
        </div>
        <div class="row">
            <div class="col-md-8" style="text-align: right;">
                <?= _r($this->btnDownloadSave); ?>
                <?= _r($this->btnDownloadCancel); ?>
                <?= _r($this->btnDocumentDelete); ?>
                <?= _r($this->btnDocumentCheck); ?>
            </div>
        </div>


        <div class="form-group">
            <?= _r($this->lblPrivacyPolicy); ?>
            <div class="col-md-5">
                <?= _r($this->btnShow); ?>
                <?= _r($this->btnHide); ?>
            </div>
        </div>

        <script>
            //const dialogPath = <?= json_encode(dirname(QCUBED_FILEMANAGER_ASSETS_URL), JSON_UNESCAPED_UNICODE); ?>;
            const bsCssPath = <?= json_encode(QCUBED_BOOTSTRAP_CSS, JSON_UNESCAPED_UNICODE); ?>;
            ckConfig = {
                skin: 'moono',
                width: '100%',
                height: '350px',
                //language: 'en',
                extraPlugins: 'colorbutton,font,justify,print,tableresize,pastefromword,liststyle,dialogadvtab,colordialog',
                contentsCss: bsCssPath,

                entities: false, // Keelab HTML entiteetide automaatse lisamise
                entities_latin: false, // Keelab ladina tähestiku konversioonid
                entities_greek: false, // Keelab kreeka tähestiku konversioonid

                // Määra sisendi keele kodeering
                basicEntities: false, // Väldi baasteisendusi (nt '&' -> '&amp;')

                // Salvesta UTF-8 formaadis
                htmlEncodeOutput: false, // Keelab väljundi kodeerimise entiteetideks
                htmlEncodeEntities: false, // Väldi ka väljundi html entiteete

                toolbar: [
                    { name: 'clipboard', items: [ 'PasteFromWord', '-', 'Undo', 'Redo' ] },
                    { name: 'basicstyles', items: [ 'Bold', 'Italic', 'Underline', 'Strike', 'RemoveFormat', 'Subscript', 'Superscript' ] },
                    { name: 'paragraph', items: [ 'NumberedList', 'BulletedList', '-', 'Outdent', 'Indent', '-', 'Blockquote' ] },
                    //{ name: 'insert', items: [ 'Image', 'Table' ] },
                    { name: 'links', items: [ 'Link', 'Unlink' ] },
                    '/',
                    { name: 'styles', items: [ 'Format', 'Font', 'FontSize' ] },
                    { name: 'colors', items: [ 'TextColor', 'BGColor', 'CopyFormatting' ] },
                    { name: 'align', items: [ 'JustifyLeft', 'JustifyCenter', 'JustifyRight', 'JustifyBlock' ] },
                    { name: 'document', items: [ 'Print', 'Source' ] }
                ]
            };
        </script>

        <div class="form-group">
            <div class="col-md-12">
                <?= _r($this->txtContent); ?>
            </div>
        </div>

        <div class="form-actions fluid">
            <div class="col-md-offset-3 col-md-9">
                <?= _r($this->btnSave); ?>
                <?= _r($this->btnCancel); ?>
            </div>
        </div>




    </div>
</div>