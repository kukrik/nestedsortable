<style>
    .tabbable-custom > .tab-content {background-color: #f5f5f5 !important; margin-bottom: -20px;}
    .tabbable-custom .nav-tabs > li > a {background-color: #f5f5f5 !important;}
    .form-slider {margin: 0;padding: 0 20px;}
    .image-wrapper {display: inline-block;background-color: #fff;border-radius: 4px;margin: 15px 0 30px 30px;padding: 15px;}
    .slider-wrapper {display: block; background-color: #fff;border-radius: 4px;margin: 15px 30px 15px 0;padding: 15px;}
    .slider-setting-wrapper {display: block;background-color: #fff;border-radius: 4px;margin: 15px 30px 30px 0;padding: 15px;}
    .form-horizontal .radio-inline {/*padding-top: 18px;*/margin-top: 0;margin-bottom: 0;}
    .edit.radio-inline {padding-top: 18px;margin-top: 0;margin-bottom: 0;}
    .svg-container img {height: 100%;}
    .bx-wrapper {margin: auto; margin-top: 15px;margin-bottom: 45px;}
    .slider-container {width: 100%;max-width: 1200px;margin: 0 auto;}
    /*.bx-wrapper img {max-height: 330px;}*/
    .sortable div.activated {background-color: #eaffea; /*#ddffdd*/}
    .sortable div.inactivated {background-color: #fff0f1; /*#ffe8e8*/}
    .placeholder {height: 105px;outline: 1px dashed #4183C4;background: rgba(73, 182, 255, 0.07);border-radius: 3px;margin: -1px;}
    .image-blocks {display: block;padding: 10px;height: 95px;border-top: #ddd 1px solid;}
    .icon-set:hover, .btn-icon:hover {background: #f6f6f6;color: inherit;text-decoration: none;border: #ddd 1px solid;border-radius: 4px;}
    .preview {display: inline-block;width: 110px;}
    .preview img {display: inline-block;max-width: 110px;max-height: 75px;border-radius: 7px;}
    .events {display: inline-block;vertical-align: middle;}
    .icon-set, .btn-icon {display: inline-block;font-size: 16px;color: #7d898d;background-color: transparent;width: 38px;padding: 7px;text-align: center;vertical-align: middle;cursor: pointer;}
</style>

<div class="form-horizontal">
    <div class="row">
        <div class="row equal">
            <div class="col-md-3">
                <div class="image-wrapper">
                    <?= _r($this->btnAddImage); ?>
                    <?= _r($this->dlgSorter); ?>
                </div>
            </div>
            <div class="col-md-9">
                <div class="slider-wrapper">
                    <?= _r($this->objTestSlider); ?>
                    <div class="form-actions fluid" style="margin: 1px;">
                        <div class="col-md-8">
                            <?= _r($this->btnChangeStatus); ?>
                            <?= _r($this->lblPublishingSlider); ?>
                            <?= _r($this->lstPublishingSlider); ?>
                            <?= _r($this->btnPublishingUpdate); ?>
                            <?= _r($this->btnPublishingCancel); ?>
                        </div>
                        <div class="col-md-4" style="text-align: right;">
                            <?= _r($this->btnRefresh); ?>
                            <?= _r($this->btnBack); ?>
                        </div>
                    </div>
                </div>
                <div class="slider-setting-wrapper hidden">
                    <div class="row">
                        <div class="col-md-6">
                            <?= _r($this->txtTitle); ?>
                        </div>
                        <div class="col-md-6">
                            <?= _r($this->txtUrl); ?>
                        </div>
                        <div class="col-md-2">
                            <?= _r($this->lblDimensions); ?>
                        </div>
                        <div class="col-md-4">
                            <?= _r($this->txtWidth); ?>
                            <?= _r($this->lblCross); ?>
                            <?= _r($this->txtHeight); ?>
                        </div>
                        <div class="col-md-6">
                            <?= _r($this->txtTop); ?>
                            <?= _r($this->lstStatusSlider); ?>
                            <?= _r($this->calPostUpdateDate); ?>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-12" style="text-align: right">
                            <?= _r($this->btnUpdate); ?>
                            <?= _r($this->btnCancel); ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>