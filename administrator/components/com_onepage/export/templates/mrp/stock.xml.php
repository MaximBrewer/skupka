<?php defined( '_JEXEC' ) or die( 'Restricted access' );?><mrpEnvelope>
  <body>
    <mrpRequest>
      <!-- Povinný blok pre definíciu príkazu -->
      <request command="EXPEO1" requestId="<?php echo date("Ymdhis"); ?>"> 
        <!-- requestId je ID spojenia (treba TIMESTAMP 20121120093530125) -->
      </request>
       <data>
        <filter>
		  <?php if (false) { ?>
          <fltvalue name="cisloSkladu">1</fltvalue>
		  <?php } ?>
          <fltvalue name="cisloCeny"><?php echo $tidd['config']->cenamrp; ?></fltvalue>
          <fltvalue name="malObraz">F</fltvalue>
          <fltvalue name="velObraz">F</fltvalue>
          <fltvalue name="stavy">T</fltvalue>
          <!-- Nepovinný blok s prípadnými dátami, záleží na príkaze, či ho potrebuje -->
          <!-- Číslo karty v rozsahu 1 až 10 -->
          <fltvalue name="SKKAR.CISLO"><?php echo $mrp_plu; ?></fltvalue>
		  <?php if (false) { ?>
          <!-- Kód skupiny skladových kariet A alebo B -->
          <fltvalue name="SKKAR.SKUPINA">A|B</fltvalue>
          <!-- Typ položky Z -->
          <fltvalue name="SKKAR.TYP_POL">Z</fltvalue>
		  <?php } ?>
        </filter>
      </data>
    </mrpRequest>
  </body>  
</mrpEnvelope>