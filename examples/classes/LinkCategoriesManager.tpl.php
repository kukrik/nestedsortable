<div class="form-horizontal">
    <div class="table-body">
        <div class="row">
            <div class="col-md-1"><?= _r($this->lstItemsPerPageByAssignedUserObject); ?></div>
            <div class="col-md-3" style="margin-top: -7px;"><?= _r($this->txtFilter); ?></div>
            <div class="col-md-8" style="text-align: right; margin-bottom: 15px;"><?= _r($this->dtgLinkCategories->Paginator); ?></div>
        </div>
        <?= _r($this->dtgLinkCategories); ?>
        <div class="row">
            <div class="col-md-12">
                <?= _r($this->btnAddCategory); ?>
                <?= _r($this->btnGoToLinks); ?>
                <?= _r($this->txtCategory); ?>
                <?= _r($this->lstStatus); ?>
                <?= _r($this->btnSaveCategory); ?>
                <?= _r($this->btnSave); ?>
                <?= _r($this->btnDelete); ?>
                <?= _r($this->btnCancel); ?>
            </div>
        </div>
    </div>
</div>










