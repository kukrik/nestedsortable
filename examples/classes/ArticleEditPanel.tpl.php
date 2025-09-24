<style>
    .select2-container--web-vauu .select2-results__option[aria-disabled=true] {
        display: none;
    }
    .article-body {margin: -5px 5px;}
</style>

<div class="form-horizontal">
    <div class="article-body">
        <div class="row equal">
            <div class="col-md-9 left-box padded-wrapper">
                <div class="form-group">
                    <?= _r($this->lblExistingMenuText); ?>
                    <div class="col-md-7">
                        <?= _r($this->txtExistingMenuText); ?>
                    </div>
                </div>
                <div class="form-group">
                    <?= _r($this->lblMenuText); ?>
                    <div class="col-md-7">
                        <?= _r($this->txtMenuText); ?>
                    </div>
                </div>
                <div class="form-group">
                    <?= _r($this->lblTitle); ?>
                    <div class="col-md-7">
                        <?= _r($this->txtTitle); ?>
                    </div>
                </div>
                <div class="form-group">
                    <?= _r($this->lblCategory); ?>
                    <div class="col-md-7">
                        <?= _r($this->lstCategory); ?>
                        <?= _r($this->btnGoToArticleCategroy); ?>
                    </div>
                </div>
                <div class="form-group">
                    <?= _r($this->lblTitleSlug); ?>
                    <div class="col-md-9">
                        <?= _r($this->txtTitleSlug); ?>
                    </div>
                </div>

                <script>
                    const dialogPath = <?= json_encode(dirname(QCUBED_FILEMANAGER_ASSETS_URL), JSON_UNESCAPED_UNICODE); ?>;
                    const bsCssPath = <?= json_encode(QCUBED_BOOTSTRAP_CSS, JSON_UNESCAPED_UNICODE); ?>;
                    ckConfig = {
                        skin: 'moono',
                        width: '100%',
                        height: '350px',
                        extraPlugins: 'colorbutton,font,justify,print,tableresize,pastefromword,liststyle,dialogadvtab,colordialog',
                        filebrowserImageBrowseUrl: dialogPath + '/examples/dialog.php',
                        filebrowserBrowseUrl: dialogPath + '/examples/dialog.php',
                        filebrowserWindowWidth: '95%',
                        filebrowserWindowHeight: '95%',
                        contentsCss: bsCssPath,
                        toolbar: [
                            { name: 'clipboard', items: [ 'PasteFromWord', '-', 'Undo', 'Redo' ] },
                            { name: 'basicstyles', items: [ 'Bold', 'Italic', 'Underline', 'Strike', 'RemoveFormat', 'Subscript', 'Superscript' ] },
                            { name: 'paragraph', items: [ 'NumberedList', 'BulletedList', '-', 'Outdent', 'Indent', '-', 'Blockquote' ] },
                            { name: 'insert', items: [ 'Image', 'Table' ] },
                            { name: 'links', items: [ 'Link', 'Unlink' ] },
                            '/',
                            { name: 'styles', items: [ 'Format', 'Font', 'FontSize' ] },
                            { name: 'colors', items: [ 'TextColor', 'BGColor', 'CopyFormatting' ] },
                            { name: 'align', items: [ 'JustifyLeft', 'JustifyCenter', 'JustifyRight', 'JustifyBlock' ] },
                            { name: 'document', items: [ 'Print', 'Source' ] }
                        ]
                    };
                </script>

                <div class="form-group">
                    <div class="col-md-12">
                        <?= _r($this->txtContent); ?>
                    </div>
                </div>
                <div class="form-group padded-form-actions">
                    <div class="col-md-12">
                        <?= _r($this->btnSave); ?>
                        <?= _r($this->btnSaving); ?>
                        <?= _r($this->btnCancel); ?>
                    </div>
                </div>
            </div>

            <div class="col-md-3 right-box padded-wrapper">
                <div class="form-group">
                    <?= _r($this->lblPostDate); ?>
                    <?= _r($this->calPostDate); ?>
                </div>
                <div class="form-group">
                    <?= _r($this->lblPostUpdateDate); ?>
                    <?= _r($this->calPostUpdateDate); ?>
                </div>
                <div class="form-group">
                    <?= _r($this->lblAuthor); ?>
                    <?= _r($this->txtAuthor); ?>
                </div>
                <div class="form-group">
                    <?= _r($this->lblUsersAsArticlesEditors); ?>
                    <?= _r($this->txtUsersAsArticlesEditors); ?>
                </div>
                <div class="form-group">
                    <?= _r($this->objMediaFinder); ?>
                    <?= _r($this->lblPictureDescription); ?>
                    <?= _r($this->txtPictureDescription); ?>
                </div>
                <div class="form-group">
                    <?= _r($this->lblAuthorSource); ?>
                    <?= _r($this->txtAuthorSource); ?>
                </div>
                <div class="form-group">
                    <?= _r($this->lblStatus); ?>
                    <?= _r($this->lstStatus); ?>
                </div>
              <!--  <div class="form-group">-->
                   <!-- --><?php /*= _r($this->lblConfirmationAsking); */?>
                    <?php /*= _r($this->chkConfirmationAsking); */?>
                <!--</div>-->
            </div>
        </div>
    </div>
</div>






