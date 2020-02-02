<?php
// based on http://sima.cat/nif.php

class valnif {
#
# Checks for any valid European Union value added tax number
# from original Cobol public version
# http://a104.g.akamai.net/7/104/3242/v001/www.aeat.es/padres/prevalid/descarga/prv34902.exe
# to PHP by David Gimeno i Ayuso, info@sima.cat. Catalonia 2002.
#
# input  $vat          vat number (upper or lowercase, separators included)
#        $country      valid ISO country code of any EU-15
#                      note: GR is the ISO country code for Greece but EU
#                      directive 2001/115 changed it to EL
#
# output $vat          formatted vat number (uppercase, without separators)
#        function      false     valid
#                      1         invalid format
#                      2         invalid value
#                      3         invalid check
#                      4         invalid country
#----------------------------------------------------------------------------
# May/1/2004
#
# Added 10 new EU countries plus Bulgaria and Romania
# from original Cobol public version
# http://a104.g.akamai.net/f/104/3242/15m/www.aeat.es/padres/prevalid/descarga/prv34904.exe
# to PHP by David Gimeno i Ayuso, info@sima-pc.com. Catalonia 2004.
#----------------------------------------------------------------------------
# Dec/31/2004
#
# Optimized formula for checking Germany VAT code
#----------------------------------------------------------------------------
# Feb/5/2006
#
# Corrected error on checking Italy VAT's: it didn't verify whether
# the rightmost three chars were right or not
#----------------------------------------------------------------------------
# Mar/2/2006
#
# Corrected checking of Romanian codes shorter than 10 digits,
# padding them with 0's [thanks to Antonio Gherdan for warning me]
#----------------------------------------------------------------------------
# Apr/5/2006
#
# Corrected checking of German codes when checksum resulted in 10.
#----------------------------------------------------------------------------
# Apr/8/2006
#
# Padded computed French control digits to 2 in length.
#----------------------------------------------------------------------------
# Mai/17/2006
#
# Adapted Belgian VAT code to new format of 10 digits, padding with zeros.
#----------------------------------------------------------------------------
# Sep/30/2006
#
# Corrected checksum error on Czech 9-digits code.
#----------------------------------------------------------------------------
# Jun/15/2008
#
# Added new Spanish letter codes.
#----------------------------------------------------------------------------
# Sep/28/2010
#
# Corrected checking of Italian codes shorter than 4 digits
# [thanks to Stefano Zeri for warning me]
#----------------------------------------------------------------------------
# Jan/25/2012
#
# Added checking of new UK codes beginning with 100 and up
# [thanks to Wiktor Walc for warning me]
#----------------------------------------------------------------------------
# Oct/1/2012
#
# Changed all ereg for preg
# [thanks to Ivan de Castro for doing it and sharing]
#----------------------------------------------------------------------------
function valnif(&$VAT,$COUNTRY="ES") {
  switch (strtoupper($COUNTRY)):
  case "AT":
    #
    # Austria
    #
    $FAN=strtoupper(preg_replace("/[^[:alnum:]\?]/","",$VAT));
    $OK=preg_match("/(^U{1})([0-9]{1,7})([0-9]{1}$|\?{1}$)/",$FAN,$REGS);
    if (!$OK):
      return 1;                         //invalid format
    endif;
    $TIP=strtoupper($REGS[1]);
    $NUM=$REGS[2];
    $CTR=$REGS[3];
    $NUM=str_pad($NUM,7,"0",STR_PAD_LEFT);
    $CHK=0;
    for ($I=0; $I<strlen($NUM); $I++):
      $C=substr($NUM,$I,1)*($I%2==0?1:2);
      $CHK=$CHK+substr($C,0,1)+substr($C,1,1);
    endfor;
    $CHK=10-($CHK+4)%10;
    $CHK=($CHK==10?0:$CHK);
    if (!preg_match("/\?{1}/",$CTR) and $CTR<>$CHK):
      return 3;                         //invalid check
    endif;
    $VAT=$TIP.$NUM.$CHK;
    return false;
  case "BE":
    #
    # Belgium
    #
    $FAN=strtoupper(preg_replace("/[^[:alnum:]\?]/","",$VAT));
    $OK=preg_match("/(^[0-9]{1,8})([0-9]{2}$|\?{1,2}$)/",$FAN,$REGS);
    if (!$OK):
      return 1;                         //invalid format
    endif;
    $NUM=$REGS[1];
    $CTR=$REGS[2];
    $NUM=str_pad($NUM,8,"0",STR_PAD_LEFT);
    if (substr($NUM,1,1)<2):
      return 2;                         //invalid value
    endif;
    $CHK=str_pad(97-$NUM%97,2,"0",STR_PAD_LEFT);
    if (!preg_match("/\?{1,2}/",$CTR) and $CTR<>$CHK):
      return 3;                         //invalid check
    endif;
    $VAT=$NUM.$CHK;
    return false;
  case "BG":
    #
    # Bulgaria
    #
    $FAN=strtoupper(preg_replace("/[^[:alnum:]\?]/","",$VAT));
    $OK=preg_match("/(^[0-9]{1,9})([0-9]{1}$|\?{1}$)/",$FAN,$REGS);
    if (!$OK):
      return 1;                         //invalid format
    endif;
    $NUM=$REGS[1];
    $CTR=$REGS[2];
    $NUM=str_pad($NUM,9,"0",STR_PAD_LEFT);
    if (preg_match("/[23]/",substr($NUM,0,1)) and substr($NUM,1,2)<>"22"):
      return 2;                         //invalid value
    endif;
    $CHG=array(4,3,2,7,6,5,4,3,2);
    $CHK=0;
    for ($I=0; $I<strlen($NUM); $I++):
      $CHK=$CHK+$CHG[$I]*substr($NUM,$I,1);
    endfor;
    $CHK=11-$CHK%11;
    $CHK=($CHK==11?0:$CHK);
    if ($CHK==10):
      return 2;                         //invalid value
    endif;
    if (!preg_match("/\?{1}/",$CTR) and $CTR<>$CHK):
      return 3;                         //invalid check
    endif;
    $VAT=$NUM.$CHK;
    return false;
  case "CY":
    #
    # Cyprus
    #
    $FAN=strtoupper(preg_replace("/[^[:alnum:]\?]/","",$VAT));
    $OK=preg_match("/(^[0-9]{1,8})([A-Z]{1}$|\?{1}$)/",$FAN,$REGS);
    if (!$OK):
      return 1;                         //invalid format
    endif;
    $NUM=$REGS[1];
    $CTR=$REGS[2];
    $NUM=str_pad($NUM,8,"0",STR_PAD_LEFT);
    if (!preg_match("/[013459]/",substr($NUM,0,1))):
      return 2;                         //invalid value
    endif;
    $CHG=array(1,0,5,7,9,13,15,17,19,21);
    $CHK=0;
    for ($I=0; $I<strlen($NUM); $I++):
      if ($I%2==0):
        $CHK=$CHK+$CHG[substr($NUM,$I,1)];
      else:
        $CHK=$CHK+substr($NUM,$I,1);
      endif;
    endfor;
    $CHK=substr("ABCDEFGHIJKLMNOPQRSTUVWXYZ",$CHK%26,1);
    if (!preg_match("/\?{1}/",$CTR) and $CTR<>$CHK):
      return 3;                         //invalid check
    endif;
    $VAT=$NUM.$CHK;
    return false;
  case "CZ":
    #
    # Czech Republic
    #
    $FAN=strtoupper(preg_replace("/[^[:alnum:]\?]/","",$VAT));
    $OK=preg_match("/(^[012345678][0-9]{6})([0-9]{1}$|\?{1}$)/",$FAN,$REGS);
    if (!$OK):
      $OK=preg_match("/(^[6][0-9]{7})([0-9]{1}$|\?{1}$)/",$FAN,$REGS);
      if (!$OK):
        $OK=preg_match("/(^[0-9]{9}$)/",$FAN,$REGS);
        if (!$OK):
          $OK=preg_match("/(^[0-9]{10}$)/",$FAN,$REGS);
          if (!$OK):
            return 1;                   //invalid format
          endif;
        endif;
      endif;
    endif;
    $NUM=$REGS[1];
    $CTR=$REGS[2];
    if (strlen($NUM)>8):
      if (strlen($NUM)==9):
        if (substr($NUM,0,2)>53 and substr($NUM,0,2)<80):
          return 2;                     //invalid value
        endif;
      else:
        if (substr($NUM,0,2)<54):
          return 2;                     //invalid value
        endif;
      endif;
      if (substr($NUM,2,2)<1 or substr($NUM,2,2)>12 and substr($NUM,2,2)<51 or substr($NUM,2,2)>62):
        return 2;                       //invalid value
      endif;
      if (substr($NUM,4,2)<1):
        return 2;                       //invalid value
      endif;
      if (preg_match("/02|52/",substr($NUM,2,2))):
        if (substr($NUM,0,2)%4>0):
          if (substr($NUM,4,2)>28):
            return 2;                   //invalid value
          endif;
        else:
          if (substr($NUM,4,2)>29):
            return 2;                   //invalid value
          endif;
        endif;
      elseif (preg_match("/04|06|09|11|54|56|59|61/",substr($NUM,2,2))):
        if (substr($NUM,4,2)>30):
          return 2;                     //invalid value
        endif;
      else:
        if (substr($NUM,4,2)>31):
          return 2;                     //invalid value
        endif;
      endif;
    endif;
    if (strlen($NUM)<9):
      $CHG=array(8,7,6,5,4,3,2);
      $CHK=0;
      for ($I=0; $I<7; $I++):
        $CHK=$CHK+$CHG[$I]*substr($NUM,$I+(1 and strlen($NUM)==8),1);
      endfor;
      if (strlen($NUM)==7):
        $CHK=11-$CHK%11;
        $CHK=($CHK==10?0:$CHK);
        $CHK=($CHK==11?1:$CHK);
      else:
        $CHK=9-(11-$CHK%11)%10;
      endif;
      if (!preg_match("/\?{1}/",$CTR) and $CTR<>$CHK):
        return 3;                       //invalid check
      endif;
      $VAT=$NUM.$CHK;
    elseif (strlen($NUM)==9):
      $VAT=$NUM;
    else:
      $CHK=0;
      for ($I=0; $I<strlen($NUM); $I=$I+2):
        $CHK=$CHK+substr($NUM,$I,2);
      endfor;
      if ($CHK%11>0 or fmod($NUM,11)>0):
        return 3;                       //invalid check
      endif;
      $VAT=$NUM;
    endif;
    return false;
  case "DE":
    #
    # Germany
    #
    $FAN=strtoupper(preg_replace("/[^[:alnum:]\?]/","",$VAT));
    $OK=preg_match("/(^[0-9]{1,8})([0-9]{1}$|\?{1}$)/",$FAN,$REGS);
    if (!$OK):
      return 1;                         //invalid format
    endif;
    $NUM=$REGS[1];
    $CTR=$REGS[2];
    if ($NUM==0):
      return 2;                         //invalid value
    endif;
    $NUM=str_pad($NUM,8,"0",STR_PAD_LEFT);
    //
    // original formula for Germany
    //
    //$CHK=10;
    //for ($I=0; $I<strlen($NUM); $I++):
    //  $CHK=($CHK+substr($NUM,$I,1))%10; $CHK=($CHK==0?10:$CHK);
    //  $CHK=2*$CHK%11; $CHK=($CHK==0?11:$CHK);
    //endfor;
    //$CHK=11-$CHK; $CHK=($CHK==10?0:$CHK);
    //
    // optimized formula follows
    //
    $CHK=0;
    for ($I=0; $I<strlen($NUM); $I++):
      $CHK=2*((substr($NUM,$I,1)+$CHK+9)%10+1)%11;
    endfor;
    $CHK=11-$CHK; $CHK=($CHK==10?0:$CHK);
    if (!preg_match("/\?{1}/",$CTR) and $CTR<>$CHK):
      return 3;                         //invalid check
    endif;
    $VAT=$NUM.$CHK;
    return false;
  case "DK":
    #
    # Denmark
    #
    $FAN=strtoupper(preg_replace("/[^[:alnum:]]/","",$VAT));
    $OK=preg_match("/(^[0-9]{1,8})/",$FAN,$REGS);
    if (!$OK):
      return 1;                         //invalid format
    endif;
    $NUM=$REGS[1];
    if (substr($NUM,0,1)<1):
      return 2;                         //invalid value
    endif;
    $NUM=substr($NUM,0,1).str_pad(substr($NUM,1),7,"0",STR_PAD_LEFT);
    $CHK=2*substr($NUM,0,1);
    for ($I=1; $I<strlen($NUM); $I++):
      $CHK=$CHK+(8-$I)*substr($NUM,$I,1);
    endfor;
    if ($CHK%11>0):
      return 3;                         //invalid check
    endif;
    $VAT=$NUM;
    return false;
  case "EE":
    #
    # Estonia
    #
    $FAN=strtoupper(preg_replace("/[^[:alnum:]\?]/","",$VAT));
    $OK=preg_match("/(^[0-9]{1,8})([0-9]{1}$|\?{1}$)/",$FAN,$REGS);
    if (!$OK):
      return 1;                         //invalid format
    endif;
    $NUM=$REGS[1];
    $CTR=$REGS[2];
    $NUM=str_pad($NUM,8,"0",STR_PAD_LEFT);
    $CHG=array(3,7,1,3,7,1,3,7);
    $CHK=0;
    for ($I=0; $I<strlen($NUM); $I++):
      $CHK=$CHK+$CHG[$I]*substr($NUM,$I,1);
    endfor;
    $CHK=10-$CHK%10;
    $CHK=($CHK==10?0:$CHK);
    if (!preg_match("/\?{1}/",$CTR) and $CTR<>$CHK):
      return 3;                         //invalid check
    endif;
    $VAT=$NUM.$CHK;
    return false;
  case "ES":
    #
    # Spain
    #
    $FAN=strtoupper(preg_replace("/[^[:alnum:]\?]/","",$VAT));
    $OK=preg_match("/(^[A-Z]{1})([0-9]{1,7})([0-9]{1}$|\?{1}$)/",$FAN,$REGS);
    if (!$OK):
      $OK=preg_match("/(^[A-Z]{1})([0-9]{1,7})([A-Z]{1}$)/",$FAN,$REGS);
      if (!$OK):
        $OK=preg_match("/()(^[0-9]{1,8})([A-Z]{1}$|\?{1}$)/",$FAN,$REGS);
        if (!$OK):
          return 1;                     //invalid format
        endif;
      endif;
    endif;
    $TIP=$REGS[1];
    $NUM=$REGS[2];
    $CTR=$REGS[3];
    if ($TIP==""):
      $NUM=str_pad($NUM,8,"0",STR_PAD_LEFT);
    else:
      $NUM=str_pad($NUM,7,"0",STR_PAD_LEFT);
    endif;
    if (preg_match("/[A-H]|[JNPQRSUVW]/",$TIP)):
      $CHK=0;
      for ($I=0; $I<strlen($NUM); $I++):
        $C=substr($NUM,$I,1)*($I%2>0?1:2);
        $CHK=$CHK+substr($C,0,1)+substr($C,1,1);
      endfor;
      $CHK=10-$CHK%10;
      $CHK=($CHK==10?0:$CHK);
      if (preg_match("/[NPQRSW]/",$TIP)):
        $CHK=substr("JABCDEFGHI",$CHK,1);
      endif;
    elseif (preg_match("/[KLMX]/",$TIP) or ($TIP=="")):
      $CHK=$NUM%23;
      $CHK=substr("TRWAGMYFPDXBNJZSQVHLCKE",$CHK,1);
    elseif (preg_match("/[Y]/",$TIP)):
      $CHK=(10000000+$NUM)%23;
      $CHK=substr("TRWAGMYFPDXBNJZSQVHLCKE",$CHK,1);
    elseif (preg_match("/[Z]/",$TIP)):
      $CHK=(20000000+$NUM)%23;
      $CHK=substr("TRWAGMYFPDXBNJZSQVHLCKE",$CHK,1);
    else:
      return 2;                         //invalid value
    endif;
    if (!preg_match("/\?{1}/",$CTR) and $CTR<>$CHK):
      return 3;                         //invalid check
    endif;
    $VAT=$TIP.$NUM.$CHK;
    return false;
  case "FI":
    #
    # Finland
    #
    $FAN=strtoupper(preg_replace("/[^[:alnum:]\?]/","",$VAT));
    $OK=preg_match("/(^[0-9]{1,7})([0-9]{1}$|\?{1}$)/",$FAN,$REGS);
    if (!$OK):
      return 1;                         //invalid format
    endif;
    $NUM=$REGS[1];
    $CTR=$REGS[2];
    $NUM=str_pad($NUM,7,"0",STR_PAD_LEFT);
    $CHG=array(7,9,10,5,8,4,2);
    $CHK=0;
    for ($I=0; $I<strlen($NUM); $I++):
      $CHK=$CHK+$CHG[$I]*substr($NUM,$I,1);
    endfor;
    $CHK=11-$CHK%11;
    $CHK=($CHK==11?0:$CHK);
    if ($CHK==10):
      return 2;                         //invalid value
    endif;
    if (!preg_match("/\?{1}/",$CTR) and $CTR<>$CHK):
      return 3;                         //invalid check
    endif;
    $VAT=$NUM.$CHK;
    return false;
  case "FR":
    #
    # France
    #
    $FAN=strtoupper(preg_replace("/[^[:alnum:]\?]/","",$VAT));
    $OK=preg_match("/(^[0-9]{2}|^\?{1,2})([0-9]{1,9}$)/",$FAN,$REGS);
    if (!$OK):
      $OK=preg_match("/(^[[:alnum:]]{2})([0-9]{1,9}$)/",$FAN,$REGS);
      if (!$OK):
        return 1;                       //invalid format
      endif;
    endif;
    $CTR=$REGS[1];
    $NUM=$REGS[2];
    $NUM=str_pad($NUM,9,"0",STR_PAD_LEFT);
    if ($NUM==0):
      return 2;                         //invalid value
    elseif (preg_match("/I|O/",$CTR)):
      return 2;                         //invalid value
    elseif (is_numeric($CTR) or preg_match("/\?{1,2}/",$CTR)):
      $CHK=str_pad((($NUM%97)*100+12)%97,2,"0",STR_PAD_LEFT);
      if (!preg_match("/\?{1,2}/",$CTR) and $CTR<>$CHK):
        return 3;                       //invalid check
      endif;
    elseif (preg_match("/[A-Z]/",substr($CTR,0,1))==preg_match("/[A-Z]/",substr($CTR,1,1))):
      return 2;                         //invalid value
    else:
      $CHG=array_merge(range("0","9"),range("A","H"),range("J","N"),range("P","Z"));
      $C=array_search(substr($CTR,0,1),$CHG);
      $CHK=($C<10?$C*24-10:$C*34-100)+array_search(substr($CTR,1,1),$CHG);
      $C=$CHK%11;
      $CHK=floor($CHK/11)+1;
      if ($C<>($NUM+$CHK)%11):
        return 3;                       //invalid check
      endif;
      $CHK=$CTR;
    endif;
    $VAT=$CHK.$NUM;
    return false;
  case "GB":
    #
    # United Kingdom
    #
    $FAN=strtoupper(preg_replace("/[^[:alnum:]]/","",$VAT));
    $OK=preg_match("/(^000|^001)([0-9]{9})([3]{1}$)/",$FAN,$REGS);
    if (!$OK):
      $OK=preg_match("/(^000|^001)([0-9]{9}$)()/",$FAN,$REGS);
      if (!$OK):
        $OK=preg_match("/()(^[0-9]{9})([3]{1}$)/",$FAN,$REGS);
        if (!$OK):
          $OK=preg_match("/()(^[0-9]{12}$)()/",$FAN,$REGS);
          if (!$OK):
            $OK=preg_match("/()(^[0-9]{9}$)()/",$FAN,$REGS);
            if (!$OK):
              $OK=preg_match("/(^HA)(8888[0-9]{3})([0-9]{2}$)/",$FAN,$REGS);
              if (!$OK):
                $OK=preg_match("/(^GD)(8888[0-9]{3})([0-9]{2}$)/",$FAN,$REGS);
                if (!$OK):
                  $OK=preg_match("/(^HA)([0-9]{3}$)()/",$FAN,$REGS);
                  if (!$OK):
                    $OK=preg_match("/(^GD)([0-9]{3}$)()/",$FAN,$REGS);
                    if (!$OK):
                      return 1;         //invalid format
                    endif;
                  endif;
                endif;
              endif;
            endif;
          endif;
        endif;
      endif;
    endif;
    $TIP=$REGS[1];
    $NUM=$REGS[2];
    $CTR=$REGS[3];
    if ($TIP=="GD" or $TIP=="HA"):
      if (substr($NUM,0,4)==8888):
        $CHK=substr($NUM,4)%97;
        if (!preg_match("/\?{2}/",$CTR) and $CTR<>$CHK):
          return 3;                     //invalid check
        endif;
        $CTR=$CHK;
      else:
        if ($TIP=="GD" and $NUM>499):
          return 2;                     //invalid value
        elseif ($TIP=="HA" and $NUM<500):
          return 2;                     //invalid value
        endif;
      endif;
    else:
      if (substr($NUM,0,7)<1):
        return 3;                       //invalid check
      endif;
      if (substr($NUM,-2)>=97):
        return 3;                       //invalid check
      endif;
      if ($CTR>"" and $CTR<>3):
        return 3;                       //invalid check
      endif;
      $CHG=array(8,7,6,5,4,3,2,10,1);
      $CHK=0;
      for ($I=0; $I<9; $I++):
        $CHK=$CHK+$CHG[$I]*substr($NUM,$I,1);
      endfor;
      $CHK=$CHK%97;
      if (substr($NUM,0,3)<100):
        if ($CHK>0):
          return 3;                     //invalid check
        endif;
      else:
        if ($CHK<>0 and $CHK<>42 and $CHK<>55):
          return 3;                     //invalid check
        endif;
      endif;
    endif;
    $VAT=$TIP.$NUM.$CTR;
    return false;
  case "GR": case "EL":
    #
    # Greece
    #
    $FAN=strtoupper(preg_replace("/[^[:alnum:]\?]/","",$VAT));
    $OK=preg_match("/(^[0-9]{8})([0-9]{1}$|\?{1}$)/",$FAN,$REGS);
    if (!$OK):
      $OK=preg_match("/(^[0-9]{7})([0-9]{1}$|\?{1}$)/",$FAN,$REGS);
      if (!$OK):
        return 1;                       //invalid format
      endif;
    endif;
    $NUM=$REGS[1];
    $CTR=$REGS[2];
    $CHK=0;
    for ($I=0; $I<strlen($NUM); $I++):
      $CHK=$CHK+(pow(2,strlen($NUM)-$I))*substr($NUM,$I,1);
    endfor;
    $CHK=$CHK%11;
    $CHK=($CHK==10?0:$CHK);
    if (!preg_match("/\?{1}/",$CTR) and $CTR<>$CHK):
      return 3;                         //invalid check
    endif;
    $VAT=$NUM.$CHK;
    return false;
  case "HU":
    #
    # Hungary
    #
    $FAN=strtoupper(preg_replace("/[^[:alnum:]\?]/","",$VAT));
    $OK=preg_match("/(^[0-9]{1,7})([0-9]{1}$|\?{1}$)/",$FAN,$REGS);
    if (!$OK):
      return 1;                         //invalid format
    endif;
    $NUM=$REGS[1];
    $CTR=$REGS[2];
    $NUM=str_pad($NUM,7,"0",STR_PAD_LEFT);
    if (substr($NUM,0,1)==0):
      return 2;                         //invalid value
    endif;
    $CHG=array(9,7,3,1,9,7,3);
    $CHK=0;
    for ($I=0; $I<strlen($NUM); $I++):
      $CHK=$CHK+$CHG[$I]*substr($NUM,$I,1);
    endfor;
    $CHK=10-$CHK%10;
    $CHK=($CHK==10?0:$CHK);
    if (!preg_match("/\?{1}/",$CTR) and $CTR<>$CHK):
      return 3;                         //invalid check
    endif;
    $VAT=$NUM.$CHK;
    return false;
  case "IE":
    #
    # Ireland
    #
    $FAN=strtoupper(preg_replace("/[^[:alnum:]\*\+\?]/","",$VAT));
    $OK=preg_match("/(^[0-9]{1})([A-Z\*\+]{1})([0-9]{1,5})([A-Z\?]{1}$)/",$FAN,$REGS);
    if (!$OK):
      $OK=preg_match("/()()(^[0-9]{1,7})([A-Z]{1}$|\?{1}$)/",$FAN,$REGS);
      if (!$OK):
        return 1;                       //invalid format
      endif;
    endif;
    $NUM=$REGS[1].$REGS[3];
    $TIP=$REGS[2];
    $CTR=$REGS[4];
    if ($TIP>""):
      if (substr($NUM,0,1)<7):
        return 2;                       //invalid value
      endif;
      $NUM=substr($NUM,0,1).str_pad(substr($NUM,1),5,"0",STR_PAD_LEFT);
      $CHK=2*substr($NUM,0,1);
      for ($I=1; $I<strlen($NUM); $I++):
        $CHK=$CHK+(8-$I)*substr($NUM,$I,1);
      endfor;
    else:
      if ($NUM==0):
        return 2;                       //invalid value
      endif;
      $NUM=str_pad($NUM,7,"0",STR_PAD_LEFT);
      $CHK=0;
      for ($I=0; $I<strlen($NUM); $I++):
        $CHK=$CHK+(8-$I)*substr($NUM,$I,1);
      endfor;
    endif;
    $CHK=$CHK%23;
    $CHK=substr("WABCDEFGHIJKLMNOPQRSTUV",$CHK,1);
    if (!preg_match("/\?{1}/",$CTR) and $CTR<>$CHK):
      return 3;                         //invalid check
    endif;
    $VAT=substr($NUM,0,1).$TIP.substr($NUM,1).$CHK;
    return false;
  case "IT":
    #
    # Italy
    #
    $FAN=strtoupper(preg_replace("/[^[:alnum:]\?]/","",$VAT));
    $OK=preg_match("/(^[0-9]{1,10})([0-9]{1}$|\?{1}$)/",$FAN,$REGS);
    if (!$OK):
      return 1;                         //invalid format
    endif;
    $NUM=$REGS[1];
    $CTR=$REGS[2];
    $NUM=str_pad($NUM,10,"0",STR_PAD_LEFT);
    if (substr($NUM,0,7)==0):
      return 2;                         //invalid value
    endif;
    if (substr($NUM,7)<1 or substr($NUM,7)>100 and substr($NUM,7)<>120 and substr($NUM,7)<>121):
      return 2;                         //invalid value
    endif;
    $CHK=0;
    for ($I=0; $I<strlen($NUM); $I++):
      $C=substr($NUM,$I,1)*($I%2==0?1:2);
      $CHK=$CHK+substr($C,0,1)+substr($C,1,1);
    endfor;
    $CHK=10-$CHK%10;
    $CHK=($CHK==10?0:$CHK);
    if (!preg_match("/\?{1}/",$CTR) and $CTR<>$CHK):
      return 3;                         //invalid check
    endif;
    $VAT=$NUM.$CHK;
    return false;
  case "LT":
    #
    # Lithuania
    #
    $FAN=strtoupper(preg_replace("/[^[:alnum:]\?]/","",$VAT));
    $OK=preg_match("/(^[0-9]{11})([0-9]{1}$|\?{1}$)/",$FAN,$REGS);
    if (!$OK):
      $OK=preg_match("/(^[0-9]{8})([0-9]{1}$|\?{1}$)/",$FAN,$REGS);
      if (!$OK):
        return 1;                       //invalid format
      endif;
    endif;
    $NUM=$REGS[1];
    $CTR=$REGS[2];
    if (substr($NUM,-1,1)<>1):
      return 2;                         //invalid value
    endif;
    $CHG=array(1,2,3,4,5,6,7,8,9,1,2,3,4);
    do {
      $CHK=0;
      for ($I=0; $I<strlen($NUM); $I++):
        $CHK=$CHK+$CHG[$I]*substr($NUM,$I,1);
      endfor;
      if (count($CHG)<13):
        break;
      endif;
      array_shift($CHG); array_shift($CHG);
    } while ($CHK%11==10);
    $CHK=$CHK%11;
    $CHK=($CHK==10?0:$CHK);
    if (!preg_match("/\?{1}/",$CTR) and $CTR<>$CHK):
      return 3;                         //invalid check
    endif;
    $VAT=$NUM.$CHK;
    return false;
  case "LU":
    #
    # Luxemburg
    #
    $FAN=strtoupper(preg_replace("/[^[:alnum:]\?]/","",$VAT));
    $OK=preg_match("/(^[0-9]{1,6})([0-9]{2}$|\?{1,2}$)/",$FAN,$REGS);
    if (!$OK):
      return 1;                         //invalid format
    endif;
    $NUM=$REGS[1];
    $CTR=$REGS[2];
    if ($NUM==0):
      return 2;                         //invalid value
    endif;
    $NUM=str_pad($NUM,6,"0",STR_PAD_LEFT);
    $CHK=str_pad($NUM%89,2,"0",STR_PAD_LEFT);
    if (!preg_match("/\?{1,2}/",$CTR) and $CTR<>$CHK):
      return 3;                         //invalid check
    endif;
    $VAT=$NUM.$CHK;
    return false;
  case "LV":
    #
    # Latvia
    #
    $FAN=strtoupper(preg_replace("/[^[:alnum:]\?]/","",$VAT));
    $OK=preg_match("/(^[4-9][0-9]{9})([0-9]{1}$|\?{1}$)/",$FAN,$REGS);
    if (!$OK):
      $OK=preg_match("/(^[0-3][0-9]{10})/",$FAN,$REGS);
      if (!$OK):
        return 1;                       //invalid format
      endif;
    endif;
    $NUM=$REGS[1];
    $CTR=$REGS[2];
    if (substr($NUM,0,1)<4):
      if (substr($NUM,0,2)<1):
        return 2;                       //invalid value
      endif;
      if (preg_match("/02/",substr($NUM,2,2))):
        if (substr($NUM,4,2)%4>0):
          if (substr($NUM,0,2)>28):
            return 2;                   //invalid value
          endif;
        else:
          if (substr($NUM,0,2)>29):
            return 2;                   //invalid value
          endif;
        endif;
      elseif (preg_match("/04|06|09|11/",substr($NUM,2,2))):
        if (substr($NUM,0,2)>30):
          return 2;                     //invalid value
        endif;
      else:
        if (substr($NUM,0,2)>31):
          return 2;                     //invalid value
        endif;
      endif;
      if (substr($NUM,2,2)<1 or substr($NUM,2,2)>12):
        return 2;                       //invalid value
      endif;
    endif;
    if (substr($NUM,0,1)>3):
      $CHG=array(9,1,4,8,3,10,2,5,7,6);
      $CHK=0;
      for ($I=0; $I<strlen($NUM); $I++):
        $CHK=$CHK+$CHG[$I]*substr($NUM,$I,1);
      endfor;
      if ($CHK%11==4 and substr($NUM,0,1)==9):
        $CHK=$CHK-45;
      endif;
      if ($CHK%11==4):
        $CHK=4-$CHK%11;
      elseif ($CHK%11>4):
        $CHK=14-$CHK%11;
      else:
        $CHK=3-$CHK%11;
      endif;
      if (!preg_match("/\?{1}/",$CTR) and $CTR<>$CHK):
        return 3;                       //invalid check
      endif;
    endif;
    $VAT=$NUM.$CHK;
    return false;
  case "MT":
    #
    # Malta
    #
    $FAN=strtoupper(preg_replace("/[^[:alnum:]\?]/","",$VAT));
    $OK=preg_match("/(^[0-9]{1,6})([0-9]{2}$|\?{1,2}$)/",$FAN,$REGS);
    if (!$OK):
      return 1;                         //invalid format
    endif;
    $NUM=$REGS[1];
    $CTR=$REGS[2];
    $NUM=str_pad($NUM,6,"0",STR_PAD_LEFT);
    if ($NUM<100001):
      return 2;                         //invalid value
    endif;
    $CHG=array(3,4,6,7,8,9);
    $CHK=0;
    for ($I=0; $I<strlen($NUM); $I++):
      $CHK=$CHK+$CHG[$I]*substr($NUM,$I,1);
    endfor;
    $CHK=str_pad(37-$CHK%37,2,"0",STR_PAD_LEFT);
    if (!preg_match("/\?{1,2}/",$CTR) and $CTR<>$CHK):
      return 3;                         //invalid check
    endif;
    $VAT=$NUM.$CHK;
    return false;
  case "NL":
    #
    # Netherlands
    #
    $FAN=strtoupper(preg_replace("/[^[:alnum:]\?]/","",$VAT));
    $OK=preg_match("/(^[0-9]{1,8})([0-9]{1}|\?{1})([B]{1})([0-9]{2}$)/",$FAN,$REGS);
    if (!$OK):
      return 1;                         //invalid format
    endif;
    $NUM=$REGS[1];
    $CTR=$REGS[2];
    $TIP=$REGS[3].$REGS[4];
    if ($NUM==0):
      return 2;                         //invalid value
    endif;
    $NUM=str_pad($NUM,8,"0",STR_PAD_LEFT);
    $CHK=0;
    for ($I=0; $I<strlen($NUM); $I++):
      $CHK=$CHK+(9-$I)*substr($NUM,$I,1);
    endfor;
    $CHK=$CHK%11;
    if ($CHK==10):
      return 2;                         //invalid value
    endif;
    if (!preg_match("/\?{1}/",$CTR) and $CTR<>$CHK):
      return 3;                         //invalid check
    endif;
    $VAT=$NUM.$CHK.$TIP;
    return false;
  case "PL":
    #
    # Poland
    #
    $FAN=strtoupper(preg_replace("/[^[:alnum:]\?]/","",$VAT));
    $OK=preg_match("/(^[0-9]{1,9})([0-9]{1}$|\?{1}$)/",$FAN,$REGS);
    if (!$OK):
      return 1;                         //invalid format
    endif;
    $NUM=$REGS[1];
    $CTR=$REGS[2];
    $NUM=str_pad($NUM,9,"0",STR_PAD_LEFT);
    $CHG=array(6,5,7,2,3,4,5,6,7);
    $CHK=0;
    for ($I=0; $I<strlen($NUM); $I++):
      $CHK=$CHK+$CHG[$I]*substr($NUM,$I,1);
    endfor;
    $CHK=$CHK%11;
    if ($CHK==10):
      return 2;                         //invalid value
    endif;
    if (!preg_match("/\?{1}/",$CTR) and $CTR<>$CHK):
      return 3;                         //invalid check
    endif;
    $VAT=$NUM.$CHK;
    return false;
  case "PT":
    #
    # Portugal
    #
    $FAN=strtoupper(preg_replace("/[^[:alnum:]\?]/","",$VAT));
    $OK=preg_match("/(^[0-9]{1,8})([0-9]{1}$|\?{1}$)/",$FAN,$REGS);
    if (!$OK):
      return 1;                         //invalid format
    endif;
    $NUM=$REGS[1];
    $CTR=$REGS[2];
    if (substr($NUM,0,1)<"1"):
      return 2;                         //invalid value
    endif;
    $NUM=substr($NUM,0,1).str_pad(substr($NUM,1),7,"0",STR_PAD_LEFT);
    $CHK=0;
    for ($I=0; $I<strlen($NUM); $I++):
      $CHK=$CHK+(9-$I)*substr($NUM,$I,1);
    endfor;
    $CHK=11-$CHK%11;
    $CHK=($CHK>9?0:$CHK);
    if (!preg_match("/\?{1}/",$CTR) and $CTR<>$CHK):
      return 3;                         //invalid check
    endif;
    $VAT=$NUM.$CHK;
    return false;
  case "RO":
    #
    # Romania
    #
    $FAN=strtoupper(preg_replace("/[^[:alnum:]\?]/","",$VAT));
    $OK=preg_match("/(^[0-9]{1,9})([0-9]{1}$|\?{1}$)/",$FAN,$REGS);
    if (!$OK):
      $OK=preg_match("/(^[12346][0-9]{11})([0-9]{1}$|\?{1}$)/",$FAN,$REGS);
      if (!$OK):
        return 1;                       //invalid format
      endif;
    endif;
    $NUM=$REGS[1];
    $CTR=$REGS[2];
    if (strlen($NUM)==12):
      if (substr($NUM,3,2)<1 or substr($NUM,3,2)>12):
        return 2;                       //invalid value
      endif;
      if (preg_match("/02/",substr($NUM,3,2))):
        if (substr($NUM,1,2)%4>0):
          if (substr($NUM,5,2)>28):
            return 2;                   //invalid value
          endif;
        else:
          if (substr($NUM,5,2)>29):
            return 2;                   //invalid value
          endif;
        endif;
      elseif (preg_match("/04|06|09|11/",substr($NUM,3,2))):
        if (substr($NUM,5,2)>30):
          return 2;                     //invalid value
        endif;
      else:
        if (substr($NUM,5,2)>31):
          return 2;                     //invalid value
        endif;
      endif;
      $CHG=array(2,7,9,1,4,6,3,5,8,2,7,9);
      $CHK=0;
      for ($I=0; $I<strlen($NUM); $I++):
        $CHK=$CHK+$CHG[$I]*substr($NUM,$I,1);
      endfor;
      $CHK=$CHK%11;
      $CHK=($CHK==10?1:$CHK);
      if (!preg_match("/\?{1}/",$CTR) and $CTR<>$CHK):
        return 3;                       //invalid check
      endif;
    else:
      $NUM=str_pad($NUM,9,"0",STR_PAD_LEFT);
      $CHG=array(7,5,3,2,1,7,5,3,2);
      $CHK=0;
      for ($I=0; $I<strlen($NUM); $I++):
        $CHK=$CHK+$CHG[$I]*substr($NUM,$I,1);
      endfor;
      $CHK=$CHK*10%11;
      $CHK=($CHK==10?0:$CHK);
      if (!preg_match("/\?{1}/",$CTR) and $CTR<>$CHK):
        return 3;                       //invalid check
      endif;
    endif;
    $VAT=$NUM.$CHK;
    return false;
  case "SE":
    #
    # Sweden
    #
    $FAN=strtoupper(preg_replace("/[^[:alnum:]\?]/","",$VAT));
    $OK=preg_match("/(^[0-9]{1,9})([0-9]{1}|\?{1})([0-9]{2}$)/",$FAN,$REGS);
    if (!$OK):
      return 1;                         //invalid format
    endif;
    $NUM=$REGS[1];
    $CTR=$REGS[2];
    $TIP=$REGS[3];
    if ($TIP==0):
      return 2;                         //invalid value
    endif;
    $NUM=str_pad($NUM,9,"0",STR_PAD_LEFT);
    $CHK=0;
    for ($I=0; $I<strlen($NUM); $I++):
      $C=substr($NUM,$I,1)*($I%2>0?1:2);
      $CHK=$CHK+substr($C,0,1)+substr($C,1,1);
    endfor;
    $CHK=10-$CHK%10;
    $CHK=($CHK==10?0:$CHK);
    if (!preg_match("/\?{1}/",$CTR) and $CTR<>$CHK):
      return 3;                         //invalid check
    endif;
    $VAT=$NUM.$CHK.$TIP;
    return false;
  case "SI":
    #
    # Slovenia
    #
    $FAN=strtoupper(preg_replace("/[^[:alnum:]\?]/","",$VAT));
    $OK=preg_match("/(^[0-9]{1,7})([0-9]{1}$|\?{1}$)/",$FAN,$REGS);
    if (!$OK):
      return 1;                         //invalid format
    endif;
    $NUM=$REGS[1];
    $CTR=$REGS[2];
    $NUM=str_pad($NUM,7,"0",STR_PAD_LEFT);
    if ($NUM<1000000):
      return 2;                         //invalid value
    endif;
    $CHG=array(8,7,6,5,4,3,2);
    $CHK=0;
    for ($I=0; $I<strlen($NUM); $I++):
      $CHK=$CHK+$CHG[$I]*substr($NUM,$I,1);
    endfor;
    $CHK=11-$CHK%11;
    $CHK=($CHK==10?0:$CHK);
    $CHK=($CHK==11?1:$CHK);
    if (!preg_match("/\?{1}/",$CTR) and $CTR<>$CHK):
      return 3;                         //invalid check
    endif;
    $VAT=$NUM.$CHK;
    return false;
  case "SK":
    #
    # Slovakia
    #
    $FAN=strtoupper(preg_replace("/[^[:alnum:]\?]/","",$VAT));
    $OK=preg_match("/(^[0-9]{9})(\?{1}$)/",$FAN,$REGS);
    if (!$OK):
      $OK=preg_match("/(^[0-9]{10}$)/",$FAN,$REGS);
      if (!$OK):
        $OK=preg_match("/(^[0-9]{9}$)/",$FAN,$REGS);
        if (!$OK):
          return 1;                     //invalid format
        endif;
      endif;
    endif;
    $NUM=$REGS[1];
    $CTR=$REGS[2];
    if ($CTR>""):
      if (substr($NUM,0,2)>0):
        return 2;                       //invalid value
      endif;
    elseif (strlen($NUM)==9 or (substr($NUM,0,2)>0 and substr($NUM,0,2)<>10 and substr($NUM,0,2)<>20)):
      if (strlen($NUM)==9):
        if (substr($NUM,0,2)>53):
          return 2;                     //invalid value
        endif;
      else:
        if (substr($NUM,0,2)<54):
          return 2;                     //invalid value
        endif;
      endif;
      if (substr($NUM,2,2)<1 or substr($NUM,2,2)>12 and substr($NUM,2,2)<51 or substr($NUM,2,2)>62):
        return 2;                       //invalid value
      endif;
      if (substr($NUM,4,2)<1):
        return 2;                       //invalid value
      endif;
      if (preg_match("/02|52/",substr($NUM,2,2))):
        if (substr($NUM,0,2)%4>0):
          if (substr($NUM,4,2)>28):
            return 2;                   //invalid value
          endif;
        else:
          if (substr($NUM,4,2)>29):
            return 2;                   //invalid value
          endif;
        endif;
      elseif (preg_match("/04|06|09|11|54|56|59|61/",substr($NUM,2,2))):
        if (substr($NUM,4,2)>30):
          return 2;                     //invalid value
        endif;
      else:
        if (substr($NUM,4,2)>31):
          return 2;                     //invalid value
        endif;
      endif;
    endif;
    if ($CTR>""):
      $CHG=array(8,7,6,5,4,3,2);
      $CHK=0;
      for ($I=2; $I<strlen($NUM); $I++):
        $CHK=$CHK+$CHG[$I]*substr($NUM,$I,1);
      endfor;
      $CHK=11-$CHK%11;
      $CHK=($CHK==10?0:$CHK);
      $CHK=($CHK==11?1:$CHK);
      $VAT=$NUM.$CHK;
    else:
      $VAT=$NUM;
    endif;
    return false;
  default:
    return 4;                           //invalid country
  endswitch;
}



}
