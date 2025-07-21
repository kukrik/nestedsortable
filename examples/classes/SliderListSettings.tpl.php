<div class="form-horizontal">
    <div class="table-body">
        <div class="row">
            <div class="col-md-1"><?= _r($this->lstItemsPerPage); ?></div>
            <div class="col-md-11" style="text-align: right; margin-bottom: 15px;"><?= _r($this->dtgSliders->Paginator); ?></div>
        </div>
        <?= _r($this->dtgSliders); ?>
        <div class="row">
            <div class="col-md-12">
                <?= _r($this->txtTitle); ?>
                <?= _r($this->btnSave); ?>
                <?= _r($this->btnCancel); ?>
            </div>
        </div>
    </div>
</div>










