<div class="form-horizontal">
    <div class="row">
        <div class="table-top-heading">
            <span class="vauu-title-3"><?php _t('Sports calendar groups settings') ?></span>
        </div>
    </div>
    <div class="table-body">
        <div class="row">
            <div class="col-md-1"><?= _r($this->lstItemsPerPageByAssignedUserObject); ?></div>
            <div class="col-md-3" style="margin-top: -7px;"><?= _r($this->txtFilter); ?></div>
            <div class="col-md-8" style="text-align: right; margin-bottom: 15px;"><?= _r($this->dtgSportsGroups->Paginator); ?></div>
        </div>
        <?= _r($this->dtgSportsGroups); ?>
        <div class="row">
            <div class="col-md-12">
                <?= _r($this->btnGoToCalendar); ?>
                <?= _r($this->txtSportsGroup); ?>
                <?= _r($this->txtSportsTitle); ?>
                <?= _r($this->btnSave); ?>
                <?= _r($this->btnCancel); ?>
            </div>
        </div>
    </div>
</div>