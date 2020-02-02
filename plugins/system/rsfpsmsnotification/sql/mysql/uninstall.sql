DROP TABLE IF EXISTS `#__rsform_smsnotification`;

DELETE FROM #__rsform_config WHERE SettingName = 'smsnotification.smsservice';
DELETE FROM #__rsform_config WHERE SettingName = 'smsnotification.usessl';
DELETE FROM #__rsform_config WHERE SettingName = 'smsnotification.clockworkkey';
DELETE FROM #__rsform_config WHERE SettingName = 'smsnotification.twiliosid';
DELETE FROM #__rsform_config WHERE SettingName = 'smsnotification.twiliotoken';
DELETE FROM #__rsform_config WHERE SettingName = 'smsnotification.smsglobaluser';
DELETE FROM #__rsform_config WHERE SettingName = 'smsnotification.smsglobalpassword';
DELETE FROM #__rsform_config WHERE SettingName = 'smsnotification.clickatellusername';
DELETE FROM #__rsform_config WHERE SettingName = 'smsnotification.clickatellpassword';
DELETE FROM #__rsform_config WHERE SettingName = 'smsnotification.clickatellapiid';
DELETE FROM #__rsform_config WHERE SettingName = 'smsnotification.nexmokey';
DELETE FROM #__rsform_config WHERE SettingName = 'smsnotification.nexmosecret';
