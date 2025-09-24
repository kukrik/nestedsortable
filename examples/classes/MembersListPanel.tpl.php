<div class="form-horizontal">
    <div class="row">
        <div class="table-top-heading">
            <span class="vauu-title-3"><?php _t('Members manager') ?></span>
        </div>
    </div>
    <div class="table-body">
        <div class="row">
            <div class="col-md-1"><?= _r($this->lstItemsPerPageByAssignedUserObject); ?></div>
            <div class="col-md-3" style="margin-top: -7px;"><?= _r($this->txtFilter); ?></div>
            <div class="col-md-2" style="text-align: left;"><?= _r($this->btnClearFilters); ?></div>
            <div class="col-md-6" style="text-align: right; margin-bottom: 15px;"><?= _r($this->dtgMembers->Paginator); ?></div>
        </div>
        <?= _r($this->dtgMembers); ?>
        <div class="row">
            <div class="col-md-3"><?= _r($this->btnRefresh); ?> <?= _r($this->btnBack); ?></div>
            <div class="col-md-9" style="text-align: right;">
                <?= _r($this->dtgMembers->PaginatorAlternate); ?>
            </div>
        </div>
    </div>
</div>