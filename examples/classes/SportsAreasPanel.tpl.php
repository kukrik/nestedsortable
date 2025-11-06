<style>
    .form-actions  {display: block; background-color: #f5f5f5; border-radius: 4px; margin: 0; padding: 15px;}
    .form-horizontal .radio-inline {/*padding-top: 18px;*/margin-top: 0;margin-bottom: 0;}
    .setting-wrapper {margin-top: 0; padding: 30px 0 10px; scroll-margin: 0;}
    .sports-area-add-wrapper {margin: 15px 0; padding: 0 15px 15px; border-bottom: #ddd 1px solid;}
</style>
<div class="form-horizontal">
    <div class="row">
        <div class="panel-heading">
            <span class="vauu-title-3"><?php _t('Sports areas settings') ?></span>
        </div>
    </div>
    <div class="sports-area-add-wrapper">
        <div class="row">
            <?= _r($this->btnAddSportsArea); ?>
            <?= _r($this->btnRefresh); ?>
            <?= _r($this->btnGoToEvents); ?>
        </div>
    </div>
    <div class="setting-wrapper hidden">
        <div class="row">
            <div class="col-md-12">
                <div class="form-group">
                    <?= _r($this->lblSportsArea); ?>
                    <div class="col-md-4">
                        <?= _r($this->txtSportsArea); ?>
                    </div>
                </div>
                <div class="form-group">
                    <?= _r($this->lblStatus); ?>
                    <div class="col-md-4">
                        <?= _r($this->lstStatus); ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-md-12">
            <div class="form-actions fluid hidden">
                <div class="col-md-offset-4 col-md-9">
                    <?= _r($this->btnSaveNew); ?>
                    <?= _r($this->btnSave); ?>
                    <?= _r($this->btnDelete); ?>
                    <?= _r($this->btnCancel); ?>
                </div>
            </div>
        </div>
    </div>
    <div class="table-body">
        <div class="row">
            <div class="col-md-1"><?= _r($this->lstItemsPerPageByAssignedUserObject); ?></div>
            <div class="col-md-3" style="margin-top: -7px;"><?= _r($this->txtFilter); ?></div>
            <div class="col-md-2" style="text-align: left;"><?= _r($this->btnClearFilters); ?></div>
            <div class="col-md-6" style="text-align: right; margin-bottom: 15px;"><?= _r($this->dtgSportsAreas->Paginator); ?></div>
        </div>
        <?= _r($this->dtgSportsAreas); ?>
    </div>
</div>









