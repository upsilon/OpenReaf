INSERT INTO `m_staff` (`localgovcode`, `bushocode`, `staffid`, `appdatefrom`, `staffnum`, `pwd`, `staffname`, `tourokukbn`, `kengencode1`, `kengencode2`, `kengencode3`, `kengencode4`, `kengencode5`, `kengencode6`, `haishidate`,  `upddate`, `updtime`, `updid`) VALUES ('001', '01', '000099', '20110401', 'initialStaff', '1234', '管理者', '3', '10', '20', '30', '40', '50', '60', '', '20110401', '000000', '000099');
INSERT INTO `m_systemparameter` (`localgovcode`, `sitecloseflg`, `siteclosemessage`) VALUES ('001', '1', '<div style=\"margin:180px auto;\">\r\n<img src=\"image/kouji.gif\" alt=\"工事中\" />\r\n現在工事中です。もうしばらくお持ちください。<br><br>\r\n</div>');
INSERT INTO `m_busho` (`localgovcode`, `bushocode`, `bushoname`, `bushoshortname`, `upddate`, `updtime`, `updid`) VALUES ('001', '01', '初期設定', '初期設定', '20110401', '000000', '000099');
INSERT INTO `m_saiban` (`localgovcode`, `saibancode`, `saibanno`, `saibannolng`, `saibanflg`, `displayorder`, `upddate`, `updtime`, `updid`) VALUES ('001', 'UserID', 0, 6, '0', '1', '20110401', '000000', '000099');
INSERT INTO `m_saiban` (`localgovcode`, `saibancode`, `saibanno`, `saibannolng`, `saibanflg`, `displayorder`, `upddate`, `updtime`, `updid`) VALUES ('001', 'YoyakuNum', 0, 6, '0', '0', '20110401', '000000', '000099');
INSERT INTO `m_saiban` (`localgovcode`, `saibancode`, `saibanno`, `saibannolng`, `saibanflg`, `displayorder`, `upddate`, `updtime`, `updid`) VALUES ('001', 'BihinYoyakuNum', 0, 6, '0', '2', '20110401', '000000', '000099');
INSERT INTO `m_saiban` (`localgovcode`, `saibancode`, `saibanno`, `saibannolng`, `saibanflg`, `displayorder`, `upddate`, `updtime`, `updid`) VALUES ('001', 'Application', 0, 6, '0', '3', '20110401', '000000', '000099');
INSERT INTO `m_saiban` (`localgovcode`, `saibancode`, `saibanno`, `saibannolng`, `saibanflg`, `displayorder`, `upddate`, `updtime`, `updid`) VALUES ('001', 'Permit', 0, 6, '0', '4', '20110401', '000000', '000099');
INSERT INTO `m_saiban` (`localgovcode`, `saibancode`, `saibanno`, `saibannolng`, `saibanflg`, `displayorder`, `upddate`, `updtime`, `updid`) VALUES ('001', 'Reciept', 0, 6, '0', '5', '20110401', '000000', '000099');
INSERT INTO `m_canceljiyucode` (`localgovcode`, `cancelcode`, `cancelkbn`, `canceljiyuname`, `rate`, `upddate`, `updtime`, `updid`) VALUES ('001', '00', '0', '天候都合', 100, '20110401', '000000', 'SetUp');
INSERT INTO `m_canceljiyucode` (`localgovcode`, `cancelcode`, `cancelkbn`, `canceljiyuname`, `rate`, `upddate`, `updtime`, `updid`) VALUES ('001', '01', '1', '利用者都合', 100, '20110401', '000000', 'SetUp');
INSERT INTO `m_canceljiyucode` (`localgovcode`, `cancelcode`, `cancelkbn`, `canceljiyuname`, `rate`, `upddate`, `updtime`, `updid`) VALUES ('001', '02', '1', '施設都合', 100, '20110401', '000000', 'SetUp');
INSERT INTO `m_canceljiyucode` (`localgovcode`, `cancelcode`, `cancelkbn`, `canceljiyuname`, `rate`, `upddate`, `updtime`, `updid`) VALUES ('001', '03', '1', 'その他', 100, '20110401', '000000', 'Setup');
INSERT INTO `m_mokuteki` (`localgovcode`, `mokutekicode`, `mokutekiname`, `mokutekiskbcode`, `mokutekidaicode`, `delflg`, `upddate`, `updtime`, `updid`) VALUES ('001', '00', '--', '1000', '01', '1', '20110401', '000000', '000099');
INSERT INTO `m_codename` VALUES 
('001','CardUse','0','無','20110401','000000','000099'),
('001','CardUse','1','有','20110401','000000','000099'),
('001','HonninKakuninKubun','1','運転免許証','20110401','000000','000099'),
('001','HonninKakuninKubun','2','健康保険','20110401','000000','000099'),
('001','HonninKakuninKubun','3','パスポート','20110401','000000','000099'),
('001','HonninKakuninKubun','4','住基カード','20110401','000000','000099'),
('001','HonninKakuninKubun','5','その他','20110401','000000','000099'),
('001','KojinDanKbn','1','個人','20110401','000000','000099'),
('001','KojinDanKbn','2','団体','20110401','000000','000099'),
('001','KoukaiKbn','0','不可','20110401','000000','000099'),
('001','KoukaiKbn','1','可','20110401','000000','000099'),
('001','YoyakuKbn','01','抽選','20110401','000000','000099'),
('001','YoyakuKbn','02','一般','20110401','000000','000099'),
('001','YoyakuKbn','03','休館','20110401','000000','000099'),
('001','YoyakuKbn','04','保守','20110401','000000','000099'),
('001','YoyakuKbn','05','開放','20110401','000000','000099'),
('001','UserKubun','1','在住','20110401','000000','000099'),
('001','UserKubun','2','在勤','20110401','000000','000099'),
('001','UserKubun','3','在学','20110401','000000','000099'),
('001','UserKubun','4','市外','20110401','000000','000099');
