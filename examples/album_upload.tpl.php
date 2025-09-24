<?php $strPageTitle = t('Album edit'); ?>
<?php require('header.inc.php'); ?>
<?php $this->RenderBegin(); ?>
    <div class="page-content">
        <div class="row">
            <div class="col-md-12">
                <div class="content-body">
                    <div class="panel-heading">
                        <h3 class="vauu-title-3 margin-left-0"><?php _t('Album edit') ?></h3>
                    </div>
                    <div class="panel-body">
                        <div class="form-horizontal" style="margin: 0 5px;">
                            <div class="row equal">
                                <div class="col-md-9 left-box padded-wrapper">
                                    <div class="galleryupload-buttonbar">
                                        <div class="row">
                                            <div class="col-md-12">
                                                <?= _r($this->btnAddFiles); ?>
                                                <?= _r($this->btnAllStart); ?>
                                                <?= _r($this->btnAllCancel); ?>
                                                <?= _r($this->btnBack); ?>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="upload-wrapper">
                                        <div class="row">
                                            <div class="col-md-12">
                                                <div id="alert-wrapper"></div>
                                                <div class="alert-multi-wrapper"></div>
                                                <?= _r($this->objUpload); ?>
                                                <div class="fileupload-donebar hidden">
                                                    <?= _r($this->btnDone); ?>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-3 right-box padded-wrapper disabled">
                                    <div class="form-group">
                                        <?= _r($this->lblPostDate); ?>
                                        <?= _r($this->calPostDate); ?>
                                    </div>
                                    <div class="form-group">
                                        <?= _r($this->lblPostUpdateDate); ?>
                                        <?= _r($this->calPostUpdateDate); ?>
                                    </div>
                                    <div class="form-group">
                                        <?= _r($this->lblInserter); ?>
                                        <?= _r($this->txtInserter); ?>
                                    </div>
                                    <div class="form-group">
                                        <?= _r($this->lblUsersAsEditors); ?>
                                        <?= _r($this->txtUsersAsEditors); ?>
                                    </div>
                                    <div class="form-group">
                                        <?= _r($this->lblPhotoDescription); ?>
                                        <?= _r($this->txtPhotoDescription); ?>
                                        <?= _r($this->lblPhotoAuthor); ?>
                                        <?= _r($this->txtPhotoAuthor); ?>
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