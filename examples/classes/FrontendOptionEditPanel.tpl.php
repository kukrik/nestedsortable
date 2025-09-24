<div class="form-horizontal">
    <div class="form-top">
        <div class="form-group">
            <?= _r($this->lblFrontendTemplateName); ?>
            <div class="col-md-5">
                <?= _r($this->txtFrontendTemplateName); ?>
            </div>
        </div>
        <div class="form-group">
            <?= _r($this->lblContentTypesManagement); ?>
            <div class="col-md-5">
                <?= _r($this->lstContentTypesManagement); ?>
            </div>
        </div>
        <div class="form-group">
            <?= _r($this->lblClassName); ?>
            <div class="col-md-5">
                <?= _r($this->txtClassName); ?>
            </div>
        </div>
        <div class="form-group">
            <?= _r($this->lblFrontendTemplatePath); ?>
            <div class="col-md-5">
                <?= _r($this->txtFrontendTemplatePath); ?>
            </div>
        </div>
        <div class="form-group">
            <?= _r($this->lblStatus); ?>
            <div class="col-md-9">
                <?= _r($this->lstStatus); ?>
            </div>
        </div>
        <div class="form-actions fluid">
            <div class="col-md-offset-3 col-md-12">
                <?= _r($this->btnSave); ?>
                <?= _r($this->btnDelete); ?>
                <?= _r($this->btnCancel); ?>
            </div>
        </div>
    </div>
</div>