<?php
$method = $viewData['method']; 

$method->virtuemart_shipmentmethod_id
?>

<div class="wrapper" id="coolrunner_map">
        <noscript>
        <div class="global-site-notice noscript">
            <div class="notice-inner">
                <p><strong>Det lader til at JavaScript er deaktiveret i din browser.</strong><br>
                Du skal have JavaScript slaet til i din browser for at kunne bruge alle funktionerne pa dette website.</p>
            </div>
        </div></noscript>

        <div class="page">
            <div class="main-container col1-layout">
                <div class="main">
                    <div class="col-main">
                        <div class="std">
                            <h1>Find indleveringssteder</h1>

                            <p class="p1"><span class="s1">Her kan du finde indleveringssteder hos DAO, Post Danmark og GLS, og se, hvor du kan aflevere din pakke.<br></span><span class="s1">Du kan zoome p&aring; kortet og afgr&aelig;nse visningen til et enkelt fragtfirma. Du kan ogs&aring; s&oslash;ge indleveringssteder pr. postnummer og eventuelt vejnavn.</span></p>
                        </div>

                        <div class="narrow-droppoints">
                            <div id="carrier-selection">
                                <div class="droppoint-filters">
                                    <span class="preword">Vis:</span>

                                    <div class="droppoint-filter">
                                        <input id="dao-droppoints" type="checkbox" value="1" checked="checked"> <label for="dao-droppoints">DAO</label>
                                    </div>

                                    <div class="droppoint-filter">
                                        <input id="pdk-droppoints" type="checkbox" value="1" checked="checked"> <label for="pdk-droppoints">Post Danmark</label>
                                    </div>

                                    <div class="droppoint-filter">
                                        <input id="gls-droppoints" type="checkbox" value="1" checked="checked"> <label for="gls-droppoints">GLS</label>
                                    </div>
                                </div>
                            </div>

                            <div class="search-droppoints">
                                <input id="search-country" type="hidden" name="country" value="DK"> <input id="search-zipcode" type="text" name="zipcode" value="" placeholder="Postnr."> <input id="search-street" type="text" name="street" value="" placeholder="Vejnavn (valgfrit)"> <button type="button" class="btn btn-primary">Sog</button>
                            </div>
                        </div>

                        <div class="droppoint-map-sidebar">
                            <div id="all-droppoint-map">
                                <div id="all-droppoint-map-canvas"></div>
                            </div>
                        </div>

                        <div id="all-droppoint-opening-hours-weekday-container">
                            <span id="all-droppoint-opening-hours-weekday-mo">Mandag</span>
                            <span id="all-droppoint-opening-hours-weekday-tu">Tirsdag</span>
                            <span id="all-droppoint-opening-hours-weekday-we">Onsdag</span>
                            <span id="all-droppoint-opening-hours-weekday-th">Torsdag</span>
                            <span id="all-droppoint-opening-hours-weekday-fr">Fredag</span>
                            <span id="all-droppoint-opening-hours-weekday-sa">Lordag</span>
                            <span id="all-droppoint-opening-hours-weekday-su">Sondag</span>
                        </div>
                        
                    </div>
                </div>
            </div>
        </div>
    </div>
    <script type="text/javascript">
		AllDroppoints.start();
    </script>
	
<?php

