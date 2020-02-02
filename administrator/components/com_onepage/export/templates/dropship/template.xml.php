<mrpEnvelope>
  <body>
    <mrpRequest>
      <!-- Povinný blok pre definíciu príkazu -->
      <request command="IMPEO0" requestId="<?php echo date("Ymdhis"); ?>"> 
        <!-- requestId je ID spojenia (treba TIMESTAMP 20121120093530125) -->
      </request>
      <data>
	  
        <params>
          <paramvalue name="cisloSkladu"><?php echo $tidd['config']->cisloSkladu; ?></paramvalue>
          <paramvalue name="stredisko"><?php echo $tidd['config']->stredisko; ?></paramvalue>
          <paramvalue name="cisloZakazky"><?php echo $tidd['config']->cisloZakazky; ?></paramvalue>
          <paramvalue name="prefixRadyObj"><?php echo $tidd['config']->prefix; ?></paramvalue>
        </params>
		
	  
		<?php foreach ($orders as $order) { 
		
		if (empty($order['details']['ST'])) {
			$order['details']['ST'] = $order['details']['BT']; 
		}
		
		
		
			$firma_hash = ''; 
			$address_type = 'BT'; 
			if (!empty($order['details'][$address_type]->company)) $firma_hash .= htmlspecialchars($order['details'][$address_type]->company); 
			$key = $tidd['config']->ico; 
			if (!empty($key))
			if (!empty($order['details'][$address_type]->$key)) $firma_hash .=  ' '.htmlspecialchars($order['details'][$address_type]->$key);
			$key = $tidd['config']->dic; 
			if (!empty($key))
			if (!empty($order['details'][$address_type]->$key)) $firma_hash .= ' '.htmlspecialchars($order['details'][$address_type]->$key); 
		
			$adresa_hash = $firma_hash; 
			$adresa_hash .= ' '.htmlspecialchars($order['details'][$address_type]->first_name); 
			$adresa_hash .= ' '.htmlspecialchars($order['details'][$address_type]->last_name); 
			if (!empty($order['details'][$address_type]->email))
			$adresa_hash .= ' '.htmlspecialchars($order['details'][$address_type]->email); 
			if (!empty($order['details'][$address_type]->phone_2))
			$adresa_hash .= ' '.htmlspecialchars($order['details'][$address_type]->phone_2); 
			if (!empty($order['details'][$address_type]->phone_1))
			$adresa_hash .= ' '.htmlspecialchars($order['details'][$address_type]->phone_1); 
			
			
			$adresa_hash .= htmlspecialchars(' '.$order['details'][$address_type]->address_1); 
			if (!empty($order['details'][$address_type]->address_2)) $adresa_hash .= htmlspecialchars(' '.$order['details']['ST']->address_2); 
		  
		    $adresa_hash .= htmlspecialchars(' '.$order['details'][$address_type]->city); 
		    $adresa_hash .= htmlspecialchars(' '.$order['details'][$address_type]->zip); 
			
			
			$md5 = md5($adresa_hash); 
			$md5 = substr($md5,0,10); 
			
			$firma_hash_st = ''; 
			$address_type = 'ST'; 
			if (!empty($order['details'][$address_type]->company)) $firma_hash_st .= htmlspecialchars($order['details'][$address_type]->company); 
			$key = $tidd['config']->ico; 
			if (!empty($key))
			if (!empty($order['details'][$address_type]->$key)) $firma_hash_st .=  ' '.htmlspecialchars($order['details'][$address_type]->$key);
			$key = $tidd['config']->dic; 
			if (!empty($key))
			if (!empty($order['details'][$address_type]->$key)) $firma_hash_st .= ' '.htmlspecialchars($order['details'][$address_type]->$key); 
		
			$adresa_hash_st = $firma_hash_st; 
			$adresa_hash_st .= ' '.htmlspecialchars($order['details'][$address_type]->first_name); 
			$adresa_hash_st .= ' '.htmlspecialchars($order['details'][$address_type]->last_name); 
			
			if (!empty($order['details'][$address_type]->phone_2))
			$adresa_hash_st .= ' '.htmlspecialchars($order['details'][$address_type]->phone_2); 
			if (!empty($order['details'][$address_type]->phone_1))
			$adresa_hash_st .= ' '.htmlspecialchars($order['details'][$address_type]->phone_1); 
			
			
			$adresa_hash_st .= htmlspecialchars(' '.$order['details'][$address_type]->address_1); 
			if (!empty($order['details'][$address_type]->address_2)) $adresa_hash_st .= htmlspecialchars(' '.$order['details']['ST']->address_2); 
		  
		    $adresa_hash_st .= htmlspecialchars(' '.$order['details'][$address_type]->city); 
		    $adresa_hash_st .= htmlspecialchars(' '.$order['details'][$address_type]->zip); 
			
            $md5_st = md5($adresa_hash_st); 
			$md5_st = substr($md5_st,0,10); 
			
			
	
		?>
        <objednavka cislo="EO00100004" formaUhrady="Hotovost" puvodniCislo="<?php echo htmlspecialchars($order['details']['BT']->virtuemart_order_id); ?>" datum="<?php echo date('Y-m-d', strtotime($order['details']['BT']->created_on)); ?>" cenySDPH="T">
          <mena kod="EUR" kurz="1" mnozstvi="1"/>
          <adresa id="<?php echo $md5; ?>" ulice="<?php echo htmlspecialchars($order['details']['BT']->address_1); 
		  if (!empty($order['details']['BT']->address_2)) echo htmlspecialchars(' '.$order['details']['BT']->address_2); 
		  ?>" mesto="<?php echo htmlspecialchars($order['details']['BT']->city); ?>" psc="<?php echo htmlspecialchars($order['details']['BT']->zip); ?>"> 
            <?php if (!empty($firma_hash)) { ?>
			<firma nazev="<?php if (!empty($order['details']['BT']->company)) echo htmlspecialchars($order['details']['BT']->company); ?>" ico="<?php 
			$key = $tidd['config']->ico; 
			if (!empty($key))
			if (!empty($order['details']['BT']->$key)) echo htmlspecialchars($order['details']['BT']->$key); 
			?>" dic="<?php 
			$key = $tidd['config']->dic; 
			if (!empty($key))
			if (!empty($order['details']['BT']->$key)) echo htmlspecialchars($order['details']['BT']->$key); 
			?>"/>
			<?php } ?>
            <osoba jmeno="<?php echo htmlspecialchars($order['details']['BT']->first_name); ?>" prijmeni="<?php echo htmlspecialchars($order['details']['BT']->last_name); ?>"/>
            <!-- Email sa skladá až do dĺžky poľa v databáze -->
            <email><?php echo htmlspecialchars($order['details']['BT']->email); ?></email>
            <!-- Telefón môže obsahovať tri položky -->
			<?php if (!empty($order['details']['BT']->phone_2)) { ?>
            <tel><?php echo htmlspecialchars($order['details']['BT']->phone_2); ?></tel>
			<?php } ?>
			<?php if (!empty($order['details']['BT']->phone_1)) { ?>
            <tel><?php echo htmlspecialchars($order['details']['BT']->phone_1); ?></tel>
			<?php } ?>
			<?php if (!empty($order['details']['BT']->fax)) { ?>
            <tel><?php echo htmlspecialchars($order['details']['BT']->fax); ?></tel>
			<?php } ?>
			
            
          </adresa>
		  
		  
		  
		   <adresa_dod id="<?php echo $md5_st; ?>" ulice="<?php echo htmlspecialchars($order['details']['ST']->address_1); 
		  if (!empty($order['details']['ST']->address_2)) echo htmlspecialchars(' '.$order['details']['ST']->address_2); 
		  ?>" mesto="<?php echo htmlspecialchars($order['details']['ST']->city); ?>" psc="<?php echo htmlspecialchars($order['details']['ST']->zip); ?>"> 
            <?php 
			if (!empty($firma_hash_st)) {
				?><firma nazev="<?php if (!empty($order['details']['ST']->company)) echo htmlspecialchars($order['details']['ST']->company); ?>" ico="<?php 
			$key = $tidd['config']->ico; 
			if (!empty($key))
			if (!empty($order['details']['ST']->$key)) echo htmlspecialchars($order['details']['ST']->$key); 
			?>" dic="<?php 
			$key = $tidd['config']->dic; 
			if (!empty($key))
			if (!empty($order['details']['ST']->$key)) echo htmlspecialchars($order['details']['ST']->$key); 
			?>"/>
			<?php } ?>
            <osoba jmeno="<?php echo htmlspecialchars($order['details']['ST']->first_name); ?>" prijmeni="<?php echo htmlspecialchars($order['details']['ST']->last_name); ?>"/>
            <!-- Email sa skladá až do dĺžky poľa v databáze -->
            <email><?php echo htmlspecialchars($order['details']['BT']->email); ?></email>
            <!-- Telefón môže obsahovať tri položky -->
			<?php if (!empty($order['details']['ST']->phone_2)) { ?>
            <tel><?php echo htmlspecialchars($order['details']['ST']->phone_2); ?></tel>
			<?php } ?>
			<?php if (!empty($order['details']['ST']->phone_1)) { ?>
            <tel><?php echo htmlspecialchars($order['details']['ST']->phone_1); ?></tel>
			<?php } ?>
			<?php if (!empty($order['details']['ST']->fax)) { ?>
            <tel><?php echo htmlspecialchars($order['details']['ST']->fax); ?></tel>
			<?php } ?>
			
            
          </adresa_dod>
		  
           
          <polozky><?php foreach ($order['items'] as $item) { ?>
            <polozka cisloKarty="<?php echo $item->cisloKarty; ?>" text="" cenaMJ="<?php echo $item->product_final_price; ?>" pocetMJ="<?php echo $item->product_quantity; ?>" sazbaDPH="20"/>
		  <?php } ?>
          </polozky>
		  <?php if (!empty($order['details']['BT']->customer_note)) { ?>
          <poznamka>
           <?php echo htmlspecialchars($order['details']['BT']->customer_note); ?>
          </poznamka>
		  <?php } ?>
        </objednavka>
		<?php } ?>
      </data>
    </mrpRequest>
  </body>  
</mrpEnvelope>