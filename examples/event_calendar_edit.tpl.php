<?php $strPageTitle = t('Calendar event edit'); ?>
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
                    <h3 class="vauu-title-3 margin-left-0"><?php _t('Editing event calendar') ?></h3>
                </div>
                <div class="panel-body">
                    <div class="form-horizontal" style="padding: 0 5px;">
                        <div class="row equal">
                            <div class="col-md-9 left-box padded-wrapper">
                                <div class="form-group">
                                    <?= _r($this->lblYear); ?>
                                    <div class="col-md-4">
                                        <?= _r($this->txtYear); ?>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <?= _r($this->lblTitle); ?>
                                    <div class="col-md-8">
                                        <?= _r($this->txtTitle); ?>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <?= _r($this->lblChanges); ?>
                                    <div class="col-md-8">
                                        <?= _r($this->lstChanges); ?>
                                        <?= _r($this->btnGoToChanges); ?>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <?= _r($this->lblTargetGroup); ?>
                                    <div class="col-md-8">
                                        <?= _r($this->lstTargetGroup); ?>
                                        <?= _r($this->btnGoToTargetGroup); ?>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <?= _r($this->lblEventPlace); ?>
                                    <div class="col-md-8">
                                        <?= _r($this->txtEventPlace); ?>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <?= _r($this->lblEventDate); ?>
                                    <div class="col-md-4">
                                      <?= _r($this->calBeginningEvent); ?>
                                    </div>
                                    <div class="col-md-4">
                                        <?= _r($this->calEndEvent); ?>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <div class="col-md-4 col-md-offset-3">
                                        <?= _r($this->calStartTime); ?>
                                    </div>
                                    <div class="col-md-4">
                                        <?= _r($this->calEndTime); ?>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <?= _r($this->lblWebsiteUrl); ?>
                                    <div class="col-md-5">
                                        <?= _r($this->txtWebsiteUrl); ?>
                                    </div>
                                    <div class="col-md-3">
                                        <?= _r($this->lstWebsiteTargetType); ?>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <?= _r($this->lblFacebookUrl); ?>
                                    <div class="col-md-5">
                                        <?= _r($this->txtFacebookUrl); ?>
                                    </div>
                                    <div class="col-md-3">
                                        <?= _r($this->lstFacebookTargetType); ?>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <?= _r($this->lblInstagramUrl); ?>
                                    <div class="col-md-5">
                                        <?= _r($this->txtInstagramUrl); ?>
                                    </div>
                                    <div class="col-md-3">
                                        <?= _r($this->lstInstagramTargetType); ?>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <?= _r($this->lblContact); ?>
                                    <div class="col-md-8">
                                        <?= _r($this->txtOrganizers); ?>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <div class="col-md-offset-3 col-md-8">
                                        <?= _r($this->txtPhone); ?>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <div class="col-md-offset-3 col-md-8">
                                        <?= _r($this->txtEmail); ?>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <?= _r($this->lblGroupTitle); ?>
                                    <div class="col-md-8">
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
                                    <div class="col-md-12" style="margin-top: 15px; text-align: right;">
                                        <?= _r($this->btnSelectedSave); ?>
                                        <?= _r($this->btnSelectedCheck); ?>
                                        <?= _r($this->btnSelectedDelete); ?>
                                        <?= _r($this->btnSelectedCancel); ?>
                                    </div>
                                </div>
                                <script>
                                    const bsCssPath = <?= json_encode(QCUBED_BOOTSTRAP_CSS, JSON_UNESCAPED_UNICODE); ?>;
                                    ckConfig = {
                                        skin: 'moono',
                                        width: '100%',
                                        height: '100px',
                                        extraPlugins: 'colorbutton,font,justify,print,tableresize,pastefromword,liststyle,dialogadvtab,colordialog',
                                        contentsCss: bsCssPath,
                                        toolbar: [
                                            { name: 'clipboard', items: [ 'PasteFromWord', '-', 'Undo', 'Redo' ] },
                                            { name: 'basicstyles', items: [ 'Bold', 'Italic', 'Underline', 'Strike', 'RemoveFormat', 'Subscript', 'Superscript' ] },
                                            { name: 'insert', items: [ 'Image', 'Table' ] },
                                            { name: 'paragraph', items: [ 'NumberedList', 'BulletedList', '-', 'Outdent', 'Indent', '-', 'Blockquote' ] },
                                            { name: 'links', items: [ 'Link', 'Unlink' ] },
                                            '/',
                                            { name: 'styles', items: [ 'Format', 'Font', 'FontSize' ] },
                                            { name: 'colors', items: [ 'TextColor', 'BGColor', 'CopyFormatting' ] },
                                            { name: 'align', items: [ 'JustifyLeft', 'JustifyCenter', 'JustifyRight', 'JustifyBlock' ] },
                                            { name: 'document', items: [ 'Print', 'Source' ] }
                                        ]
                                    };
                                    ckConfig.removePlugins = 'link,image';
                                </script>
                                <div class="form-group">
                                    <div class="col-md-12">
                                        <?= _r($this->lblInformation); ?>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <div class="col-md-12">
                                        <?= _r($this->txtInformation); ?>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <div class="col-md-12">
                                        <?= _r($this->lblSchedule); ?>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <div class="col-md-12">
                                        <?= _r($this->txtSchedule); ?>
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
                                    <?= _r($this->lblAuthor); ?>
                                    <?= _r($this->txtAuthor); ?>
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






