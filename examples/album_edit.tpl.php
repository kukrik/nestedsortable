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
                                                <?= _r($this->btnGoUpload); ?>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="album-fileinfo-wrapper">
                                        <div class="form-group">
                                            <?= _r($this->lblTitle); ?>
                                            <div class="col-md-7">
                                                <?= _r($this->txtTitle); ?>
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
                                    </div>
                                    <div class="table-body" style="margin-bottom: -20px;">
                                        <?= _r($this->dtgAlbumList); ?>
                                    </div>
                                    <div class="table-body-alert">
                                        <?= _r($this->lblInfo); ?>
                                    </div>
                                    <div class="album-tools-wrapper">
                                        <div class="form-group">
                                            <div class="col-md-12">
                                                <?= _r($this->btnAlbumSave); ?>
                                                <?= _r($this->btnAlbumDelete); ?>
                                                <?= _r($this->btnAlbumCancel); ?>
                                            </div>
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