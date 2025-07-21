<div class="form-horizontal">
    <div class="table-body">

        <div class="row">
            <div class="col-md-12">
                <?= _r($this->lblInfo); ?>
            </div>
        </div>

        <div class="row">
            <div class="col-md-1"><?= _r($this->lstItemsPerPageByAssignedUserObject); ?></div>
            <div class="col-md-3" style="margin-top: -7px;"><?= _r($this->txtFilter); ?></div>
            <div class="col-md-8" style="text-align: right; margin-bottom: 15px;"><?= _r($this->dtgSportsAreas->Paginator); ?></div>
        </div>
        <?= _r($this->dtgSportsAreas); ?>
        <div class="row">
            <div class="col-md-12">
                <?= _r($this->btnBack); ?>
            </div>
        </div>
    </div>
</div>









