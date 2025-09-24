<div class="form-horizontal">
    <div class="row">
        <div class="table-heading" style="border-bottom: none;">
            <?= _r($this->lblInfo); ?>
        </div>
    </div>
    <div class="table-body">
        <div class="row">
            <div class="col-md-1"><?= _r($this->lstItemsPerPageByAssignedUserObject); ?></div>
            <div class="col-md-3" style="margin-top: -7px;"><?= _r($this->txtFilter); ?></div>
            <div class="col-md-2" style="text-align: left;"><?= _r($this->btnClearFilters); ?></div>
            <div class="col-md-6" style="text-align: right; margin-bottom: 15px;"><?= _r($this->dtgFrontendOptions->Paginator); ?></div>
        </div>
        <?= _r($this->dtgFrontendOptions); ?>
        <div class="row">
            <div class="col-md-4"><?= _r($this->btnUpdate); ?> <?= _r($this->btnNew); ?></div>
            <div class="col-md-8" style="text-align: right;"><?= _r($this->dtgFrontendOptions->PaginatorAlternate); ?></div>
        </div>
    </div>
</div>









