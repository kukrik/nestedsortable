<?php $strPageTitle = 'Menüüpuu näited' ; ?>

<?php require('examples-header.inc.php'); ?>

<?php // require(QCUBED_CONFIG_DIR . '/header.inc.php'); ?>

<?php $this->RenderBegin(); ?>

<!-- BEGIN CONTENT -->
<div class="page-content-wrapper">
    <div class="page-content">
        <!-- BEGIN PAGE CONTENT-->
        <div class="row">
            <div class="col-md-12">
                <div class="content-body">
                    <!-- MENU CONTAINER BEGIN -->
                    <div class="panel-body">
                        <h3 class="panel-title" style="margin-bottom: -10px;">Menüüpuu näited</h3>
                        <div class="panel-examples">
                            <div class="row">
                                <div class="col-md-12">
                                    <p>Need näited näitavad, kuidas saab erinevaid menüüpuid näidata igas saidis, kui
                                        kasutatatakse saidi halduses QCubed v4 versiooni peale ehitatud
                                        NestedSortable dünaamilist pistikprogrammi.</p>
                                    <p>Näidete jaoks on siin piiratud menüüpuu näitamist kolme tasemega. Tegelikult saab
                                        menüüpuu tasemete arvu piiramatult suurendada, kuid praktikas on heaks tavaks
                                        piirata menüüpuu tasemed kuni kahe-kolme taseme peale, mille tõttu on saidi kasutajatel kerge
                                        navigeerida erinevatel lehtedel ja leida kiiresti otsitavat infot.</p>
                                </div>
                                <div class="col-md-12" style="margin-bottom: 10px;">
                                    <h4>Näide: NaturalList</h4>
                                <p>Esimene näide kasutab <i>NaturalList</i> klassi ja selle eesmärgiks on näha, kuidas
                                    näeb välja puhas loetelu ilma mingite stiilideta ega javascripti toetuseta. Proovige
                                    mängida <a href="menu_manager.php">menüü halduriga</a> ja tulge uuesti siia, selleks tuleb
                                    brauserit värskendada.</p>
                                    <p>See <strong><i>NaturalList klass</i></strong> on heaks lähtebaasiks, kui soovitakse
                                        erineva välimusega ja javascripti efektidega menüüd teha. Nendeks on funktsioonid
                                        <i>renderMenuTree()</i> ja <i>makeJqWidget()</i>.</p>
                                </div>
                                <div class="col-md-4 col-md-offset-4" style="margin-bottom: 10px; border: #cdcdcd 1px solid">
                                    <?= _r($this->tblList); ?>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-12" style="margin-bottom: 10px;">
                                    <h4>Näide: Bootstrap Navbar</h4>
                                    <p>Teine näide kasutab <i>QCubed Bootstrap</i> klassi võimalusi, konkreetselt
                                        <i>NavbarList'i võimalusi</i>. Praegu ei toeta ametlik Bootstrap mitmetasandilisi
                                        tasemeid, saab kuvada ainult kuni 2 tasandit.</p>
                                    <p>Sellisel juhul tuleb <strong>lihtsalt seadistada NestedSortable <i>MaxLevels'i</i></strong>
                                        väärtuseks "2", mis piirab soovitud tasemete sügavust.</p>
                                </div>
                                <div class="col-md-12" style="margin-bottom: 10px;">
                                    <?=  _r($this->navBar); ?>
                                    <div class="col-md-12" style="border: #ccc 2px dotted; min-height: 220px;">
                                        <h4>Home</h4>
                                        <p>Lorem ipsum dolor sit amet, exerci fastidii detracto in mel, alterum probatus scripserit te quo.
                                            Falli labore et eum, cibo posse scripserit in qui. Ne vix enim platonem accusamus.
                                            Mei et sint everti, mea discere erroribus ei, eam an omnes postea repudiandae.
                                            Ei blandit vituperata quo, in pro justo suavitate. Te case cibo tritani per.
                                            Nec sumo consequat ei, amet animal vis te.</p>
                                        <p>An placerat periculis mediocritatem has, ipsum officiis id sed. Ex nec error eripuit.
                                            Ut quo justo aeterno ceteros, eam ei etiam error. Ea has choro fabulas, quidam facete
                                            voluptaria te mel. Luptatum similique vituperatoribus mei ex. Nec cetero menandri
                                            abhorreant cu, ex aeterno debitis veritus eos.</p>
                                        <p>Cum eu etiam possit utamur, dolorum corrumpit at his, duo tempor inermis elaboraret eu.
                                            Vel ad summo dicit liberavisse, ut esse homero has. Everti vidisse dolores eos in.
                                            Dolorum complectitur at mel. His corrumpit expetendis in, ut usu posse movet,
                                            praesent dignissim has no.</p>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-12" style="margin-bottom: 10px;">
                                    <h4>Näide: SmartMenus Bootstrap Addon (Navbar)</h4>
                                    <p>Kolmas näide kasutab Bootstrap Navbar'ile alternatiivi <i>SmartMenus't</i>.
                                        Seda juhul, kui ametlik Bootstrap Navbar'i kahetasandilised võimalused ei
                                        rahulda teid. See näide kasutab pistikprogrammi <a href="https://www.smartmenus.org" target="_blank">SmartMenus</a>
                                        võimalusi.</p>
                                    <p><i>NaturalList</i> klassi baasil kohandatud <i>SmartMenus</i> klass koos kasutades
                                         <i>QCubed Bootstrap Navbar'iga</i> annab meile soovitud tulemuse.</p>
                                </div>
                                <div class="col-md-12" style="margin-bottom: 10px;">
                                    <?= _r($this->smartMenus); ?>
                                    <div class="col-md-12" style="border: #ccc 2px dotted; min-height: 220px;">
                                        <h4>Home</h4>
                                        <p>Lorem ipsum dolor sit amet, exerci fastidii detracto in mel, alterum probatus scripserit te quo.
                                            Falli labore et eum, cibo posse scripserit in qui. Ne vix enim platonem accusamus.
                                            Mei et sint everti, mea discere erroribus ei, eam an omnes postea repudiandae.
                                            Ei blandit vituperata quo, in pro justo suavitate. Te case cibo tritani per.
                                            Nec sumo consequat ei, amet animal vis te.</p>
                                        <p>An placerat periculis mediocritatem has, ipsum officiis id sed. Ex nec error eripuit.
                                            Ut quo justo aeterno ceteros, eam ei etiam error. Ea has choro fabulas, quidam facete
                                            voluptaria te mel. Luptatum similique vituperatoribus mei ex. Nec cetero menandri
                                            abhorreant cu, ex aeterno debitis veritus eos.</p>
                                        <p>Cum eu etiam possit utamur, dolorum corrumpit at his, duo tempor inermis elaboraret eu.
                                            Vel ad summo dicit liberavisse, ut esse homero has. Everti vidisse dolores eos in.
                                            Dolorum complectitur at mel. His corrumpit expetendis in, ut usu posse movet,
                                            praesent dignissim has no.</p>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-12" style="margin-bottom: 10px;">
                                    <h4>Näide: Bootstrap Navbar'i kasutamine koos SideBar-ga</h4>
                                    <p>Mõnikord võib tekkida vajadus näidata näiteks tellija soovil niisugust saiti,
                                        kus menüüpuu esimest taset kuvatakse Bootstrap Navbar'is ja menüüpuu järgmised
                                        tasemed kuvatakse sisu kõrval kas vasakul või paremal poolel.</p>
                                    <p>Neljandas näites näitame, et kuidas seda saab teha, kasutades css-i klasse ja
                                        javascripti. Loomulikult tuleb ka <i>NaturalList</i> klassi kasutada ja sobitada
                                        <i>SideBar</i> klassi jaoks.</p>
                                </div>
                                <div class="col-md-12" style="margin-bottom: 10px;">
                                    <?= _r($this->sideMenu); ?>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-3">
                                    <div id="sidebar">
                                        <nav class="submenu">
                                            <ul>
                                                <?= _r($this->tblSubMenu); ?>
                                            </ul>
                                        </nav>
                                    </div>
                                </div>
                                <div class="col-md-9" style="border: #ccc 2px dotted; min-height: 320px;">
                                    <h4>Home</h4>
                                    <p>Lorem ipsum dolor sit amet, exerci fastidii detracto in mel, alterum probatus scripserit te quo.
                                        Falli labore et eum, cibo posse scripserit in qui. Ne vix enim platonem accusamus.
                                        Mei et sint everti, mea discere erroribus ei, eam an omnes postea repudiandae.
                                        Ei blandit vituperata quo, in pro justo suavitate. Te case cibo tritani per.
                                        Nec sumo consequat ei, amet animal vis te.</p>
                                    <p>An placerat periculis mediocritatem has, ipsum officiis id sed. Ex nec error eripuit.
                                        Ut quo justo aeterno ceteros, eam ei etiam error. Ea has choro fabulas, quidam facete
                                        voluptaria te mel. Luptatum similique vituperatoribus mei ex. Nec cetero menandri
                                        abhorreant cu, ex aeterno debitis veritus eos.</p>
                                    <p>Cum eu etiam possit utamur, dolorum corrumpit at his, duo tempor inermis elaboraret eu.
                                        Vel ad summo dicit liberavisse, ut esse homero has. Everti vidisse dolores eos in.
                                        Dolorum complectitur at mel. His corrumpit expetendis in, ut usu posse movet,
                                        praesent dignissim has no.</p>
                                </div>
                                <div class="row">
                                    <div class="col-md-12" style="margin-bottom: 20px;">
                                        <h4>Näide: NestedSidebar</h4>
                                        <p>Mõnikord võib tekkida vajadus näidata näiteks tellija soovil niisugust saiti,
                                            kus ei kasutata Bootstrap Navbar'i ja menüüpuu kuvatakse sisu kõrval kas vasakul või paremal poolel.</p>
                                        <p>Viiendas näites näitame, et kuidas seda saab teha, kasutades css-i klasse ja
                                            javascripti. See on lihtsustatud näide, arendajatel on võimalik seda täiustada kas css-i või javascripti või php poolel.</p>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-3">
                                        <div id="nestedmenu">
                                            <?= _r($this->tblNestedMenu); ?>
                                        </div>
                                    </div>
                                    <div class="col-md-9" style="border: #ccc 2px dotted; min-height: 320px;">
                                        <h4>Home</h4>
                                        <p>Lorem ipsum dolor sit amet, exerci fastidii detracto in mel, alterum probatus scripserit te quo.
                                            Falli labore et eum, cibo posse scripserit in qui. Ne vix enim platonem accusamus.
                                            Mei et sint everti, mea discere erroribus ei, eam an omnes postea repudiandae.
                                            Ei blandit vituperata quo, in pro justo suavitate. Te case cibo tritani per.
                                            Nec sumo consequat ei, amet animal vis te.</p>
                                        <p>An placerat periculis mediocritatem has, ipsum officiis id sed. Ex nec error eripuit.
                                            Ut quo justo aeterno ceteros, eam ei etiam error. Ea has choro fabulas, quidam facete
                                            voluptaria te mel. Luptatum similique vituperatoribus mei ex. Nec cetero menandri
                                            abhorreant cu, ex aeterno debitis veritus eos.</p>
                                        <p>Cum eu etiam possit utamur, dolorum corrumpit at his, duo tempor inermis elaboraret eu.
                                            Vel ad summo dicit liberavisse, ut esse homero has. Everti vidisse dolores eos in.
                                            Dolorum complectitur at mel. His corrumpit expetendis in, ut usu posse movet,
                                            praesent dignissim has no.</p>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-12" style="margin-top: 10px; margin-bottom: 10px;">
                                        <h4>Kokkuvõte</h4>

                                        <p>Siin tuleb meeles pidada, et kuidas siduda Bootstrap Navbari või SmartMenus
                                            Navbari ja/või Sidebari sisuga, siis on mitmesuguseid võimalusi ja lahendusi.
                                        Iga arendaja valib ise.</p>
                                    </div>
                                </div>
                            </div>
                            <!-- MENU CONTAINER BEGIN -->
                        </div>
                    </div>
                </div>
            <!-- END PAGE CONTENT-->
            </div>
        </div>
        <!-- BEGIN CONTENT -->

<?php $this->RenderEnd(); ?>

<?php require('footer.inc.php'); ?>

<?php // require(QCUBED_CONFIG_DIR . '/footer.inc.php'); ?>

