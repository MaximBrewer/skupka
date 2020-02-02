CREATE DEFINER=`{{username}}`@`{{host}}` TRIGGER `SHAREDSTOCK` AFTER UPDATE ON `{{prefix}}virtuemart_products` FOR EACH ROW 
BEGIN

IF (@STOCK_TRIG_DISABLED IS NULL) THEN

SET @DIFF = OLD.product_in_stock - NEW.product_in_stock; 
SET @DIFF_ORDERED = OLD.product_ordered - NEW.product_ordered; 

 

   IF ((((@DIFF <> 0) OR (@DIFF_ORDERED <> 0)) AND (NEW.product_mpn IS NOT NULL) AND (NEW.product_mpn NOT LIKE '')) OR (OLD.product_mpn <> NEW.product_mpn)) THEN

SET @REF_ID_VAL = (select `id` from {{prefix}}virtuemart_sharedstock as s where s.mpn = TRIM(NEW.product_mpn) and ref_id = 0);

IF (@REF_ID_VAL IS NULL) THEN
  INSERT INTO {{prefix}}virtuemart_sharedstock (id, ref_id, virtuemart_product_id, mpn, product_in_stock, product_ordered) VALUES (NULL, 0, 0, TRIM(NEW.product_mpn), NEW.product_in_stock, NEW.product_ordered);
  
SET @REF_ID_VAL = LAST_INSERT_ID();
ELSE
   IF (@JOOMLA_IS_ADMIN = true) THEN
	  UPDATE {{prefix}}virtuemart_sharedstock SET product_in_stock = NEW.product_in_stock, product_ordered = NEW.product_ordered where `id` = @REF_ID_VAL;
	ELSE
      UPDATE {{prefix}}virtuemart_sharedstock SET product_in_stock = product_in_stock - @DIFF, product_ordered = product_ordered - @DIFF_ORDERED where `id` = @REF_ID_VAL;
	 END IF;
  
END IF;

SET @EXISTING_RECORD = (select `id` from {{prefix}}virtuemart_sharedstock as s where s.virtuemart_product_id = NEW.virtuemart_product_id);

IF (@EXISTING_RECORD IS NULL) THEN
  INSERT INTO {{prefix}}virtuemart_sharedstock (`id`, ref_id, virtuemart_product_id, mpn, product_in_stock, product_ordered) VALUES (NULL, @REF_ID_VAL, NEW.virtuemart_product_id, TRIM(NEW.product_mpn), NULL, NULL);
ELSE
   UPDATE {{prefix}}virtuemart_sharedstock set ref_id = @REF_ID_VAL, mpn = TRIM(NEW.product_mpn) where `id` = @EXISTING_RECORD;
END IF;
END IF;
END IF;
END