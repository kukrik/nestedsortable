<div class="form-horizontal">
    <div class="row">
        <div class="table-top-heading">
            <span class="vauu-title-3"><?php _t('Sports content types options') ?></span>
        </div>
    </div>
    <div class="table-body">
        <div class="row">
            <div class="col-md-1"><?= _r($this->lstItemsPerPageByAssignedUserObject); ?></div>
            <div class="col-md-3" style="margin-top: -7px;"><?= _r($this->txtFilter); ?></div>
            <div class="col-md-2" style="text-align: left;"><?= _r($this->btnClearFilters); ?></div>
            <div class="col-md-6" style="text-align: right; margin-bottom: 15px;"><?= _r($this->dtgSportsContentTypes->Paginator); ?></div>
        </div>
        <?= _r($this->dtgSportsContentTypes); ?>
        <div class="row">
            <div class="col-md-12">
                <?= _r($this->btnAddType); ?>
                <?= _r($this->txtType); ?>
                <?= _r($this->lstStatus); ?>
                <?= _r($this->btnSaveType); ?>
                <?= _r($this->btnSave); ?>
                <?= _r($this->btnDelete); ?>
                <?= _r($this->btnCancel); ?>
            </div>
        </div>
    </div>
</div>










