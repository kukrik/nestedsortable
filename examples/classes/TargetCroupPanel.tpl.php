<style>#c28_col_1 {pointer-events: none;}</style>
<div class="form-horizontal">
    <div class="row">
        <div class="table-top-heading">
            <span class="vauu-title-3"><?php _t('Target groups options') ?></span>
        </div>
    </div>
    <div class="table-body">
        <div class="row">
            <div class="col-md-1"><?= _r($this->lstItemsPerPageByAssignedUserObject); ?></div>
            <div class="col-md-3" style="margin-top: -7px;"><?= _r($this->txtFilter); ?></div>
            <div class="col-md-2" style="text-align: left;"><?= _r($this->btnClearFilters); ?></div>
            <div class="col-md-6" style="text-align: right; margin-bottom: 15px;"><?= _r($this->dtgTargetGroup->Paginator); ?></div>
        </div>
        <?= _r($this->dtgTargetGroup); ?>
        <div class="row">
            <div class="col-md-12">
                <?= _r($this->btnAddTargetGroup); ?>
                <?= _r($this->btnGoToEvents); ?>
                <?= _r($this->txtTargetGroup); ?>
                <?= _r($this->lstStatus); ?>
                <?= _r($this->btnSaveNew); ?>
                <?= _r($this->btnSave); ?>
                <?= _r($this->btnDelete); ?>
                <?= _r($this->btnCancel); ?>
            </div>
        </div>
    </div>
</div>