<style>
    .form-actions-wrapper  {display: block; background-color: #f5f5f5; border-radius: 4px; margin: 0 -15px; padding: 15px; text-align: left;}
    .form-horizontal .radio-inline {/*padding-top: 18px;*/margin-top: 0;margin-bottom: 0;}
    .setting-wrapper {margin-top: 0; padding: 30px 0 10px;}
    .link-wrapper {margin-top: 15px;/*text-align: left;*/}
</style>
<div class="form-horizontal">
    <div class="row">
        <div class="panel-heading">
            <span class="vauu-title-3"><?php _t('Managing record holders') ?></span>
        </div>
    </div>
    <div class="js-wrapper-top"></div>
    <div class="tab-content-body">
        <div class="row equal">
            <div class="col-md-9 left-box padded-wrapper">
                <div class="row">
                    <div class="col-md-12">
                        <div class="add-wrapper">
                            <?= _r($this->btnAddNewRecordsHolder); ?>
                            <?= _r($this->btnRefresh); ?>
                        </div>
                    </div>
                </div>
                <div class="setting-wrapper hidden">
                    <div class="row">
                        <div class="col-md-12">
                            <div class="form-group">
                                <?= _r($this->lblFirstName); ?>
                                <div class="col-md-5">
                                    <?= _r($this->txtFirstName); ?>
                                </div>
                            </div>
                            <div class="form-group">
                                <?= _r($this->lblLastName); ?>
                                <div class="col-md-5">
                                    <?= _r($this->txtLastName); ?>
                                </div>
                            </div>
                            <div class="form-group">
                                <?= _r($this->lblBirthDate); ?>
                                <div class="col-md-5">
                                    <div class="input-group" role="group">
                                    <?= _r($this->dtxBirthDate); ?>
                                    <?= _r($this->btnBirthDate); ?>
                                    </div>
                                </div>
                            </div>
                            <div class="form-group">
                                <?= _r($this->lblGender); ?>
                                <div class="col-md-3">
                                    <?= _r($this->lstGender); ?>
                                </div>
                            </div>
                            <div class="form-group">
                                <?= _r($this->lblStatus); ?>
                                <div class="col-md-5">
                                    <?= _r($this->lstStatus); ?>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-12">
                    <div class="form-actions-wrapper hidden" style="text-align: right;">
                        <?= _r($this->btnSave); ?>
                        <?= _r($this->btnDelete); ?>
                        <?= _r($this->btnCancel); ?>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-12">
                        <div class="link-wrapper">
                            <?= _r($this->lblInfo); ?>
                            <div class="table-body">
                                <div class="row">
                                    <div class="col-md-1"><?= _r($this->lstItemsPerPageByAssignedUserObject); ?></div>
                                    <div class="col-md-3" style="margin-top: -7px;"><?= _r($this->txtFilter); ?></div>
                                    <div class="col-md-2" style="text-align: left;"><?= _r($this->btnClearFilters); ?></div>
                                    <div class="col-md-6" style="text-align: right; margin-bottom: 15px;"><?= _r($this->dtgAthletes->Paginator); ?></div>
                                </div>
                                <?= _r($this->dtgAthletes); ?>
                            </div>
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
                    <?= _r($this->lblAuthor); ?>
                    <?= _r($this->txtAuthor); ?>
                </div>
                <div class="form-group">
                    <?= _r($this->lblUsersAsEditors); ?>
                    <?= _r($this->txtUsersAsEditors); ?>
                </div>
            </div>
        </div>
    </div>
</div>