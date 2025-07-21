<?php $strPageTitle = t('Board edit'); ?>

<?php require('header.inc.php'); ?>
<?php $this->RenderBegin(); ?>

    <style>
        .form-actions-wrapper  {display: block; background-color: #f5f5f5; border-radius: 4px; margin: 0 -15px; padding: 15px; text-align: left;}
        .form-actions  {display: block; background-color: #f5f5f5; border-radius: 4px; margin: 15px -15px 0 -15px; padding: 15px;}
        .form-horizontal .radio-inline {/*padding-top: 18px;*/margin-top: 0;margin-bottom: 0;}
        .edit.radio-inline {padding-top: 18px;margin-top: 0;margin-bottom: 0;}
        .board-add-wrapper {margin-top: -15px;}


        .slug-wrapper {margin: 15px 0;padding: 10px 0;border-top: #ddd 1px solid;}
        .board-image-wrapper {margin-top: 20px;padding: 20px 0;border-top: #ddd 1px solid;text-align: center;}
        .board-setting-wrapper {margin-top: 15px;padding: 30px 0 10px;border-top: #ddd 1px solid;}
        .image-wrapper {margin-top: 15px;/*text-align: left;*/}

        .sortable div.activated {background-color: #eaffea; /*#ddffdd*/}
        .sortable div.inactivated {background-color: #fff0f1; /*#ffe8e8*/}
        .placeholder {height: 105px;outline: 1px dashed #4183C4;background: rgba(73, 182, 255, 0.07);border-radius: 3px;margin: -1px;}

        .image-blocks {display: block;padding: 10px;height: 95px;border-top: #ddd 1px solid;}
        .image-blocks:hover {background-color: #f6f6f6;}
        .icon-set:hover, .btn-icon:hover {background: #f6f6f6;color: inherit;text-decoration: none;border: #7d898d 1px solid;border-radius: 4px;}
        .preview {display: inline-block;width: 110px;}
        .preview img {display: inline-block;max-width: 75px;max-height: 75px;border-radius: 7px;}

        .events, .image-info {display: inline-block;vertical-align: middle;}
        .icon-set, .btn-icon {display: inline-block;font-size: 16px;color: #7d898d;background-color: transparent;width: 38px;padding: 7px;text-align: center;vertical-align: middle;cursor: pointer;}
        .js-validate-popup {position: relative;}
    </style>

    <div class="page-content">
        <div class="row">
            <div class="col-md-12">
                <div class="content-body">
                    <div class="panel-heading">
                        <h3 class="vauu-title-3 margin-left-0"><?php _t('Board edit: ') ?><?= _r($this->lblGroupTitle); ?></h3>
                    </div>
                    <div class="panel-body">
                        <div class="form-horizontal" style="padding: 0 5px;">
                            <div class="row equal">
                                <div class="col-md-9 left-box padded-wrapper">
                                    <div class="row">
                                        <div class="col-md-12">
                                            <div class="board-add-wrapper">
                                                <?= _r($this->btnAddMember); ?>
                                                <?= _r($this->txtNewFullName); ?>
                                                <?= _r($this->btnMemberSave); ?>
                                                <?= _r($this->btnMemberCancel); ?>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="slug-wrapper">
                                            <div class="col-md-12">
                                                <?= _r($this->lblTitleSlug); ?>
                                                <?= _r($this->txtTitleSlug); ?>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="js-board-wrapper"></div>
                                    <div class="row">
                                        <div class="board-image-wrapper hidden">
                                            <div class="col-md-3 col-md-offset-4">
                                                <?= _r($this->objMediaFinder); ?>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="board-setting-wrapper hidden">
                                        <div class="row">
                                            <?php foreach ($this->objActiveInputs as $input): ?>
                                                <?php if ($input->InputKey == 1): ?>
                                                    <div class="form-group js-fullname">
                                                        <?= _r($this->lblFullName); ?>
                                                        <div class="col-md-6">
                                                            <?= _r($this->txtFullName); ?>
                                                        </div>
                                                    </div>
                                                <?php elseif ($input->InputKey == 2): ?>
                                                    <div class="form-group js-position">
                                                        <?= _r($this->lblPosition); ?>
                                                        <div class="col-md-6">
                                                            <?= _r($this->txtPosition); ?>
                                                        </div>
                                                    </div>
                                                <?php elseif ($input->InputKey == 3): ?>
                                                    <div class="form-group js-areasresponsibility">
                                                        <?= _r($this->lblAreasResponsibility); ?>
                                                        <div class="col-md-6">
                                                            <?= _r($this->txtAreasResponsibility); ?>
                                                        </div>
                                                    </div>
                                                <?php elseif ($input->InputKey == 4): ?>
                                                    <div class="form-group js-interests">
                                                        <?= _r($this->lblInterests); ?>
                                                        <div class="col-md-6">
                                                            <?= _r($this->txtInterests); ?>
                                                        </div>
                                                    </div>
                                                <?php elseif ($input->InputKey == 5): ?>
                                                    <div class="form-group js-description">
                                                        <?= _r($this->lblDescription); ?>
                                                        <div class="col-md-6">
                                                            <?= _r($this->txtDescription); ?>
                                                        </div>
                                                    </div>
                                                <?php elseif ($input->InputKey == 6): ?>
                                                    <div class="form-group js-telephone">
                                                        <?= _r($this->lblTelephone); ?>
                                                        <div class="col-md-6">
                                                            <?= _r($this->txtTelephone); ?>
                                                        </div>
                                                    </div>
                                                <?php elseif ($input->InputKey == 7): ?>
                                                    <div class="form-group js-sms">
                                                        <?= _r($this->lblSMS); ?>
                                                        <div class="col-md-6">
                                                            <?= _r($this->txtSMS); ?>
                                                        </div>
                                                    </div>
                                                <?php elseif ($input->InputKey == 8): ?>
                                                    <div class="form-group js-fax">
                                                        <?= _r($this->lblFax); ?>
                                                        <div class="col-md-6">
                                                            <?= _r($this->txtFax); ?>
                                                        </div>
                                                    </div>
                                                <?php elseif ($input->InputKey == 9): ?>
                                                    <div class="form-group js-address">
                                                        <?= _r($this->lblAddress); ?>
                                                        <div class="col-md-6">
                                                            <?= _r($this->txtAddress); ?>
                                                        </div>
                                                    </div>
                                                <?php elseif ($input->InputKey == 10): ?>
                                                    <div class="form-group js-email">
                                                        <?= _r($this->lblEmail); ?>
                                                        <div class="col-md-6">
                                                            <?= _r($this->txtEmail); ?>
                                                        </div>
                                                    </div>
                                                <?php elseif ($input->InputKey == 11): ?>
                                                    <div class="form-group js-website">
                                                        <?= _r($this->lblWebsite); ?>
                                                        <div class="col-md-6">
                                                            <?= _r($this->txtWebsite); ?>
                                                        </div>
                                                    </div>
                                                <?php endif; ?>
                                            <?php endforeach; ?>
                                            <div class="form-group">
                                                <?= _r($this->lblBoardStatus); ?>
                                                <div class="col-md-6">
                                                    <?= _r($this->lstBoardStatus); ?>
                                                </div>
                                            </div>

                                        </div>
                                    </div>
                                    <div class="col-md-12">
                                        <div class="form-actions-wrapper hidden" style="text-align: right;">
                                            <?= _r($this->btnUpdate); ?>
                                            <?= _r($this->btnCloseWindow); ?>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-md-12">
                                            <div class="image-wrapper">
                                                <?= _r($this->lblInfo); ?>
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
                                        <?= _r($this->lblImageUpload); ?>
                                        <?= _r($this->lstImageUpload); ?>
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