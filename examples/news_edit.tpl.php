<?php $strPageTitle = t('News edit'); ?>
<?php require('header.inc.php'); ?>
<?php $this->RenderBegin(); ?>
    <style>
        .select2-container--web-vauu .select2-results__option[aria-disabled=true] {display: none;}
        .vauu-table tbody > tr:first-child td  {border-top: 1px solid #ddd;}
    </style>
    <div class="page-content">
        <div class="row">
            <div class="col-md-12">
                <div class="content-body">
                    <div class="panel-heading">
                        <h3 class="vauu-title-3 margin-left-0"><?php _t('News edit') ?></h3>
                    </div>
                    <div class="panel-body">
                        <div class="form-horizontal" style="padding: 0 5px;">
                            <div class="row equal">
                                <div class="col-md-9 left-box padded-wrapper">
                                    <div class="form-group">
                                        <?= _r($this->lblTitle); ?>
                                        <div class="col-md-7">
                                            <?= _r($this->txtTitle); ?>
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        <?= _r($this->lblChanges); ?>
                                        <div class="col-md-7">
                                            <?= _r($this->lstChanges); ?>
                                            <?= _r($this->btnGoToChanges); ?>
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        <?= _r($this->lblNewsCategory); ?>
                                        <div class="col-md-7">
                                            <?= _r($this->lstNewsCategory); ?>
                                            <?= _r($this->btnGoToCategories); ?>
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        <?= _r($this->lblGroupTitle); ?>
                                        <div class="col-md-7">
                                            <?= _r($this->lstGroupTitle); ?>
                                            <?= _r($this->btnGoToSettings); ?>
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        <?= _r($this->lblTitleSlug); ?>
                                        <div class="col-md-9">
                                            <?= _r($this->txtTitleSlug); ?>
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        <?= _r($this->lblDocumentLink); ?>
                                        <div class="col-md-9">
                                            <?= _r($this->btnDocumentLink); ?>
                                            <?= _r($this->txtDocumentLink); ?>
                                            <?= _r($this->txtLinkTitle); ?>
                                            <?= _r($this->btnDownloadSave); ?>
                                            <?= _r($this->btnDownloadCancel); ?>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-md-12">
                                            <?= _r($this->dtgSelectedList); ?>
                                            <div class="col-md-offset-3 col-md-6">
                                                <?= _r($this->txtSelectedTitle); ?>
                                            </div>
                                            <div class="col-md-3">
                                                <?= _r($this->lstSelectedStatus); ?>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-md-12" style="text-align: right;">
                                            <?= _r($this->btnSelectedSave); ?>
                                            <?= _r($this->btnSelectedCheck); ?>
                                            <?= _r($this->btnSelectedDelete); ?>
                                            <?= _r($this->btnSelectedCancel); ?>
                                        </div>
                                    </div>
                                    <script>
                                        const dialogPath = <?= json_encode(dirname(QCUBED_FILEMANAGER_ASSETS_URL), JSON_UNESCAPED_UNICODE); ?>;
                                        const bsCssPath = <?= json_encode(QCUBED_BOOTSTRAP_CSS, JSON_UNESCAPED_UNICODE); ?>;
                                        ckConfig = {
                                            skin: 'moono',
                                            width: '100%',
                                            height: '350px',
                                            //language: 'en',
                                            extraPlugins: 'colorbutton,font,justify,print,tableresize,pastefromword,liststyle,dialogadvtab,colordialog',
                                            filebrowserImageBrowseUrl: dialogPath + '/examples/dialog.php',
                                            filebrowserBrowseUrl: dialogPath + '/examples/dialog.php',
                                            filebrowserWindowWidth: '95%',
                                            filebrowserWindowHeight: '95%',
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
                                                { name: 'insert', items: [ 'Image', 'Table' ] },
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
                                    <div class="form-group padded-form-actions">
                                        <div class="col-md-12">
                                            <?= _r($this->btnSave); ?>
                                            <?= _r($this->btnSaving); ?>
                                            <?= _r($this->btnDelete); ?>
                                            <?= _r($this->btnCancel); ?>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-3 right-box padded-wrapper">
                                    <div class="form-group">
                                        <?= _r($this->lblPostDate); ?>
                                        <?= _r($this->calPostDate); ?>
                                    </div>
                                    <div class="form-group">
                                        <?= _r($this->lblPostUpdateDate); ?>
                                        <?= _r($this->calPostUpdateDate); ?>
                                    </div>
                                    <div class="form-group">
                                        <?= _r($this->lblNewsAuthor); ?>
                                        <?= _r($this->txtNewsAuthor); ?>
                                    </div>
                                    <div class="form-group">
                                        <?= _r($this->lblUsersAsEditors); ?>
                                        <?= _r($this->txtUsersAsEditors); ?>
                                    </div>
                                    <div class="form-group">
                                        <?= _r($this->objMediaFinder); ?>
                                    </div>
                                    <div class="form-group">
                                        <?= _r($this->lblPictureDescription); ?>
                                        <?= _r($this->txtPictureDescription); ?>
                                        <?= _r($this->lblAuthorSource); ?>
                                        <?= _r($this->txtAuthorSource); ?>
                                    </div>
                                    <div class="form-group">
                                        <?= _r($this->lblStatus); ?>
                                        <?= _r($this->lstStatus); ?>
                                    </div>
                                    <div class="form-group">
                                        <?= _r($this->lblUsePublicationDate); ?>
                                        <?= _r($this->chkUsePublicationDate); ?>
                                    </div>
                                    <div class="form-group">
                                        <?= _r($this->lblAvailableFrom); ?>
                                        <?= _r($this->calAvailableFrom); ?>
                                        <?= _r($this->lblExpiryDate); ?>
                                        <?= _r($this->calExpiryDate); ?>
                                    </div>
                                    <!--                                    <div class="form-group">-->
                                    <!--                                        --><?php //= _r($this->lblConfirmationAsking); ?>
                                    <!--                                        --><?php //= _r($this->chkConfirmationAsking); ?>
                                    <!--                                    </div>-->
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
<?php $this->RenderEnd(); ?>
<?php require('footer.inc.php'); ?>