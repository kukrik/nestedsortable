<style>
    .form-actions-wrapper  {display: block; background-color: #f5f5f5; border-radius: 4px; margin: 0 -15px; padding: 15px; text-align: left;}
    .form-actions  {display: block; background-color: #f5f5f5; border-radius: 4px; margin: 0 -15px 0 -15px; padding: 15px;}
    .form-horizontal .radio-inline {/*padding-top: 18px;*/margin-top: 0;margin-bottom: 0;}
    .add-wrapper {display: block; margin-top: -15px; padding: 0 0 15px; border-bottom: #ddd 1px solid;}
    .setting-wrapper {margin-top: 0; padding: 30px 0 10px;}
    .link-wrapper {margin-top: 15px;/*text-align: left;*/}
</style>
<div class="form-horizontal">
    <div class="row">
        <div class="panel-heading" style="margin-top: -15px;">
            <span class="vauu-title-3"><?php _t('Managing genders') ?></span>
        </div>
    </div>
    <div class="js-wrapper-top"></div>
    <div class="tab-content-body">
        <div class="row equal">
            <div class="col-md-9 left-box padded-wrapper">
                <div class="row">
                    <div class="col-md-12">
                        <div class="add-wrapper">
                            <?= _r($this->btnAddNewGender); ?>
                        </div>
                    </div>
                </div>
                <div class="setting-wrapper hidden">
                    <div class="row">
                        <div class="col-md-12">
                            <div class="form-group">
                                <?= _r($this->lblName); ?>
                                <div class="col-md-5">
                                    <?= _r($this->txtName); ?>
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
                                <?= _r($this->dtgGenders); ?>
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