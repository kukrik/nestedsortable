<div class="form-horizontal">

    <div class="table-body">
        <div class="row">
            <div class="col-md-1"><?= _r($this->lstItemsPerPageByAssignedUserObject); ?></div>
            <div class="col-md-3" style="margin-top: -7px;"><?= _r($this->txtFilter); ?></div>
            <div class="col-md-2" style="text-align: left;"><?= _r($this->btnClearFilters); ?></div>
            <div class="col-md-6" style="text-align: right; margin-bottom: 15px;"><?= _r($this->dtgNewsChanges->Paginator); ?></div>
        </div>
        <?= _r($this->dtgNewsChanges); ?>
        <div class="row">
            <div class="col-md-12">
                <?= _r($this->btnAddChange); ?>
                <?= _r($this->btnGoToNews); ?>
                <?= _r($this->txtChange); ?>
                <?= _r($this->lstStatus); ?>
                <?= _r($this->btnSaveChange); ?>
                <?= _r($this->btnSave); ?>
                <?= _r($this->btnDelete); ?>
                <?= _r($this->btnCancel); ?>
            </div>
        </div>
    </div>
</div>










