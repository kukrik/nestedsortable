<?php $strPageTitle = t('Carousel edit'); ?>

<?php require('header.inc.php'); ?>
<?php $this->RenderBegin(); ?>

    <style>
        .svg-container img {height: 100%;}
        .bx-wrapper {margin: auto; margin-top: 15px;margin-bottom: 45px;}
        .bx-wrapper img {max-height: 330px;}
        .slider-wrapper {margin-top: -20px;}
        .refresh-wrapper {display: block; background-color: #f5f5f5; border-radius: 4px; margin: 0 -15px 25px -15px; padding: 15px; text-align: right;}
        .form-actions  {display: block; background-color: #f5f5f5; border-radius: 4px; margin: 15px -15px 0 -15px; padding: 15px; text-align: left;}
        .slider-setting-wrapper {display: block; border-bottom: #ddd 1px solid; padding-bottom: 15px;}
        .form-horizontal .radio-inline {/*padding-top: 18px;*/margin-top: 0;margin-bottom: 0;}
        .edit.radio-inline {padding-top: 18px;margin-top: 0;margin-bottom: 0;}
        .image-upload-wrapper {margin-top: 15px;}
        .image-wrapper {margin-top: 15px; text-align: left;}
        .svg-container img {height: 100%;}
        .sortable div.activated {background-color: #eaffea; /*#ddffdd*/}
        .sortable div.inactivated {background-color: #fff0f1; /*#ffe8e8*/}
        .placeholder {height: 105px;outline: 1px dashed #4183C4;background: rgba(73, 182, 255, 0.07);border-radius: 3px;margin: -1px;}
        .image-blocks {display: block;padding: 10px;height: 95px;border-top: #ddd 1px solid;}
        .image-blocks:hover {background-color: #f6f6f6;}
        .icon-set:hover, .btn-icon:hover {background: #f6f6f6;color: inherit;text-decoration: none;border: #7d898d 1px solid;border-radius: 4px;}
        .preview {display: inline-block;width: 110px;}
        .preview img {display: inline-block;max-width: 110px;max-height: 75px;border-radius: 7px;}
        .events, .image-info {display: inline-block;vertical-align: middle;}
        .icon-set, .btn-icon {display: inline-block;font-size: 16px;color: #7d898d;background-color: transparent;width: 38px;padding: 7px;text-align: center;vertical-align: middle;cursor: pointer;}
    </style>

    <div class="page-content">
        <div class="row">
            <div class="col-md-12">
                <div class="content-body">
                    <div class="panel-heading">
                        <h3 class="vauu-title-3 margin-left-0"><?php _t('Carousel edit') ?></h3>
                    </div>
                    <div class="panel-body">
                        <div class="form-horizontal" style="padding: 0 5px;">
                            <div class="row equal">
                                <div class="col-md-9 left-box padded-wrapper">
                                    <div class="col-md-12">
                                        <div class="slider-wrapper">
                                        <?= _r($this->objTestSlider); ?>
                                        </div>
                                    </div>
                                    <div class="col-md-12">
                                        <div class="refresh-wrapper">
                                            <?= _r($this->btnRefresh); ?>
                                        </div>
                                    </div>

                                    <div class="slider-setting-wrapper hidden">
                                        <div class="row">
                                            <div class="col-md-6">
                                                <?= _r($this->txtTitle); ?>
                                            </div>
                                            <div class="col-md-6">
                                                <?= _r($this->txtUrl); ?>
                                            </div>
                                        </div>

                                        <div class="row">
                                            <div class="col-md-2">
                                                <?= _r($this->lblDimensions); ?>
                                            </div>
                                            <div class="col-md-4">
                                                <?= _r($this->txtWidth); ?>
                                                <?= _r($this->lblCross); ?>
                                                <?= _r($this->txtHeight); ?>
                                            </div>
                                            <div class="col-md-6">
                                                <?= _r($this->txtTop); ?>
                                                <?= _r($this->lstStatusSlider); ?>
                                                <?= _r($this->calSliderPostUpdateDate); ?>
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="col-md-12" style="text-align: right">
                                                <?= _r($this->btnUpdate); ?>
                                                <?= _r($this->btnCancel); ?>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-md-12">
                                            <div class="image-upload-wrapper">
                                                <?= _r($this->btnAddImage); ?>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-md-12">
                                            <div class="image-wrapper">
                                                <?= _r($this->dlgSorter); ?>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-12">
                                        <div class="form-actions">
                                            <?= _r($this->btnBack); ?>
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