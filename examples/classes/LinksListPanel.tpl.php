<div class="form-horizontal">
    <div class="row">
        <div class="table-top-heading">
            <span class="vauu-title-3"><?php _t('Links manager') ?></span>
        </div>
    </div>
    <div class="row">
        <div class="buttons-heading">
            <div class="row">
                <div class="col-md-1 move-button-js"><?= _r($this->btnMove); ?></div>
                <div class="move-items-js">
                    <div class="col-md-3"><?= _r($this->lstLinksLocked); ?></div>
                    <div class="col-md-3"><?= _r($this->lstTargetGroup); ?></div>
                    <div class="col-md-3"><?= _r($this->btnLockedCancel); ?></div>
                </div>
            </div>
        </div>
    </div>
    <div class="table-body">
        <div class="row">
            <div class="col-md-1"><?= _r($this->lstItemsPerPageByAssignedUserObject); ?></div>
            <div class="col-md-3" style="margin-top: -7px;"><?= _r($this->txtFilter); ?></div>
            <div class="col-md-8" style="text-align: right; margin-bottom: 15px;"><?= _r($this->dtgLinks->Paginator); ?></div>
        </div>
        <?= _r($this->dtgLinks); ?>
        <div class="row">
            <div class="col-md-3"><?= _r($this->btnBack); ?></div>
            <div class="col-md-9" style="text-align: right;"><?= _r($this->dtgLinks->PaginatorAlternate); ?></div>
        </div>
    </div>
</div>