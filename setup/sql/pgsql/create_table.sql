--
-- Table structure for table h_fee
--

CREATE TABLE h_fee (
  localgovcode char(3) NOT NULL default '',
  shisetsucode char(3) NOT NULL default '',
  shitsujyocode char(2) NOT NULL default '',
  combino int NOT NULL default '0',
  usedate char(8) NOT NULL default '',
  usetimefrom char(6) NOT NULL default '',
  usetimeto char(6) default NULL,
  feesinkbn char(2) default NULL,
  yoyakunum varchar(10) NOT NULL default '',
  basefee decimal(9,2) NOT NULL default '0.00',
  shisetsufee decimal(9,2) NOT NULL default '0.00',
  tax decimal(9,2) NOT NULL default '0.00',
  suuryo decimal(9,2) NOT NULL default '0.00',
  suuryotani varchar(4) NOT NULL default '',
  surcharge varchar(4) NOT NULL default '',
  paykbn smallint NOT NULL default '0',
  bihinyoyakunum varchar(10) default NULL,
  bihinfee decimal(9,2) NOT NULL default '0.00',
  optionfee1 decimal(9,2) NOT NULL default '0.00',
  optionfee2 decimal(9,2) NOT NULL default '0.00',
  optionfee3 decimal(9,2) NOT NULL default '0.00',
  optionfee4 decimal(9,2) NOT NULL default '0.00',
  optionfee5 decimal(9,2) NOT NULL default '0.00',
  chousei_reason varchar(128) default NULL,
  useninzu smallint NOT NULL default '0',
  ninzu1 smallint NOT NULL default '0',
  ninzu2 smallint NOT NULL default '0',
  ninzu3 smallint NOT NULL default '0',
  ninzu4 smallint NOT NULL default '0',
  ninzu5 smallint NOT NULL default '0',
  ninzu6 smallint NOT NULL default '0',
  ninzu7 smallint NOT NULL default '0',
  ninzu8 smallint NOT NULL default '0',
  ninzu9 smallint NOT NULL default '0',
  ninzu10 smallint NOT NULL default '0',
  ninzu11 smallint NOT NULL default '0',
  ninzu12 smallint NOT NULL default '0',
  ninzu13 smallint NOT NULL default '0',
  ninzu14 smallint NOT NULL default '0',
  ninzu15 smallint NOT NULL default '0',
  ninzu16 smallint NOT NULL default '0',
  upddate char(8) default NULL,
  updtime char(6) default NULL,
  updid varchar(16) default NULL,
  historycode varchar(14) default NULL,
  lstupddate char(8) default NULL,
  lstupdtime char(6) default NULL,
  lstupdid varchar(16) default NULL,
  PRIMARY KEY (localgovcode,yoyakunum)
);
CREATE INDEX h_fee_idx_1 ON h_fee (localgovcode,shisetsucode,usedate);
CREATE INDEX h_fee_idx_2 ON h_fee (localgovcode,shisetsucode,shitsujyocode,usedate);

--
-- Table structure for table h_pullout
--

CREATE TABLE h_pullout (
  localgovcode char(3) NOT NULL default '',
  shisetsucode char(3) NOT NULL default '',
  shitsujyocode char(2) NOT NULL default '',
  mencode char(2) NOT NULL default '',
  combino int NOT NULL default '0',
  usedate char(8) NOT NULL default '',
  usetimefrom char(6) NOT NULL default '',
  userid varchar(128) NOT NULL default '',
  usetimeto char(6) NOT NULL default '',
  pulloutukedate char(8) NOT NULL default '',
  pulloutuketime char(6) NOT NULL default '',
  pulloutyoyakunum varchar(10) NOT NULL default '',
  komasu smallint NOT NULL default '0',
  baseshisetsufee decimal(9,2) NOT NULL default '0.00',
  shisetsufee decimal(9,2) NOT NULL default '0.00',
  shisetsutax decimal(9,2) NOT NULL default '0.00',
  pulloutjisshidate char(8) default NULL,
  pulloutjisshitime char(4) default NULL,
  pulloutjoukyoukbn char(1) default NULL,
  pulloutfixflg char(1) default NULL,
  sendmaildate varchar(8) default NULL,
  bikou text,
  mokutekicode char(2) default NULL,
  usekbn char(2) default NULL,
  daikouid varchar(16) default NULL,
  hitfixappdate varchar(8) default NULL,
  hitfixapptime varchar(6) default NULL,
  upddate char(8) default NULL,
  updtime char(6) default NULL,
  updid varchar(16) default NULL,
  lstupddate char(8) default NULL,
  lstupdtime char(6) default NULL,
  lstupdid varchar(16) default NULL,
  PRIMARY KEY (localgovcode,shisetsucode,shitsujyocode,mencode,pulloutyoyakunum)
);
CREATE INDEX h_pullout_idx_1 ON h_pullout (localgovcode,pulloutyoyakunum);
CREATE INDEX h_pullout_idx_2 ON h_pullout (localgovcode,userid);

--
-- Table structure for table h_yoyaku
--

CREATE TABLE h_yoyaku (
  localgovcode char(3) NOT NULL default '',
  shisetsucode char(3) NOT NULL default '',
  shitsujyocode char(2) NOT NULL default '',
  mencode char(2) NOT NULL default '',
  combino int NOT NULL default '0',
  usedatefrom char(8) NOT NULL default '',
  usetimefrom char(6) NOT NULL default '',
  usetimeto char(6) default NULL,
  yoyakunum varchar(10) NOT NULL default '',
  userid varchar(128) NOT NULL default '',
  komasu smallint NOT NULL default '0',
  baseshisetsufee decimal(9,2) NOT NULL default '0.00',
  shisetsufee decimal(9,2) NOT NULL default '0.00',
  shisetsutax decimal(9,2) NOT NULL default '0.00',
  shisetsupaylimitdate varchar(8) default NULL,
  utensinflg char(1) default NULL,
  honyoyakukbn char(2) default NULL,
  karishinsaid varchar(16) default NULL,
  shinsakbn char(1) default NULL,
  shinsareason varchar(100) default NULL,
  shinsadate varchar(8) default NULL,
  useukeflg char(1) default NULL,
  escapeflg char(1) default NULL,
  bikou text,
  yoyakukbn char(2) default NULL,
  yoyakuname varchar(128) default NULL,
  mokutekicode char(2) default NULL,
  usekbn char(2) default NULL,
  daikouid varchar(16) default NULL,
  appdate char(8) default NULL,
  apptime char(6) default NULL,
  canceljiyucode varchar(3) default NULL,
  cancelstaffid varchar(16) default NULL,
  upddate char(8) default NULL,
  updtime char(6) default NULL,
  updid varchar(16) default NULL,
  lstupddate char(8) default NULL,
  lstupdtime char(6) default NULL,
  lstupdid varchar(16) default NULL,
  PRIMARY KEY (localgovcode,shisetsucode,shitsujyocode,mencode,yoyakunum)
);
CREATE INDEX h_yoyaku_idx_1 ON h_yoyaku (localgovcode,yoyakunum);
CREATE INDEX h_yoyaku_idx_2 ON h_yoyaku (localgovcode,userid);
CREATE INDEX h_yoyaku_idx_3 ON h_yoyaku (localgovcode,shisetsucode,honyoyakukbn);

--
-- Table structure for table m_bihin
--

CREATE TABLE m_bihin (
  bihincode int NOT NULL default '0',
  localgovcode char(3) NOT NULL default '',
  shisetsucode char(3) NOT NULL default '',
  depotcode char(3) NOT NULL,
  bihinname varchar(64) NOT NULL,
  price decimal(9,2) NOT NULL,
  unit varchar(8) default NULL,
  total int NOT NULL default '0',
  remain int NOT NULL default '0',
  no_counting smallint NOT NULL default '0',
  openflg smallint NOT NULL default '0',
  disable smallint NOT NULL default '0',
  bihinkbn smallint NOT NULL default '0',
  unit_time int NOT NULL default '60',
  by_hour smallint NOT NULL default '0',
  dispseq smallint NOT NULL default '0',
  regdate int NOT NULL default '0',
  upddate int NOT NULL default '0',
  updid varchar(16) default NULL,
  PRIMARY KEY (bihincode)
);
CREATE INDEX m_bihin_idx_1 ON m_bihin (localgovcode,shisetsucode,depotcode);

--
-- Table structure for table m_busho
--

CREATE TABLE m_busho (
  localgovcode char(3) NOT NULL default '',
  bushocode varchar(16) NOT NULL default '',
  bushoname varchar(40) default NULL,
  bushoshortname varchar(20) default NULL,
  upddate char(8) default NULL,
  updtime char(6) default NULL,
  updid varchar(16) default NULL,
  PRIMARY KEY (localgovcode,bushocode)
);

--
-- Table structure for table m_canceljiyucode
--

CREATE TABLE m_canceljiyucode (
  localgovcode char(3) NOT NULL default '',
  cancelcode char(2) NOT NULL default '',
  cancelkbn char(1) default NULL,
  canceljiyuname varchar(20) default NULL,
  rate smallint NOT NULL default '100',
  upddate char(8) default NULL,
  updtime char(6) default NULL,
  updid varchar(16) default NULL,
  PRIMARY KEY (localgovcode,cancelcode)
);

--
-- Table structure for table m_closedday
--

CREATE TABLE m_closedday (
  localgovcode char(3) NOT NULL default '',
  shisetsucode char(3) NOT NULL default '',
  shitsujyocode char(2) NOT NULL default '',
  appdatefrom char(8) NOT NULL default '',
  monthdayfrom char(4) NOT NULL default '',
  monthdayto char(4) default NULL,
  holiclosedflg char(1) NOT NULL default '0',
  maishu1 char(1) default NULL,
  dai1shu1 char(1) default NULL,
  dai2shu1 char(1) default NULL,
  dai3shu1 char(1) default NULL,
  dai4shu1 char(1) default NULL,
  dai5shu1 char(1) default NULL,
  sun1 char(1) default NULL,
  mon1 char(1) default NULL,
  tue1 char(1) default NULL,
  wed1 char(1) default NULL,
  thu1 char(1) default NULL,
  fri1 char(1) default NULL,
  sat1 char(1) default NULL,
  maishu2 char(1) default NULL,
  dai1shu2 char(1) default NULL,
  dai2shu2 char(1) default NULL,
  dai3shu2 char(1) default NULL,
  dai4shu2 char(1) default NULL,
  dai5shu2 char(1) default NULL,
  sun2 char(1) default NULL,
  mon2 char(1) default NULL,
  tue2 char(1) default NULL,
  wed2 char(1) default NULL,
  thu2 char(1) default NULL,
  fri2 char(1) default NULL,
  sat2 char(1) default NULL,
  monthfirst3 char(1) default NULL,
  monthfainal3 char(1) default NULL,
  sun3 char(1) default NULL,
  mon3 char(1) default NULL,
  tue3 char(1) default NULL,
  wed3 char(1) default NULL,
  thu3 char(1) default NULL,
  fri3 char(1) default NULL,
  sat3 char(1) default NULL,
  closeddaychgflg char(1) NOT NULL default '0',
  koteiclosedday1 varchar(2) default NULL,
  koteiclosedday2 varchar(2) default NULL,
  koteiclosedday3 varchar(2) default NULL,
  exception_day varchar(64) NOT NULL default '',
  upddate char(8) default NULL,
  updtime char(6) default NULL,
  updid varchar(16) default NULL,
  PRIMARY KEY (localgovcode,shisetsucode,shitsujyocode,appdatefrom,monthdayfrom)
);

--
-- Table structure for table m_codename
--

CREATE TABLE m_codename (
  localgovcode char(3) NOT NULL default '',
  codeid varchar(30) NOT NULL default '',
  code varchar(2) NOT NULL default '',
  codename varchar(20) default NULL,
  upddate char(8) default NULL,
  updtime char(6) default NULL,
  updid varchar(16) default NULL,
  PRIMARY KEY (localgovcode,codeid,code)
);

--
-- Table structure for table m_depot
--

CREATE TABLE m_depot (
  localgovcode char(3) NOT NULL default '',
  shisetsucode char(3) NOT NULL default '',
  depotcode char(2) NOT NULL default '',
  depotname varchar(64) NOT NULL,
  openflg smallint NOT NULL default '0',
  dispseq smallint NOT NULL default '0',
  upddate int NOT NULL default '0',
  updid varchar(16) default NULL,
  PRIMARY KEY (localgovcode,shisetsucode,depotcode)
);

--
-- Table structure for table m_extracharge
--

CREATE TABLE m_extracharge (
  localgovcode char(3) NOT NULL default '',
  extracode char(2) NOT NULL default '',
  extraname varchar(30) default NULL,
  rate smallint NOT NULL default '0',
  upddate char(8) default NULL,
  updtime char(6) default NULL,
  updid varchar(16) default NULL,
  PRIMARY KEY (localgovcode,extracode)
);

--
-- Table structure for table m_feekbn
--

CREATE TABLE m_feekbn (
  localgovcode char(3) NOT NULL default '',
  feekbn char(2) NOT NULL default '',
  feekbnname varchar(30) default NULL,
  komapayflg char(1) default NULL,
  upddate char(8) default NULL,
  updtime char(6) default NULL,
  updid varchar(16) default NULL,
  PRIMARY KEY (localgovcode,feekbn)
);

--
-- Table structure for table m_fuzokushitsujyou
--

CREATE TABLE m_fuzokushitsujyou (
  localgovcode char(3) NOT NULL default '',
  shisetsucode char(3) NOT NULL default '',
  shitsujyocode char(2) NOT NULL default '',
  combino int NOT NULL default '0',
  fuzokucode char(2) NOT NULL default '',
  upddate char(8) default NULL,
  updtime char(6) default NULL,
  updid varchar(16) default NULL,
  PRIMARY KEY (localgovcode,shisetsucode,shitsujyocode,combino,fuzokucode)
);

--
-- Table structure for table m_genmen
--

CREATE TABLE m_genmen (
  localgovcode char(3) NOT NULL default '',
  koteigencode char(2) NOT NULL default '',
  koteigenname varchar(30) default NULL,
  rate smallint NOT NULL default '0',
  bihinrate smallint NOT NULL default '0',
  upddate char(8) default NULL,
  updtime char(6) default NULL,
  updid varchar(16) default NULL,
  PRIMARY KEY (localgovcode,koteigencode)
);

--
-- Table structure for table m_holiday
--

CREATE TABLE m_holiday (
  localgovcode char(3) NOT NULL default '',
  heichouholiday char(8) NOT NULL default '',
  heichouholidayname varchar(20) default NULL,
  holiflg char(1) default NULL,
  upddate char(8) default NULL,
  updtime char(6) default NULL,
  updid varchar(16) default NULL,
  PRIMARY KEY (localgovcode,heichouholiday)
);

--
-- Table structure for table m_men
--

CREATE TABLE m_men (
  localgovcode char(3) NOT NULL default '',
  shisetsucode char(3) NOT NULL default '',
  shitsujyocode char(2) NOT NULL default '',
  appdatefrom char(8) NOT NULL default '',
  mencode char(2) NOT NULL default '',
  menname varchar(20) default NULL,
  menskbcode int NOT NULL default '0',
  menname2 varchar(40) default NULL,
  teiin int NOT NULL default '0',
  teiin_min int NOT NULL default '0',
  pulloutukemnflg char(1) default NULL,
  menhaishidate varchar(8) default NULL,
  upddate char(8) default NULL,
  updtime char(6) default NULL,
  updid varchar(16) default NULL,
  PRIMARY KEY (localgovcode,shisetsucode,shitsujyocode,appdatefrom,mencode)
);

--
-- Table structure for table m_mencombination
--

CREATE TABLE m_mencombination (
  localgovcode char(3) NOT NULL default '',
  shisetsucode char(3) NOT NULL default '',
  shitsujyocode char(2) NOT NULL default '',
  combino int NOT NULL default '0',
  combiskbno int NOT NULL default '0',
  mencode char(2) NOT NULL default '',
  combiname varchar(64) default NULL,
  combiname2 varchar(128) default NULL,
  openflg char(1) NOT NULL default '0',
  openkbn varchar(32) NOT NULL default '1,1,1,1,1,1,1,1,1,1,1,1,0,0',
  openkbn_disable char(1) NOT NULL default '1',
  upddate char(8) default NULL,
  updtime char(6) default NULL,
  updid varchar(16) default NULL,
  PRIMARY KEY (localgovcode,shisetsucode,shitsujyocode,combino,mencode)
);

--
-- Table structure for table m_mokuteki
--

CREATE TABLE m_mokuteki (
  localgovcode char(3) NOT NULL default '',
  mokutekicode char(2) NOT NULL default '',
  mokutekiname varchar(32) default NULL,
  mokutekiname2 varchar(64) default NULL,
  mokutekiskbcode int NOT NULL default '0',
  mokutekidaicode char(2) default NULL,
  delflg char(1) default '',
  upddate char(8) default NULL,
  updtime char(6) default NULL,
  updid varchar(16) default NULL,
  PRIMARY KEY (localgovcode,mokutekicode)
);

--
-- Table structure for table m_saiban
--

CREATE TABLE m_saiban (
  localgovcode char(3) NOT NULL default '',
  saibancode varchar(30) NOT NULL default '',
  saibanno int NOT NULL default '0',
  saibannolng smallint NOT NULL default '0',
  prefix varchar(8) default NULL,
  suffix varchar(8) default NULL,
  saibanflg char(1) NOT NULL default '0',
  displayorder smallint NOT NULL default '0',
  upddate char(8) default NULL,
  updtime char(6) default NULL,
  updid varchar(16) default NULL,
  PRIMARY KEY (localgovcode,saibancode)
);

--
-- Table structure for table m_shisetsu
--

CREATE TABLE m_shisetsu (
  localgovcode char(3) NOT NULL default '',
  shisetsuclassdaicode char(2) default NULL,
  shisetsuclasscode char(2) default NULL,
  shisetsucode char(3) NOT NULL default '',
  appdatefrom char(8) NOT NULL default '',
  shisetsuskbcode int NOT NULL default '0',
  shisetsuname varchar(32) default NULL,
  shisetsuname2 varchar(64) default NULL,
  rangebusyocode varchar(16) default NULL,
  adr varchar(60) default NULL,
  tel1 varchar(5) default NULL,
  tel2 varchar(4) default NULL,
  tel3 varchar(4) default NULL,
  telno21 varchar(5) default NULL,
  telno22 varchar(4) default NULL,
  telno23 varchar(4) default NULL,
  guideurl varchar(160) default NULL,
  showguideflg char(1) NOT NULL default '1',
  showdanjyoninzuflg char(1) NOT NULL default '0',
  showoutofserviceflg char(1) NOT NULL default '0',
  showeventflg char(1) NOT NULL default '0',
  cancelfeeflg char(1) NOT NULL default '0',
  fractionflg char(1) NOT NULL default '0',
  optionkbn1 char(2) default NULL,
  optionkbn2 char(2) default NULL,
  openflg char(1) NOT NULL default '0',
  haishidate varchar(8) default '',
  upddate char(8) default NULL,
  updtime char(6) default NULL,
  updid varchar(16) default NULL,
  shinsaflg char(1) NOT NULL default '0',
  limitflg char(1) NOT NULL default '0',
  pulloutlimitflg char(1) NOT NULL default '0',
  pulloutmonlimitkbn char(1) NOT NULL default '0',
  grouppulloutmonlimit int NOT NULL default '0',
  personalpulloutmonlimit int NOT NULL default '0',
  grouppulloutmon1limit int NOT NULL default '0',
  personalpulloutmon1limit int NOT NULL default '0',
  grouppulloutmon2limit int NOT NULL default '0',
  personalpulloutmon2limit int NOT NULL default '0',
  groupippanmonlimit int NOT NULL default '0',
  personalippanmonlimit int NOT NULL default '0',
  weklimitflg char(1) NOT NULL default '0',
  pulloutweklimitflg char(1) NOT NULL default '0',
  grouppulloutweklimit int NOT NULL default '0',
  personalpulloutweklimit int NOT NULL default '0',
  groupippanweklimit int NOT NULL default '0',
  personalippanweklimit int NOT NULL default '0',
  daylimitflg char(1) NOT NULL default '0',
  pulloutdaylimitflg char(1) NOT NULL default '0',
  grouppulloutdaylimit int NOT NULL default '0',
  personalpulloutdaylimit int NOT NULL default '0',
  groupippandaylimit int NOT NULL default '0',
  personalippandaylimit int NOT NULL default '0',
  shisetsumaster varchar(64) default NULL,
  PRIMARY KEY (localgovcode,shisetsucode,appdatefrom)
);

--
-- Table structure for table m_shisetsuclass
--

CREATE TABLE m_shisetsuclass (
  localgovcode char(3) NOT NULL default '',
  shisetsuclasscode char(2) NOT NULL default '',
  shisetsuclassskbcode int NOT NULL default '0',
  shisetsuclassname varchar(32) default NULL,
  shisetsuclassname2 varchar(64) default NULL,
  delflg char(1) NOT NULL default '0',
  upddate char(8) default NULL,
  updtime char(6) default NULL,
  updid varchar(16) default NULL,
  limitflg char(1) NOT NULL default '0',
  pulloutlimitflg char(1) NOT NULL default '0',
  pulloutmonlimitkbn char(1) NOT NULL default '0',
  grouppulloutmonlimit int NOT NULL default '0',
  personalpulloutmonlimit int NOT NULL default '0',
  grouppulloutmon1limit int NOT NULL default '0',
  personalpulloutmon1limit int NOT NULL default '0',
  grouppulloutmon2limit int NOT NULL default '0',
  personalpulloutmon2limit int NOT NULL default '0',
  groupippanmonlimit int NOT NULL default '0',
  personalippanmonlimit int NOT NULL default '0',
  weklimitflg char(1) NOT NULL default '0',
  pulloutweklimitflg char(1) NOT NULL default '0',
  grouppulloutweklimit int NOT NULL default '0',
  personalpulloutweklimit int NOT NULL default '0',
  groupippanweklimit int NOT NULL default '0',
  personalippanweklimit int NOT NULL default '0',
  daylimitflg char(1) NOT NULL default '0',
  pulloutdaylimitflg char(1) NOT NULL default '0',
  grouppulloutdaylimit int NOT NULL default '0',
  personalpulloutdaylimit int NOT NULL default '0',
  groupippandaylimit int NOT NULL default '0',
  personalippandaylimit int NOT NULL default '0',
  PRIMARY KEY (localgovcode,shisetsuclasscode)
);

--
-- Table structure for table m_shitsujyou
--

CREATE TABLE m_shitsujyou (
  localgovcode char(3) NOT NULL default '',
  shisetsucode char(3) NOT NULL default '',
  shitsujyocode char(2) NOT NULL default '',
  appdatefrom char(8) NOT NULL default '',
  shitsujyoname varchar(32) default NULL,
  shitsujyoskbcode int NOT NULL default '0',
  shitsujyoname2 varchar(64) default NULL,
  openflg char(1) NOT NULL default '0',
  openkbn varchar(32) NOT NULL default '1,1,1,1,1,1,1,1,1,1,1,1,0,0',
  yoyakudispkoma smallint NOT NULL default '1',
  pulloutdispkoma smallint NOT NULL default '1',
  teiin int NOT NULL default '0',
  teiin_min int NOT NULL default '0',
  ipnchgflg1 char(1) NOT NULL default '1',
  ipnchgflg2 char(1) NOT NULL default '1',
  genmen varchar(64) default NULL,
  genapplyflg varchar(8) NOT NULL default '1,2,3',
  extracharge varchar(64) default NULL,
  msg1 varchar(128) default NULL,
  msg2 varchar(128) default NULL,
  pulloutlimitflg char(1) NOT NULL default '0',
  pulloutweklimitflg char(1) NOT NULL default '0',
  pulloutdaylimitflg char(1) NOT NULL default '0',
  limitflg char(1) NOT NULL default '0',
  weklimitflg char(1) NOT NULL default '0',
  daylimitflg char(1) NOT NULL default '0',
  pulloutmonlimitkbn char(1) NOT NULL default '0',
  pulloutdaylimitdantai int NOT NULL default '0',
  pulloutdaylimitkojin int NOT NULL default '0',
  pulloutweklimitdantai int NOT NULL default '0',
  pulloutweklimitkojin int NOT NULL default '0',
  pulloutmonlimitdantai int NOT NULL default '0',
  pulloutmonlimitkojin int NOT NULL default '0',
  pulloutmon1limitdantai int NOT NULL default '0',
  pulloutmon1limitkojin int NOT NULL default '0',
  pulloutmon2limitdantai int NOT NULL default '0',
  pulloutmon2limitkojin int NOT NULL default '0',
  yoyakudaylimitdantai int NOT NULL default '0',
  yoyakudaylimitkojin int NOT NULL default '0',
  yoyakuweklimitdantai int NOT NULL default '0',
  yoyakuweklimitkojin int NOT NULL default '0',
  yoyakumonlimitdantai int NOT NULL default '0',
  yoyakumonlimitkojin int NOT NULL default '0',
  yoyakukojindanflg char(1) NOT NULL default '0',
  pulloutkojindanflg char(1) NOT NULL default '0',
  yoyakuareapriorityflg char(1) NOT NULL default '0',
  pulloutareapriorityflg char(1) NOT NULL default '0',
  haishidate varchar(8) default NULL,
  upddate char(8) default NULL,
  updtime char(6) default NULL,
  updid varchar(16) default NULL,
  shitsujyokbn char(1) NOT NULL default '1',
  dateselectflag char(1) NOT NULL default '0',
  PRIMARY KEY (localgovcode,shisetsucode,shitsujyocode,appdatefrom)
);

--
-- Table structure for table m_singenmen
--

CREATE TABLE m_singenmen (
  localgovcode char(3) NOT NULL default '',
  singencode char(2) NOT NULL default '',
  singenname varchar(30) default NULL,
  rate smallint NOT NULL default '0',
  bihinrate smallint NOT NULL default '0',
  upddate char(8) default NULL,
  updtime char(6) default NULL,
  updid varchar(16) default NULL,
  PRIMARY KEY (localgovcode,singencode)
);

--
-- Table structure for table m_staff
--

CREATE TABLE m_staff (
  localgovcode char(3) NOT NULL default '',
  bushocode varchar(16) default NULL,
  staffid varchar(16) NOT NULL default '',
  appdatefrom char(8) NOT NULL default '',
  staffnum varchar(16) default NULL,
  pwd varchar(16) default NULL,
  staffname varchar(40) default NULL,
  tourokukbn char(1) default NULL,
  kengencode1 char(2) default NULL,
  kengencode2 char(2) default NULL,
  kengencode3 char(2) default NULL,
  kengencode4 char(2) default NULL,
  kengencode5 char(2) default NULL,
  kengencode6 char(2) default NULL,
  haishidate varchar(8) default NULL,
  upddate char(8) default NULL,
  updtime char(6) default NULL,
  updid varchar(16) default NULL,
  PRIMARY KEY (localgovcode,staffid,appdatefrom)
);
CREATE INDEX m_satff_idx_1 ON m_staff (localgovcode,staffid);

--
-- Table structure for table m_staffshisetsu
--

CREATE TABLE m_staffshisetsu (
  localgovcode char(3) NOT NULL default '',
  staffid varchar(16) NOT NULL default '',
  shisetsucode char(3) NOT NULL default '',
  shitsujyocode char(2) NOT NULL default '',
  upddate char(8) default NULL,
  updtime char(6) default NULL,
  updid varchar(16) default NULL,
  PRIMARY KEY (localgovcode,staffid,shisetsucode,shitsujyocode)
);
CREATE INDEX m_satff_idx_1 ON m_staff (localgovcode,staffid);

--
-- Table structure for table m_stjfee
--

CREATE TABLE m_stjfee (
  localgovcode char(3) NOT NULL default '',
  shisetsucode char(3) NOT NULL default '',
  shitsujyocode char(2) NOT NULL default '',
  appdatefrom char(8) NOT NULL default '',
  combino int NOT NULL default '0',
  monthdayfrom char(4) NOT NULL default '',
  monthdayto char(4) default NULL,
  tourokuno char(1) NOT NULL default '',
  sunflg smallint NOT NULL default '0',
  monflg smallint NOT NULL default '0',
  tueflg smallint NOT NULL default '0',
  wedflg smallint NOT NULL default '0',
  thuflg smallint NOT NULL default '0',
  friflg smallint NOT NULL default '0',
  satflg smallint NOT NULL default '0',
  holiflg smallint NOT NULL default '0',
  feetourokukbn char(1) default NULL,
  koteifee int NOT NULL default '0',
  timefrom char(6) NOT NULL default '',
  timeto char(6) NOT NULL default '',
  feekbn01 char(2) default NULL,
  fee01 decimal(9,2) NOT NULL default '0.00',
  feekbn02 char(2) default NULL,
  fee02 decimal(9,2) NOT NULL default '0.00',
  feekbn03 char(2) default NULL,
  fee03 decimal(9,2) NOT NULL default '0.00',
  feekbn04 char(2) default NULL,
  fee04 decimal(9,2) NOT NULL default '0.00',
  feekbn05 char(2) default NULL,
  fee05 decimal(9,2) NOT NULL default '0.00',
  feekbn06 char(2) default NULL,
  fee06 decimal(9,2) NOT NULL default '0.00',
  feekbn07 char(2) default NULL,
  fee07 decimal(9,2) NOT NULL default '0.00',
  feekbn08 char(2) default NULL,
  fee08 decimal(9,2) NOT NULL default '0.00',
  feekbn09 char(2) default NULL,
  fee09 decimal(9,2) NOT NULL default '0.00',
  feekbn10 char(2) default NULL,
  fee10 decimal(9,2) NOT NULL default '0.00',
  feeunitflg char(1) NOT NULL default '0',
  feeunit smallint NOT NULL default '1',
  komaunitflg char(1) NOT NULL default '0',
  komaunit smallint NOT NULL default '1',
  haishidate varchar(8) default NULL,
  upddate char(8) default NULL,
  updtime char(6) default NULL,
  updid varchar(16) default NULL,
  minimumusetimeflg char(1) NOT NULL default '0',
  minimumusefeeflg char(1) NOT NULL default '0',
  minimumusetime smallint NOT NULL default '0',
  minimumusefee decimal(9,2) NOT NULL default '0.00',
  PRIMARY KEY (localgovcode,shisetsucode,shitsujyocode,appdatefrom,combino,monthdayfrom,tourokuno,timefrom,timeto)
);

--
-- Table structure for table m_stjpurpose
--

CREATE TABLE m_stjpurpose (
  localgovcode char(3) NOT NULL default '',
  shisetsucode char(3) NOT NULL default '',
  shitsujyocode char(2) NOT NULL default '',
  combino int NOT NULL default '0',
  mokutekicode char(2) NOT NULL default '',
  upddate char(8) default NULL,
  updtime char(6) default NULL,
  updid varchar(16) default NULL,
  PRIMARY KEY (localgovcode,shisetsucode,shitsujyocode,combino,mokutekicode)
);
CREATE INDEX m_stjpurpose_idx_1 ON m_stjpurpose (localgovcode,mokutekicode);

--
-- Table structure for table m_stjtimetable
--

CREATE TABLE m_stjtimetable (
  localgovcode char(3) NOT NULL default '',
  shisetsucode char(3) NOT NULL default '',
  shitsujyocode char(2) NOT NULL default '',
  appdatefrom char(8) NOT NULL default '',
  monthdayfrom char(4) NOT NULL default '',
  monthdayto char(4) default NULL,
  kaijoutime char(4) default NULL,
  heijoutime char(4) default NULL,
  komatanitime smallint NOT NULL default '0',
  komatanitimekbn char(1) default NULL,
  komaclass char(1) default NULL,
  komakbn char(2) NOT NULL default '',
  komatimefrom char(6) default NULL,
  komatimeto char(6) default NULL,
  komaname varchar(20) default NULL,
  haishidate varchar(8) default NULL,
  upddate char(8) default NULL,
  updtime char(6) default NULL,
  updid varchar(16) default NULL,
  PRIMARY KEY (localgovcode,shisetsucode,shitsujyocode,appdatefrom,monthdayfrom,komakbn)
);
CREATE INDEX m_stjtimetable_idx_1 ON m_stjtimetable (localgovcode,shisetsucode,shitsujyocode,appdatefrom,monthdayfrom,monthdayto,komakbn);

--
-- Table structure for table m_stjuserrestime
--

CREATE TABLE m_stjuserrestime (
  localgovcode char(3) NOT NULL default '',
  shisetsucode char(3) NOT NULL default '',
  shitsujyocode char(2) NOT NULL default '',
  monthdayfrom char(4) NOT NULL default '',
  monthdayto char(4) NOT NULL default '',
  appdatefrom char(8) NOT NULL default '',
  tourokuno char(1) default NULL,
  haishidate varchar(8) default NULL,
  userrestime0from char(4) default NULL,
  userrestime0to char(4) default NULL,
  userrestime1from char(4) default NULL,
  userrestime1to char(4) default NULL,
  userrestime2from char(4) default NULL,
  userrestime2to char(4) default NULL,
  userrestime3from char(4) default NULL,
  userrestime3to char(4) default NULL,
  userrestime4from char(4) default NULL,
  userrestime4to char(4) default NULL,
  userrestime5from char(4) default NULL,
  userrestime5to char(4) default NULL,
  userrestime6from char(4) default NULL,
  userrestime6to char(4) default NULL,
  userrestime7from char(4) default NULL,
  userrestime7to char(4) default NULL,
  useholidayflg char(1) default NULL,
  upddate char(8) default NULL,
  updtime char(6) default NULL,
  updid varchar(16) default NULL,
  PRIMARY KEY (localgovcode,shisetsucode,shitsujyocode,appdatefrom,monthdayfrom,monthdayto)
);

--
-- Table structure for table m_systemparameter
--

CREATE TABLE m_systemparameter (
  localgovcode char(3) NOT NULL default '',
  localgovname varchar(24) default NULL,
  useridtype char(1) NOT NULL default '0',
  useridlng smallint NOT NULL default '0',
  useridlngmin smallint NOT NULL default '0',
  userid_size smallint NOT NULL default '0',
  pwdtype char(1) NOT NULL default '0',
  pwdlng smallint NOT NULL default '0',
  pwdlngmin smallint NOT NULL default '0',
  pwd_size smallint NOT NULL default '0',
  autouserid varchar(9) default NULL,
  autouseridlng smallint NOT NULL default '0',
  autopwd varchar(16) default NULL,
  userpassautoflg char(1) NOT NULL default '0',
  homepageurl varchar(160) default NULL,
  topmenuurl varchar(160) default NULL,
  mobilemenuurl varchar(160) default NULL,
  mailsendflg char(1) default NULL,
  mailfromaddr varchar(128) default NULL,
  mailfromname varchar(64) default NULL,
  mailbccaddr text,
  mailhost varchar(64) default NULL,
  mailhostport smallint NOT NULL default '0',
  mailhostuserid varchar(64) default NULL,
  mailhostuserpass varchar(64) default NULL,
  amfrom char(4) default NULL,
  amto char(4) default NULL,
  pmfrom char(4) default NULL,
  pmto char(4) default NULL,
  ntfrom char(4) default NULL,
  ntto char(4) default NULL,
  upddate char(8) default NULL,
  updtime char(6) default NULL,
  updid varchar(16) default NULL,
  loginkbn char(1) NOT NULL default '0',
  logintimefrom char(4) NOT NULL default '0000',
  logintimeto char(4) NOT NULL default '2359',
  lockoutflg char(1) NOT NULL default '0',
  lockout_count smallint NOT NULL default '5',
  reentry_interval smallint NOT NULL default '30',
  sitecloseflg char(1) NOT NULL default '0',
  siteclosemessage text,
  siteclosefrom int NOT NULL default '0',
  sitecloseto int NOT NULL default '0',
  mayorname varchar(16) NOT NULL default '',
  shisetsuclassscreenflg char(1) NOT NULL default '0',
  userlimitdispflg char(1) NOT NULL default '0',
  accesscontrolflg char(1) NOT NULL default '0',
  accesscontrolactionflg char(1) NOT NULL default '0',
  shisetsurestrictionflg char(1) NOT NULL default '0',
  PRIMARY KEY (localgovcode)
);

--
-- Table structure for table m_tax
--

CREATE TABLE m_tax (
  localgovcode char(3) NOT NULL default '',
  appdatefrom char(8) NOT NULL default '',
  limitday char(8) NOT NULL default '',
  taxrate smallint NOT NULL default '0',
  taxcutkbn char(1) default NULL,
  upddate char(8) default NULL,
  updtime char(6) default NULL,
  updid varchar(16) default NULL,
  PRIMARY KEY (localgovcode,appdatefrom)
);

--
-- Table structure for table m_unavailableday
--

CREATE TABLE m_unavailableday (
  localgovcode char(3) NOT NULL default '',
  shisetsucode char(3) NOT NULL default '',
  shitsujyocode char(2) NOT NULL default '',
  appdatefrom char(8) NOT NULL,
  closedday char(4) NOT NULL,
  yoyakukbn char(2) NOT NULL,
  PRIMARY KEY (localgovcode,shisetsucode,shitsujyocode,appdatefrom,closedday)
);

--
-- Table structure for table m_user
--

CREATE TABLE m_user (
  localgovcode char(3) NOT NULL default '',
  userid varchar(128) NOT NULL default '',
  pid varchar(16) default NULL,
  pwd varchar(16) default NULL,
  pwd2 varchar(128) default NULL,
  temporaryid varchar(16) default NULL,
  yoyakukyokaflg char(1) default NULL,
  yoyakukyokaflgweb char(1) default NULL,
  nojiyu varchar(200) default NULL,
  loginflg char(1) default NULL,
  namesei varchar(128) default '',
  nameseikana varchar(128) default '',
  userareakbn char(2) default NULL,
  kojindankbn char(1) default NULL,
  hyoujimei varchar(8) default NULL,
  usekbn char(2) default NULL,
  gakuwariappflg char(1) default NULL,
  gakuwariapplimit varchar(8) default NULL,
  headnamesei varchar(32) default '',
  headnameseikana varchar(64) default '',
  contactname varchar(32) default NULL,
  contactnamekana varchar(64) default NULL,
  seibetsukbn char(1) default NULL,
  postno1 varchar(3) default '',
  postno2 varchar(4) default '',
  adr1 varchar(128) default '',
  adr2 varchar(128) default '',
  telno11 varchar(5) default '',
  telno12 varchar(4) default '',
  telno13 varchar(4) default '',
  telno21 varchar(5) default '',
  telno22 varchar(4) default '',
  telno23 varchar(4) default '',
  telno31 varchar(5) default '',
  telno32 varchar(4) default '',
  telno33 varchar(4) default '',
  faxno1 varchar(5) default NULL,
  faxno2 varchar(4) default NULL,
  faxno3 varchar(4) default NULL,
  mailadr text,
  mailsendflg char(1) default '0',
  nengoukbn char(1) default NULL,
  bdayyear varchar(2) default NULL,
  bdaymonth varchar(2) default NULL,
  bdayday varchar(2) default NULL,
  firstapplydate char(8) default NULL,
  firstentrydate char(8) default NULL,
  newapplydate char(8) default NULL,
  bankcode varchar(8) NOT NULL default '0000',
  bankbranchcode varchar(8) NOT NULL default '000',
  bankaccounttype char(1) NOT NULL default '1',
  bankaccountnum varchar(16) default NULL,
  bankaccountname varchar(128) default NULL,
  bankaccountkana varchar(128) default NULL,
  bankaccountsumflg char(1) NOT NULL default '0',
  registfee decimal(9,2) NOT NULL default '0.00',
  registfeestatus char(1) NOT NULL default '0',
  registfeedate varchar(8) default NULL,
  paystatus char(1) NOT NULL default '0',
  payconfdate varchar(8) default NULL,
  loginerr_count smallint NOT NULL default '0',
  escapecnt smallint default '0',
  shisetsu text,
  mainpurpose varchar(80) default NULL,
  purpose text,
  koukaikbn char(1) default NULL,
  userjyoutaikbn char(1) default NULL,
  stoperasedate varchar(8) default NULL,
  stopenddate varchar(8) default NULL,
  stoperasejiyu varchar(80) default NULL,
  katudogaiyou varchar(200) default NULL,
  kouseijinnin smallint default '0',
  kaihijyouhou varchar(40) default NULL,
  katudodate varchar(100) default NULL,
  lecturerjyouhou varchar(40) default NULL,
  thanksjyouhou varchar(40) default NULL,
  bikou text,
  userlimit varchar(8) default NULL,
  cardid text,
  tourokubushocode varchar(16) default NULL,
  lastlogin int NOT NULL default '0',
  notice text,
  notice_published int NOT NULL default '0',
  notice_expired int NOT NULL default '0',
  notice_flg char(1) NOT NULL default '0',
  upddate char(8) default NULL,
  updtime char(6) default NULL,
  updid varchar(16) default NULL,
  kouseijinmeisai1 smallint default '0',
  kouseijinmeisai2 smallint default '0',
  kouseijinmeisai3 smallint default '0',
  kouseijinmeisai4 smallint default '0',
  kouseijinmeisai5 smallint default '0',
  dantaikbn char(2) default NULL,
  optionkbn1 char(2) default NULL,
  optionkbn2 char(2) default NULL,
  optionkbn3 char(2) default NULL,
  carduse char(1) default '0',
  userkubun char(1) default '',
  honninkakuninkubun char(1) default '',
  PRIMARY KEY (localgovcode,userid)
);

--
-- Table structure for table m_usrgenmen
--

CREATE TABLE m_usrgenmen (
  localgovcode char(3) NOT NULL default '',
  userid varchar(128) NOT NULL default '',
  koteigencode char(2) NOT NULL default '',
  appday char(8) default NULL,
  limitday char(8) default NULL,
  keizokuflg char(1) NOT NULL default '0',
  upddate char(8) default NULL,
  updtime char(6) default NULL,
  updid varchar(16) default NULL,
  PRIMARY KEY (localgovcode,userid,koteigencode)
);

--
-- Table structure for table m_yoyakuscheduleptn
--

CREATE TABLE m_yoyakuscheduleptn (
  localgovcode char(3) NOT NULL default '',
  shisetsucode char(3) NOT NULL default '',
  shitsujyocode char(2) NOT NULL default '',
  ukekaisitanikbn char(1) default NULL,
  pulloutflg char(1) NOT NULL default '0',
  fixflg char(1) NOT NULL default '0',
  nofixcancelflg char(1) default NULL,
  pulloutukekbn char(1) default NULL,
  pulloutukekikan varchar(32) default NULL,
  webuketimekbn char(1) NOT NULL default '0',
  webuketimefrom char(4) default NULL,
  webuketimeto char(4) default NULL,
  feepaylimtkbn char(1) NOT NULL default '0',
  feepaylimtday smallint NOT NULL default '0',
  pulloutfeepaylimtkbn char(1) NOT NULL default '0',
  pulloutfeepaylimtday smallint NOT NULL default '0',
  feepaykbn char(1) default NULL,
  feelimitautocanflg char(1) NOT NULL default '0',
  pulloutresfrommon smallint NOT NULL default '1',
  pulloutresfromday smallint NOT NULL default '1',
  pulloutresfromtime char(4) NOT NULL default '0000',
  pulloutreslimtday smallint NOT NULL default '9',
  pulloutreslimittime char(4) NOT NULL default '2359',
  pulloutday smallint NOT NULL default '10',
  pullouttime char(4) NOT NULL default '0800',
  pulloutkekkaday smallint NOT NULL default '1',
  pulloutkekkatime char(4) NOT NULL default '0800',
  pulloutkomalimitflg char(1) NOT NULL default '0',
  pulloutfixlimitkbn char(1) NOT NULL default '1',
  pulloutfixlimitflg char(1) NOT NULL default '0',
  pulloutfixlimitkojin smallint NOT NULL default '0',
  pulloutfixlimitdantai smallint NOT NULL default '0',
  pulloutfixlimtday smallint NOT NULL default '7',
  pulloutfixlimittime char(4) NOT NULL default '2359',
  pulloutopnlimtday smallint NOT NULL default '0',
  pulloutopnlimittime char(4) default NULL,
  ippanresfrommon smallint NOT NULL default '1',
  ippanresfromday smallint NOT NULL default '1',
  ippanresfromtime char(4) NOT NULL default '0000',
  ippanrestomon smallint NOT NULL default '0',
  ippanrestoday smallint NOT NULL default '1',
  ippanrestotime char(4) NOT NULL default '2359',
  ippanresstartmon smallint NOT NULL default '0',
  ippanresstartday smallint NOT NULL default '1',
  ippanresstartflg smallint NOT NULL default '3',
  ippanreslimitmon smallint NOT NULL default '0',
  ippanreslimitday smallint NOT NULL default '0',
  ippanreslimitflg smallint NOT NULL default '2',
  ippanshigairesfrommon smallint NOT NULL default '1',
  ippanshigairesfromday smallint NOT NULL default '1',
  ippanshigairesfromtime char(4) NOT NULL default '0000',
  ippanshigairestomon smallint NOT NULL default '0',
  ippanshigairestoday smallint NOT NULL default '1',
  ippanshigairestotime char(4) NOT NULL default '2359',
  ippanshigairesstartmon smallint NOT NULL default '0',
  ippanshigairesstartday smallint NOT NULL default '1',
  ippanshigairesstartflg smallint NOT NULL default '3',
  ippanshigaireslimitmon smallint NOT NULL default '0',
  ippanshigaireslimitday smallint NOT NULL default '0',
  ippanshigaireslimitflg smallint NOT NULL default '2',
  ippancanclosemon smallint NOT NULL default '1',
  ippancancloseday smallint NOT NULL default '10',
  ippancanlimitday smallint NOT NULL default '1',
  ippancanlimittime char(4) NOT NULL default '2359',
  ippancanlimitflg char(1) NOT NULL default '1',
  ippancanfeeday smallint NOT NULL default '0',
  ippancanfeetime char(4) default NULL,
  upddate char(8) default NULL,
  updtime char(6) default NULL,
  updid varchar(16) default NULL,
  kariyoyakuflg char(1) NOT NULL default '2',
  ippanyoyakukbn char(1) NOT NULL default '2',
  karikakuteilimitkbn char(1) default NULL,
  ippankarilimtday smallint NOT NULL default '0',
  ippankarilimittime char(4) default NULL,
  ippanopnlimtday smallint NOT NULL default '0',
  ippanopnlimittime char(4) default NULL,
  PRIMARY KEY (localgovcode,shisetsucode,shitsujyocode)
);

--
-- Table structure for table m_zip
--

CREATE TABLE m_zip (
  code char(7) NOT NULL,
  address text NOT NULL
);
CREATE INDEX m_zip_idx_1 ON m_zip (code);

--
-- Table structure for table t_bihinyoyaku
--

CREATE TABLE t_bihinyoyaku (
  localgovcode char(3) NOT NULL default '',
  yoyakunum varchar(10) NOT NULL,
  userid varchar(128) NOT NULL,
  yoyakudate int NOT NULL default '0',
  usedatefrom int NOT NULL default '0',
  usedateto int NOT NULL default '0',
  rentdate int NOT NULL default '0',
  returndate int NOT NULL default '0',
  basefee decimal(9,2) NOT NULL default '0.00',
  tax decimal(9,2) NOT NULL default '0.00',
  billingfee decimal(9,2) NOT NULL default '0.00',
  receiptfee decimal(9,2) NOT NULL default '0.00',
  paylimitdate int NOT NULL default '0',
  receiptdate int NOT NULL default '0',
  paykbn smallint NOT NULL default '0',
  yoyakustatus smallint NOT NULL default '0',
  note text,
  shisetsuyoyakunum varchar(10) default NULL,
  upddate int NOT NULL default '0',
  updid varchar(16) default NULL,
  PRIMARY KEY (localgovcode,yoyakunum)
);

--
-- Table structure for table t_bihinyoyakuuchiwake
--

CREATE TABLE t_bihinyoyakuuchiwake (
  localgovcode char(3) NOT NULL default '',
  yoyakunum varchar(10) NOT NULL,
  bihincode int NOT NULL default '0',
  amount int NOT NULL default '0',
  basefee decimal(9,2) NOT NULL default '0.00',
  upddate int NOT NULL default '0',
  updid varchar(16) default NULL,
  PRIMARY KEY (localgovcode,yoyakunum,bihincode)
);

--
-- Table structure for table t_monthpulloutdate
--

CREATE TABLE t_monthpulloutdate (
  localgovcode char(3) NOT NULL default '',
  shisetsucode char(3) NOT NULL default '',
  shitsujyocode char(2) NOT NULL default '',
  month smallint NOT NULL,
  pulloutfromday char(4) NOT NULL,
  pulloutlimitday char(4) NOT NULL,
  pulloutday char(4) NOT NULL,
  pulloutopenfromday char(4) NOT NULL,
  pulloutfixlimitday char(4) NOT NULL,
  pulloutfromtime char(4) NOT NULL,
  pulloutlimittime char(4) NOT NULL,
  pullouttime char(4) NOT NULL,
  pulloutopenfromtime char(4) NOT NULL,
  pulloutfixlimittime char(4) NOT NULL,
  PRIMARY KEY (localgovcode,shisetsucode,shitsujyocode,month)
);

--
-- Table structure for table t_potalmemo
--

CREATE TABLE t_potalmemo (
  localgovcode char(3) NOT NULL default '',
  shisetsucode char(3) NOT NULL default '',
  tourokudate char(8) NOT NULL default '',
  tourokutime char(6) default NULL,
  seqno char(2) NOT NULL default '',
  upkikanfrom char(8) default NULL,
  upkikanto char(8) default NULL,
  title varchar(100) default NULL,
  memo text,
  staffid varchar(16) default NULL,
  url varchar(160) default NULL,
  prioritykbn smallint NOT NULL default '0',
  disptermflg char(1) NOT NULL default '0',
  haishidate varchar(8) default NULL,
  upddate char(8) default NULL,
  updtime char(6) default NULL,
  updid varchar(16) default NULL,
  PRIMARY KEY (localgovcode,shisetsucode,tourokudate,seqno)
);

--
-- Table structure for table t_preset
--

CREATE TABLE t_preset (
  userid varchar(128) NOT NULL,
  localgovcode char(3) NOT NULL default '',
  shisetsucode char(3) NOT NULL default '',
  shitsujyocode char(2) NOT NULL default '',
  appdatefrom char(8) NOT NULL,
  mencode char(2) NOT NULL,
  combino int NOT NULL default '0',
  tourokuno int NOT NULL,
  PRIMARY KEY (userid,localgovcode,shisetsucode,shitsujyocode,appdatefrom,mencode,tourokuno)
);

--
-- Table structure for table t_pulloutyoyaku
--

CREATE TABLE t_pulloutyoyaku (
  localgovcode char(3) NOT NULL default '',
  shisetsucode char(3) NOT NULL default '',
  shitsujyocode char(2) NOT NULL default '',
  mencode char(2) NOT NULL default '',
  combino int NOT NULL default '0',
  usedate char(8) NOT NULL default '',
  usetimefrom char(6) NOT NULL default '',
  userid varchar(128) NOT NULL default '',
  usetimeto char(6) NOT NULL default '',
  pulloutukedate char(8) NOT NULL default '',
  pulloutuketime char(6) NOT NULL default '',
  pulloutyoyakunum varchar(10) NOT NULL default '',
  komasu smallint NOT NULL default '0',
  baseshisetsufee decimal(9,2) NOT NULL default '0.00',
  shisetsufee decimal(9,2) NOT NULL default '0.00',
  shisetsutax decimal(9,2) NOT NULL default '0.00',
  pulloutjisshidate char(8) default NULL,
  pulloutjisshitime char(4) default NULL,
  pulloutjoukyoukbn char(1) default NULL,
  pulloutfixflg char(1) default NULL,
  sendmaildate varchar(8) default NULL,
  bikou text,
  mokutekicode char(2) default NULL,
  usekbn char(2) default NULL,
  daikouid varchar(16) default NULL,
  hitfixappdate varchar(8) default NULL,
  hitfixapptime varchar(6) default NULL,
  upddate char(8) default NULL,
  updtime char(6) default NULL,
  updid varchar(16) default NULL,
  PRIMARY KEY (localgovcode,shisetsucode,shitsujyocode,mencode,usedate,usetimefrom,userid,usetimeto,pulloutukedate,pulloutuketime)
);
CREATE INDEX t_pulloutyoyaku_idx_1 ON t_pulloutyoyaku (localgovcode,pulloutyoyakunum);
CREATE INDEX t_pulloutyoyaku_idx_2 ON t_pulloutyoyaku (localgovcode,shisetsucode,shitsujyocode);
CREATE INDEX t_pulloutyoyaku_idx_3 ON t_pulloutyoyaku (localgovcode,usedate);
CREATE INDEX t_pulloutyoyaku_idx_4 ON t_pulloutyoyaku (localgovcode,userid);

--
-- Table structure for table t_staffbbs
--

CREATE TABLE t_staffbbs (
  seqno int NOT NULL default '0',
  localgovcode char(3) NOT NULL default '',
  shisetsucode char(3) NOT NULL default '',
  title varchar(128) default NULL,
  memo text,
  staffid varchar(16) NOT NULL default '',
  deleteflg char(1) NOT NULL default '0',
  created int NOT NULL default '0',
  published int NOT NULL default '0',
  expired int NOT NULL default '0',
  updated int NOT NULL default '0',
  updid varchar(16) default NULL,
  PRIMARY KEY (seqno)
);

--
-- Table structure for table t_unregister
--

CREATE TABLE t_unregister (
  yoyakunum varchar(10) NOT NULL default '',
  username varchar(128) default NULL,
  address varchar(128) default NULL,
  telno varchar(16) default NULL,
  contactno varchar(16) default NULL,
  upddate char(8) default NULL,
  updtime char(6) default NULL,
  updid varchar(16) default NULL,
  PRIMARY KEY (yoyakunum)
);

--
-- Table structure for table t_yoyaku
--

CREATE TABLE t_yoyaku (
  localgovcode char(3) NOT NULL default '',
  shisetsucode char(3) NOT NULL default '',
  shitsujyocode char(2) NOT NULL default '',
  mencode char(2) NOT NULL default '',
  combino int NOT NULL default '0',
  usedatefrom char(8) NOT NULL default '',
  usetimefrom char(6) NOT NULL default '',
  usetimeto char(6) default NULL,
  yoyakunum varchar(10) NOT NULL default '',
  userid varchar(128) NOT NULL default '',
  komasu smallint NOT NULL default '0',
  baseshisetsufee decimal(9,2) NOT NULL default '0.00',
  shisetsufee decimal(9,2) NOT NULL default '0.00',
  shisetsutax decimal(9,2) NOT NULL default '0.00',
  shisetsupaylimitdate varchar(8) default NULL,
  utensinflg char(1) default NULL,
  honyoyakukbn char(2) default NULL,
  karishinsaid varchar(16) default NULL,
  shinsakbn char(1) default NULL,
  shinsareason varchar(100) default NULL,
  shinsadate varchar(8) default NULL,
  useukeflg char(1) default NULL,
  escapeflg char(1) default NULL,
  bikou text,
  yoyakukbn char(2) default NULL,
  yoyakuname varchar(128) default NULL,
  mokutekicode char(2) default NULL,
  usekbn char(2) default NULL,
  daikouid varchar(16) default NULL,
  appdate char(8) default NULL,
  apptime char(6) default NULL,
  canceljiyucode varchar(3) default NULL,
  cancelstaffid varchar(16) default NULL,
  upddate char(8) default NULL,
  updtime char(6) default NULL,
  updid varchar(16) default NULL,
  PRIMARY KEY (localgovcode,shisetsucode,shitsujyocode,mencode,usedatefrom,usetimefrom,userid)
);
CREATE INDEX t_yoyaku_idx_1 ON t_yoyaku (localgovcode,yoyakunum);
CREATE INDEX t_yoyaku_idx_2 ON t_yoyaku (localgovcode,shisetsucode,shitsujyocode);
CREATE INDEX t_yoyaku_idx_3 ON t_yoyaku (localgovcode,userid);
CREATE INDEX t_yoyaku_idx_4 ON t_yoyaku (localgovcode,shisetsucode,honyoyakukbn);
CREATE INDEX t_yoyaku_idx_5 ON t_yoyaku (localgovcode,shisetsucode,shitsujyocode,mencode,usedatefrom);

--
-- Table structure for table `t_yoyaku_option_fee`
--

CREATE TABLE `t_yoyaku_fee_option` (
  localgovcode char(3) NOT NULL default '',
  shisetsucode char(3) NOT NULL default '',
  shitsujyocode char(2) NOT NULL default '',
  yoyakunum varchar(10) NOT NULL default '',
  usedate char(8) NOT NULL default '',
  usetimefrom` char(6) NOT NULL default '',
  usetimeto char(6) NOT NULL default '',
  amount int NOT NULL default '0',
  basefee decimal(9,2) NOT NULL default '0.00',
  tax decimal(9,2) NOT NULL default '0.00',
  billingfee decimal(9,2) NOT NULL default '0.00',
  feekbn char(2) NOT NULL default '',
  genmen varchar(4) NOT NULL default '',
  surcharge varchar(4) NOT NULL default '',
  upddate char(8) default NULL,
  updtime char(6) default NULL,
  updid varchar(16) default NULL,
  PRIMARY KEY (localgovcode,shisetsucode,shitsujyocode,yoyakunum)
);
CREATE INDEX t_yoyaku_fee_option_idx_1 ON t_yoyaku (localgovcode,yoyakunum);

--
-- Table structure for table t_yoyakufeekannoustatus
--

CREATE TABLE t_yoyakufeekannoustatus (
  localgovcode char(3) NOT NULL default '',
  yoyakunum varchar(10) NOT NULL default '',
  updid varchar(16) default NULL,
  upddatetime timestamp default NULL,
  PRIMARY KEY (localgovcode,yoyakunum)
);

--
-- Table structure for table t_yoyakufeeshinsei
--

CREATE TABLE t_yoyakufeeshinsei (
  localgovcode char(3) NOT NULL default '',
  shisetsucode char(3) NOT NULL default '',
  shitsujyocode char(2) NOT NULL default '',
  combino int NOT NULL default '0',
  usedate char(8) NOT NULL default '',
  usetimefrom char(6) NOT NULL default '',
  usetimeto char(6) default NULL,
  feesinkbn char(2) default NULL,
  yoyakunum varchar(10) NOT NULL default '',
  basefee decimal(9,2) NOT NULL default '0.00',
  shisetsufee decimal(9,2) NOT NULL default '0.00',
  tax decimal(9,2) NOT NULL default '0.00',
  suuryo decimal(9,2) NOT NULL default '0.00',
  suuryotani varchar(4) NOT NULL default '',
  surcharge varchar(4) NOT NULL default '',
  paykbn smallint NOT NULL default '0',
  bihinyoyakunum varchar(10) default NULL,
  bihinfee decimal(9,2) NOT NULL default '0.00',
  optionfee1 decimal(9,2) NOT NULL default '0.00',
  optionfee2 decimal(9,2) NOT NULL default '0.00',
  optionfee3 decimal(9,2) NOT NULL default '0.00',
  optionfee4 decimal(9,2) NOT NULL default '0.00',
  optionfee5 decimal(9,2) NOT NULL default '0.00',
  chousei_reason varchar(128) default NULL,
  useninzu smallint NOT NULL default '0',
  ninzu1 smallint NOT NULL default '0',
  ninzu2 smallint NOT NULL default '0',
  ninzu3 smallint NOT NULL default '0',
  ninzu4 smallint NOT NULL default '0',
  ninzu5 smallint NOT NULL default '0',
  ninzu6 smallint NOT NULL default '0',
  ninzu7 smallint NOT NULL default '0',
  ninzu8 smallint NOT NULL default '0',
  ninzu9 smallint NOT NULL default '0',
  ninzu10 smallint NOT NULL default '0',
  ninzu11 smallint NOT NULL default '0',
  ninzu12 smallint NOT NULL default '0',
  ninzu13 smallint NOT NULL default '0',
  ninzu14 smallint NOT NULL default '0',
  ninzu15 smallint NOT NULL default '0',
  ninzu16 smallint NOT NULL default '0',
  upddate char(8) default NULL,
  updtime char(6) default NULL,
  updid varchar(16) default NULL,
  PRIMARY KEY (localgovcode,yoyakunum)
);
CREATE INDEX t_yoyakufeeshinsei_idx_1 ON t_yoyakufeeshinsei (localgovcode,shisetsucode,usedate);
CREATE INDEX t_yoyakufeeshinsei_idx_2 ON t_yoyakufeeshinsei (localgovcode,shisetsucode,shitsujyocode,usedate);

--
-- Table structure for table t_yoyakufeeuketsuke
--

CREATE TABLE t_yoyakufeeuketsuke (
  localgovcode char(3) NOT NULL default '',
  shisetsucode char(3) NOT NULL default '',
  receptdate char(8) default NULL,
  uketime char(6) default NULL,
  yoyakunum varchar(10) NOT NULL default '',
  receptnum char(2) NOT NULL default '',
  userid varchar(128) default NULL,
  shisetsufee decimal(9,2) NOT NULL default '0.00',
  bihinfee decimal(9,2) NOT NULL default '0.00',
  cancelfee decimal(9,2) NOT NULL default '0.00',
  tax decimal(9,2) NOT NULL default '0.00',
  cash decimal(9,2) NOT NULL default '0.00',
  chg decimal(9,2) NOT NULL default '0.00',
  ticket decimal(9,2) NOT NULL default '0.00',
  kouzafurikomi decimal(9,2) NOT NULL default '0.00',
  others decimal(9,2) NOT NULL default '0.00',
  jyutou decimal(9,2) NOT NULL default '0.00',
  kinshucode char(2) default NULL,
  paystatus char(1) NOT NULL default '0',
  receptid varchar(16) default NULL,
  receptplace char(3) default NULL,
  upddate char(8) default NULL,
  updtime char(6) default NULL,
  updid varchar(16) default NULL,
  PRIMARY KEY (localgovcode,shisetsucode,yoyakunum,receptnum)
);
CREATE INDEX t_yoyakufeeuketsuke_idx_1 ON t_yoyakufeeuketsuke (localgovcode,yoyakunum);
CREATE INDEX t_yoyakufeeuketsuke_idx_2 ON t_yoyakufeeuketsuke (localgovcode,receptdate,yoyakunum,receptnum,userid);
CREATE INDEX t_yoyakufeeuketsuke_idx_3 ON t_yoyakufeeuketsuke (localgovcode,userid);

--
-- Table structure for table t_yoyakukanpujyutou
--

CREATE TABLE t_yoyakukanpujyutou (
  localgovcode char(3) NOT NULL default '',
  yoyakunum varchar(10) NOT NULL default '',
  uketsukeno int NOT NULL default '1',
  status smallint NOT NULL default '0',
  fee decimal(9,2) NOT NULL default '0.00',
  kinshucode char(2) default NULL,
  cancelflg smallint NOT NULL default '0',
  kanpujyutoudate date default NULL,
  destyoyakunum varchar(10) default NULL,
  bikou text,
  receiptdatetime timestamp default NULL,
  receiptstaffid varchar(16) default NULL,
  canceldatetime timestamp default NULL,
  cancelstaffid varchar(16) default NULL,
  PRIMARY KEY (localgovcode,yoyakunum,uketsukeno)
);
CREATE INDEX t_yoyakukanpujyutou_idx_1 ON t_yoyakukanpujyutou (localgovcode,yoyakunum);

--
-- Table structure for table t_yoyakukanri
--

CREATE TABLE t_yoyakukanri (
  localgovcode char(3) NOT NULL default '',
  usedate char(8) NOT NULL default '',
  shisetsucode char(3) NOT NULL default '',
  shitsujyocode char(2) NOT NULL default '',
  mencode char(2) NOT NULL default '',
  usetimefrom01 char(6) default NULL,
  usetimeto01 char(6) default NULL,
  komaflg01 char(1) default NULL,
  usetimefrom02 char(6) default NULL,
  usetimeto02 char(6) default NULL,
  komaflg02 char(1) default NULL,
  usetimefrom03 char(6) default NULL,
  usetimeto03 char(6) default NULL,
  komaflg03 char(1) default NULL,
  usetimefrom04 char(6) default NULL,
  usetimeto04 char(6) default NULL,
  komaflg04 char(1) default NULL,
  usetimefrom05 char(6) default NULL,
  usetimeto05 char(6) default NULL,
  komaflg05 char(1) default NULL,
  usetimefrom06 char(6) default NULL,
  usetimeto06 char(6) default NULL,
  komaflg06 char(1) default NULL,
  usetimefrom07 char(6) default NULL,
  usetimeto07 char(6) default NULL,
  komaflg07 char(1) default NULL,
  usetimefrom08 char(6) default NULL,
  usetimeto08 char(6) default NULL,
  komaflg08 char(1) default NULL,
  usetimefrom09 char(6) default NULL,
  usetimeto09 char(6) default NULL,
  komaflg09 char(1) default NULL,
  usetimefrom10 char(6) default NULL,
  usetimeto10 char(6) default NULL,
  komaflg10 char(1) default NULL,
  usetimefrom11 char(6) default NULL,
  usetimeto11 char(6) default NULL,
  komaflg11 char(1) default NULL,
  usetimefrom12 char(6) default NULL,
  usetimeto12 char(6) default NULL,
  komaflg12 char(1) default NULL,
  usetimefrom13 char(6) default NULL,
  usetimeto13 char(6) default NULL,
  komaflg13 char(1) default NULL,
  usetimefrom14 char(6) default NULL,
  usetimeto14 char(6) default NULL,
  komaflg14 char(1) default NULL,
  usetimefrom15 char(6) default NULL,
  usetimeto15 char(6) default NULL,
  komaflg15 char(1) default NULL,
  usetimefrom16 char(6) default NULL,
  usetimeto16 char(6) default NULL,
  komaflg16 char(1) default NULL,
  usetimefrom17 char(6) default NULL,
  usetimeto17 char(6) default NULL,
  komaflg17 char(1) default NULL,
  usetimefrom18 char(6) default NULL,
  usetimeto18 char(6) default NULL,
  komaflg18 char(1) default NULL,
  usetimefrom19 char(6) default NULL,
  usetimeto19 char(6) default NULL,
  komaflg19 char(1) default NULL,
  usetimefrom20 char(6) default NULL,
  usetimeto20 char(6) default NULL,
  komaflg20 char(1) default NULL,
  usetimefrom21 char(6) default NULL,
  usetimeto21 char(6) default NULL,
  komaflg21 char(1) default NULL,
  usetimefrom22 char(6) default NULL,
  usetimeto22 char(6) default NULL,
  komaflg22 char(1) default NULL,
  usetimefrom23 char(6) default NULL,
  usetimeto23 char(6) default NULL,
  komaflg23 char(1) default NULL,
  usetimefrom24 char(6) default NULL,
  usetimeto24 char(6) default NULL,
  komaflg24 char(1) default NULL,
  usetimefrom25 char(6) default NULL,
  usetimeto25 char(6) default NULL,
  komaflg25 char(1) default NULL,
  usetimefrom26 char(6) default NULL,
  usetimeto26 char(6) default NULL,
  komaflg26 char(1) default NULL,
  usetimefrom27 char(6) default NULL,
  usetimeto27 char(6) default NULL,
  komaflg27 char(1) default NULL,
  usetimefrom28 char(6) default NULL,
  usetimeto28 char(6) default NULL,
  komaflg28 char(1) default NULL,
  usetimefrom29 char(6) default NULL,
  usetimeto29 char(6) default NULL,
  komaflg29 char(1) default NULL,
  usetimefrom30 char(6) default NULL,
  usetimeto30 char(6) default NULL,
  komaflg30 char(1) default NULL,
  usetimefrom31 char(6) default NULL,
  usetimeto31 char(6) default NULL,
  komaflg31 char(1) default NULL,
  usetimefrom32 char(6) default NULL,
  usetimeto32 char(6) default NULL,
  komaflg32 char(1) default NULL,
  usetimefrom33 char(6) default NULL,
  usetimeto33 char(6) default NULL,
  komaflg33 char(1) default NULL,
  usetimefrom34 char(6) default NULL,
  usetimeto34 char(6) default NULL,
  komaflg34 char(1) default NULL,
  usetimefrom35 char(6) default NULL,
  usetimeto35 char(6) default NULL,
  komaflg35 char(1) default NULL,
  usetimefrom36 char(6) default NULL,
  usetimeto36 char(6) default NULL,
  komaflg36 char(1) default NULL,
  usetimefrom37 char(6) default NULL,
  usetimeto37 char(6) default NULL,
  komaflg37 char(1) default NULL,
  usetimefrom38 char(6) default NULL,
  usetimeto38 char(6) default NULL,
  komaflg38 char(1) default NULL,
  usetimefrom39 char(6) default NULL,
  usetimeto39 char(6) default NULL,
  komaflg39 char(1) default NULL,
  usetimefrom40 char(6) default NULL,
  usetimeto40 char(6) default NULL,
  komaflg40 char(1) default NULL,
  usetimefrom41 char(6) default NULL,
  usetimeto41 char(6) default NULL,
  komaflg41 char(1) default NULL,
  usetimefrom42 char(6) default NULL,
  usetimeto42 char(6) default NULL,
  komaflg42 char(1) default NULL,
  usetimefrom43 char(6) default NULL,
  usetimeto43 char(6) default NULL,
  komaflg43 char(1) default NULL,
  usetimefrom44 char(6) default NULL,
  usetimeto44 char(6) default NULL,
  komaflg44 char(1) default NULL,
  usetimefrom45 char(6) default NULL,
  usetimeto45 char(6) default NULL,
  komaflg45 char(1) default NULL,
  usetimefrom46 char(6) default NULL,
  usetimeto46 char(6) default NULL,
  komaflg46 char(1) default NULL,
  usetimefrom47 char(6) default NULL,
  usetimeto47 char(6) default NULL,
  komaflg47 char(1) default NULL,
  usetimefrom48 char(6) default NULL,
  usetimeto48 char(6) default NULL,
  komaflg48 char(1) default NULL,
  usetimefrom49 char(6) default NULL,
  usetimeto49 char(6) default NULL,
  komaflg49 char(1) default NULL,
  usetimefrom50 char(6) default NULL,
  usetimeto50 char(6) default NULL,
  komaflg50 char(1) default NULL,
  upddate char(8) default NULL,
  updtime char(6) default NULL,
  updid varchar(16) default NULL,
  PRIMARY KEY (localgovcode,usedate,shisetsucode,shitsujyocode,mencode)
);
