<?php

class m130205_150648_ticketingplate extends CDbMigration
{
    public function up()
    {
        $this->execute("ALTER TABLE airline ADD ticketingplate CHAR(3) DEFAULT '   ';");
        $this->execute("UPDATE airline SET ticketingplate='006' WHERE code='DL';");
        $this->execute("UPDATE airline SET ticketingplate='881' WHERE code='DE';");
        $this->execute("UPDATE airline SET ticketingplate='623' WHERE code='FB';");
        $this->execute("UPDATE airline SET ticketingplate='607' WHERE code='EY';");
        $this->execute("UPDATE airline SET ticketingplate='815' WHERE code='EP';");
        $this->execute("UPDATE airline SET ticketingplate='071' WHERE code='ET';");
        $this->execute("UPDATE airline SET ticketingplate='176' WHERE code='EK';");
        $this->execute("UPDATE airline SET ticketingplate='195' WHERE code='FV';");
        $this->execute("UPDATE airline SET ticketingplate='260' WHERE code='FJ';");
        $this->execute("UPDATE airline SET ticketingplate='108' WHERE code='FI';");
        $this->execute("UPDATE airline SET ticketingplate='113' WHERE code='GW';");
        $this->execute("UPDATE airline SET ticketingplate='390' WHERE code='A3';");
        $this->execute("UPDATE airline SET ticketingplate='147' WHERE code='AT';");
        $this->execute("UPDATE airline SET ticketingplate='628' WHERE code='B2';");
        $this->execute("UPDATE airline SET ticketingplate='055' WHERE code='AZ';");
        $this->execute("UPDATE airline SET ticketingplate='105' WHERE code='AY';");
        $this->execute("UPDATE airline SET ticketingplate='125' WHERE code='BA';");
        $this->execute("UPDATE airline SET ticketingplate='001' WHERE code='AA';");
        $this->execute("UPDATE airline SET ticketingplate='745' WHERE code='AB';");
        $this->execute("UPDATE airline SET ticketingplate='124' WHERE code='AH';");
        $this->execute("UPDATE airline SET ticketingplate='057' WHERE code='AF';");
        $this->execute("UPDATE airline SET ticketingplate='139' WHERE code='AM';");
        $this->execute("UPDATE airline SET ticketingplate='657' WHERE code='BT';");
        $this->execute("UPDATE airline SET ticketingplate='999' WHERE code='CA';");
        $this->execute("UPDATE airline SET ticketingplate='169' WHERE code='BH';");
        $this->execute("UPDATE airline SET ticketingplate='672' WHERE code='BI';");
        $this->execute("UPDATE airline SET ticketingplate='236' WHERE code='BD';");
        $this->execute("UPDATE airline SET ticketingplate='784' WHERE code='CZ';");
        $this->execute("UPDATE airline SET ticketingplate='048' WHERE code='CY';");
        $this->execute("UPDATE airline SET ticketingplate='160' WHERE code='CX';");
        $this->execute("UPDATE airline SET ticketingplate='625' WHERE code='D6';");
        $this->execute("UPDATE airline SET ticketingplate='596' WHERE code='CS';");
        $this->execute("UPDATE airline SET ticketingplate='297' WHERE code='CI';");
        $this->execute("UPDATE airline SET ticketingplate='293' WHERE code='M7';");
        $this->execute("UPDATE airline SET ticketingplate='114' WHERE code='LY';");
        $this->execute("UPDATE airline SET ticketingplate='724' WHERE code='LX';");
        $this->execute("UPDATE airline SET ticketingplate='080' WHERE code='LO';");
        $this->execute("UPDATE airline SET ticketingplate='239' WHERE code='MK';");
        $this->execute("UPDATE airline SET ticketingplate='258' WHERE code='MD';");
        $this->execute("UPDATE airline SET ticketingplate='182' WHERE code='MA';");
        $this->execute("UPDATE airline SET ticketingplate='781' WHERE code='MU';");
        $this->execute("UPDATE airline SET ticketingplate='077' WHERE code='MS';");
        $this->execute("UPDATE airline SET ticketingplate='218' WHERE code='NF';");
        $this->execute("UPDATE airline SET ticketingplate='086' WHERE code='NZ';");
        $this->execute("UPDATE airline SET ticketingplate='675' WHERE code='NX';");
        $this->execute("UPDATE airline SET ticketingplate='474' WHERE code='NT';");
        $this->execute("UPDATE airline SET ticketingplate='823' WHERE code='NN';");
        $this->execute("UPDATE airline SET ticketingplate='289' WHERE code='OM';");
        $this->execute("UPDATE airline SET ticketingplate='064' WHERE code='OK';");
        $this->execute("UPDATE airline SET ticketingplate='050' WHERE code='OA';");
        $this->execute("UPDATE airline SET ticketingplate='845' WHERE code='P5';");
        $this->execute("UPDATE airline SET ticketingplate='257' WHERE code='OS';");
        $this->execute("UPDATE airline SET ticketingplate='960' WHERE code='OV';");
        $this->execute("UPDATE airline SET ticketingplate='831' WHERE code='OU';");
        $this->execute("UPDATE airline SET ticketingplate='659' WHERE code='P0';");
        $this->execute("UPDATE airline SET ticketingplate='829' WHERE code='PG';");
        $this->execute("UPDATE airline SET ticketingplate='061' WHERE code='HM';");
        $this->execute("UPDATE airline SET ticketingplate='169' WHERE code='HR';");
        $this->execute("UPDATE airline SET ticketingplate='880' WHERE code='HU';");
        $this->execute("UPDATE airline SET ticketingplate='851' WHERE code='HX';");
        $this->execute("UPDATE airline SET ticketingplate='075' WHERE code='IB';");
        $this->execute("UPDATE airline SET ticketingplate='191' WHERE code='IG';");
        $this->execute("UPDATE airline SET ticketingplate='402' WHERE code='J0';");
        $this->execute("UPDATE airline SET ticketingplate='154' WHERE code='IO';");
        $this->execute("UPDATE airline SET ticketingplate='771' WHERE code='J2';");
        $this->execute("UPDATE airline SET ticketingplate='090' WHERE code='IT';");
        $this->execute("UPDATE airline SET ticketingplate='635' WHERE code='IY';");
        $this->execute("UPDATE airline SET ticketingplate='995' WHERE code='JA';");
        $this->execute("UPDATE airline SET ticketingplate='165' WHERE code='JP';");
        $this->execute("UPDATE airline SET ticketingplate='131' WHERE code='JL';");
        $this->execute("UPDATE airline SET ticketingplate='957' WHERE code='JJ';");
        $this->execute("UPDATE airline SET ticketingplate='115' WHERE code='JU';");
        $this->execute("UPDATE airline SET ticketingplate='220' WHERE code='LH';");
        $this->execute("UPDATE airline SET ticketingplate='149' WHERE code='LG';");
        $this->execute("UPDATE airline SET ticketingplate='135' WHERE code='VT';");
        $this->execute("UPDATE airline SET ticketingplate='696' WHERE code='VR';");
        $this->execute("UPDATE airline SET ticketingplate='932' WHERE code='VS';");
        $this->execute("UPDATE airline SET ticketingplate='738' WHERE code='VN';");
        $this->execute("UPDATE airline SET ticketingplate='760' WHERE code='UU';");
        $this->execute("UPDATE airline SET ticketingplate='298' WHERE code='UT';");
        $this->execute("UPDATE airline SET ticketingplate='168' WHERE code='UM';");
        $this->execute("UPDATE airline SET ticketingplate='670' WHERE code='UN';");
        $this->execute("UPDATE airline SET ticketingplate='128' WHERE code='UO';");
        $this->execute("UPDATE airline SET ticketingplate='016' WHERE code='UA';");
        $this->execute("UPDATE airline SET ticketingplate='262' WHERE code='U6';");
        $this->execute("UPDATE airline SET ticketingplate='669' WHERE code='U8';");
        $this->execute("UPDATE airline SET ticketingplate='492' WHERE code='XW';");
        $this->execute("UPDATE airline SET ticketingplate='462' WHERE code='XL';");
        $this->execute("UPDATE airline SET ticketingplate='277' WHERE code='XF';");
        $this->execute("UPDATE airline SET ticketingplate='870' WHERE code='VV';");
        $this->execute("UPDATE airline SET ticketingplate='070' WHERE code='RB';");
        $this->execute("UPDATE airline SET ticketingplate='512' WHERE code='RJ';");
        $this->execute("UPDATE airline SET ticketingplate='157' WHERE code='QR';");
        $this->execute("UPDATE airline SET ticketingplate='081' WHERE code='QF';");
        $this->execute("UPDATE airline SET ticketingplate='031' WHERE code='PW';");
        $this->execute("UPDATE airline SET ticketingplate='566' WHERE code='PS';");
        $this->execute("UPDATE airline SET ticketingplate='217' WHERE code='TG';");
        $this->execute("UPDATE airline SET ticketingplate='235' WHERE code='TK';");
        $this->execute("UPDATE airline SET ticketingplate='244' WHERE code='TN';");
        $this->execute("UPDATE airline SET ticketingplate='047' WHERE code='TP';");
        $this->execute("UPDATE airline SET ticketingplate='555' WHERE code='SU';");
        $this->execute("UPDATE airline SET ticketingplate='186' WHERE code='SW';");
        $this->execute("UPDATE airline SET ticketingplate='202' WHERE code='TA';");
        $this->execute("UPDATE airline SET ticketingplate='200' WHERE code='SD';");
        $this->execute("UPDATE airline SET ticketingplate='117' WHERE code='SK';");
        $this->execute("UPDATE airline SET ticketingplate='082' WHERE code='SN';");
        $this->execute("UPDATE airline SET ticketingplate='618' WHERE code='SQ';");
        $this->execute("UPDATE airline SET ticketingplate='331' WHERE code='S4';");
        $this->execute("UPDATE airline SET ticketingplate='421' WHERE code='S7';");
        $this->execute("UPDATE airline SET ticketingplate='479' WHERE code='ZH';");
        $this->execute("UPDATE airline SET ticketingplate='439' WHERE code='ZI';");
        $this->execute("UPDATE airline SET ticketingplate='747' WHERE code='YO';");
        $this->execute("UPDATE airline SET ticketingplate='409' WHERE code='YM';");
        $this->execute("UPDATE airline SET ticketingplate='383' WHERE code='2U';");
        $this->execute("UPDATE airline SET ticketingplate='169' WHERE code='3E';");
        $this->execute("UPDATE airline SET ticketingplate='860' WHERE code='2M';");
        $this->execute("UPDATE airline SET ticketingplate='499' WHERE code='7B';");
        $this->execute("UPDATE airline SET ticketingplate='897' WHERE code='7D';");
        $this->execute("UPDATE airline SET ticketingplate='818' WHERE code='6H';");
        $this->execute("UPDATE airline SET ticketingplate='316' WHERE code='5N';");
        $this->execute("UPDATE airline SET ticketingplate='876' WHERE code='3U';");
        $this->execute("UPDATE airline SET ticketingplate='572' WHERE code='9U';");
        $this->execute("UPDATE airline SET ticketingplate='589' WHERE code='9W';");
        $this->execute("UPDATE airline SET ticketingplate='371' WHERE code='9D';");
        $this->execute("UPDATE airline SET ticketingplate='546' WHERE code='8U';");
        $this->execute("UPDATE airline SET ticketingplate='461' WHERE code='7W';");
        $this->execute("UPDATE airline SET ticketingplate='733' WHERE code='D9';");
        $this->execute("UPDATE airline SET ticketingplate='169' WHERE code='1X';");
        $this->execute("UPDATE airline SET ticketingplate='530' WHERE code='T0';");
        $this->execute("UPDATE airline SET ticketingplate='208' WHERE code='B3';");
        $this->execute("UPDATE airline SET ticketingplate='685' WHERE code='NI';");
        $this->execute("UPDATE airline SET ticketingplate='814' WHERE code='9F';");
    }

    public function down()
    {
        echo "m130205_150648_ticketingplate does not support migration down.\n";
        return false;
    }

    /*
    // Use safeUp/safeDown to do migration with transaction
    public function safeUp()
    {
    }

    public function safeDown()
    {
    }
    */
}