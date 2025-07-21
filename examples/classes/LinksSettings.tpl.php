<div class="form-horizontal">
    <div class="row">
        <div class="table-top-heading">
            <span class="vauu-title-3"><?php _t('Links group settings') ?></span>
        </div>
    </div>
    <div class="table-body">
        <div class="row">
            <div class="col-md-1"><?= _r($this->lstItemsPerPageByAssignedUserObject); ?></div>
            <div class="col-md-3" style="margin-top: -7px;"><?= _r($this->txtFilter); ?></div>
            <div class="col-md-8" style="text-align: right; margin-bottom: 15px;"><?= _r($this->dtgLinksGroups->Paginator); ?></div>
        </div>
        <?= _r($this->dtgLinksGroups); ?>
        <div class="row">
            <div class="col-md-12">
                <?= _r($this->btnGoToLinks); ?>
                <?= _r($this->txtLinksGroup); ?>
                <?= _r($this->txtLinksTitle); ?>
                <?= _r($this->btnSave); ?>
                <?= _r($this->btnCancel); ?>
            </div>
        </div>
    </div>
</div>









