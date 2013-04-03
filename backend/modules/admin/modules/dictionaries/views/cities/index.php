<?php
$this->breadcrumbs=array(
	'Dictionaries',
);

$this->menu=array(
	array('label'=>'Create Event','url'=>array('create')),
	array('label'=>'Manage Event','url'=>array('admin')),
);
?>
<script type="text/javascript" src="http://knockoutjs.com/js/knockout-2.2.0.js"></script>
<script>

    var CityManager = {};
    CityManager.cityInfo = {};
    CityManager.airportInfo = {};
    CityManager.init = function(){
        CityManager.ajaxSending = false;
        CityManager.cityInfo.set = ko.observable(true);
        CityManager.cityInfo.code = ko.observable('');
        CityManager.cityInfo.localRu = ko.observable('');
        CityManager.cityInfo.id = ko.observable('');
        CityManager.cityInfo.localEn = ko.observable('');
        CityManager.cityInfo.countryCode = ko.observable('');
        CityManager.cityInfo.countryCode.subscribe(function (newValue){
            if(!CityManager.ajaxSending && newValue.length > 0){
                CityManager.ajaxSending = true;
                $.ajax({
                    url: "/admin/dictionaries/cities/getCountryByCode/",
                    dataType: 'json',
                    type: 'post',
                    data: {countryCode: newValue},
                    timeout: 200000,
                    success: function(data){
                        CityManager.cityInfo.countryName(data.countryName);
                        CityManager.ajaxSending = false;
                    },
                    error: function(){
                        CityManager.ajaxSending = false;
                    }
                });
            }
        });
        CityManager.cityInfo.countryName = ko.observable('');
        CityManager.cityInfo.countryId = ko.observable('');
        CityManager.cityInfo.position = ko.observable(0);
        CityManager.cityInfo.countAirports = ko.observable(1);
        CityManager.cityInfo.latitude = ko.observable('');
        CityManager.cityInfo.longitude = ko.observable('');
        CityManager.cityInfo.hotelbookId = ko.observable('');
        CityManager.cityInfo.metaphoneRu = ko.observable('');
        CityManager.cityInfo.stateCode = ko.observable('');
        CityManager.cityInfo.caseNom = ko.observable('');
        CityManager.cityInfo.caseGen = ko.observable('');
        CityManager.cityInfo.caseDat = ko.observable('');
        CityManager.cityInfo.caseAcc = ko.observable('');
        CityManager.cityInfo.caseIns = ko.observable('');
        CityManager.cityInfo.casePre = ko.observable('');
        CityManager.cityInfo.code.subscribe(function (newValue){CityManager.subscribeFunc(CityManager.subscribeTypes.cityCode);});
        CityManager.cityInfo.localRu.subscribe(function (newValue){CityManager.subscribeFunc(CityManager.subscribeTypes.cityNameRu);});
        CityManager.hotelbookIds = ko.observableArray();
        CityManager.airportInfo.set = ko.observable(true);
        CityManager.airportInfo.id = ko.observable('');
        CityManager.airportInfo.code = ko.observable('');
        CityManager.airportInfo.code.subscribe(function (newValue){CityManager.subscribeFunc(CityManager.subscribeTypes.airportCode);});
        CityManager.airportInfo.icaoCode = ko.observable('');
        CityManager.airportInfo.localRu = ko.observable('');
        CityManager.airportInfo.localEn = ko.observable('');
        CityManager.airportInfo.cityCode = ko.observable('');
        CityManager.airportInfo.cityCode.subscribe(function (newValue){
            if(!CityManager.ajaxSending && newValue.length > 0){
                CityManager.ajaxSending = true;
                $.ajax({
                    url: "/admin/dictionaries/cities/getCityByCode/",
                    dataType: 'json',
                    type: 'post',
                    data: {cityCode: newValue},
                    timeout: 200000,
                    success: function(data){
                        CityManager.airportInfo.cityName(data.cityName);
                        CityManager.ajaxSending = false;
                    },
                    error: function(){
                        CityManager.ajaxSending = false;
                    }
                });
            }
        });
        CityManager.airportInfo.cityName = ko.observable('');
        CityManager.airportInfo.cityId = ko.observable('');
        CityManager.airportInfo.position = ko.observable(0);
        CityManager.airportInfo.latitude = ko.observable('');
        CityManager.airportInfo.longitude = ko.observable('');
        CityManager.airportInfo.site = ko.observable('');
        CityManager.saveReturn = ko.observable('');
    };

    CityManager.subscribeTypes = {'cityCode':1,'airportCode':2,'cityNameRu':3};
    CityManager.lastEventType = 0;
    CityManager.subscribeFunc = function(eventType){
        if(!CityManager.ajaxSending){
            var data = {};
            data.city = {};
            for(var i in CityManager.cityInfo){
                data.city[i] = CityManager.cityInfo[i]();
            }
            data.airport = {};
            for(var i in CityManager.airportInfo){
                data.airport[i] = CityManager.airportInfo[i]();
            }
            if(eventType == CityManager.subscribeTypes.cityCode){
                data.code = CityManager.cityInfo.code();
                if(data.code.length != 3 || !CityManager.cityInfo.set()){
                    return false;
                }
                CityManager.lastEventType = CityManager.subscribeTypes.cityCode;
            }
            if(eventType == CityManager.subscribeTypes.airportCode){
                data.code = CityManager.airportInfo.code();
                if(data.code.length != 3 || !CityManager.airportInfo.set()){
                    return false;
                }
                CityManager.lastEventType = CityManager.subscribeTypes.airportCode;
            }
            if(eventType == CityManager.subscribeTypes.cityNameRu){
                data.ruNameChange = '1';
                CityManager.lastEventType = CityManager.subscribeTypes.cityNameRu;
            }

            CityManager.ajaxSending = true;
            $.ajax({
                url: "/admin/dictionaries/cities/getInfoByIata/",
                dataType: 'json',
                type: 'post',
                data: data,
                timeout: 200000,
                success: function(data){
                    CityManager.updateInfo(data);
                    CityManager.ajaxSending = false;
                },
                error: function(){
                    CityManager.ajaxSending = false;
                }
            });
        }
    };
    CityManager.hotelItemClick = function(arg1,arg2){
        CityManager.cityInfo.hotelbookId(arg1.id);
    };
    CityManager.saveInfo = function(){
        if(!CityManager.ajaxSending){
            var data = {};
            data.city = {};
            for(var i in CityManager.cityInfo){
                data.city[i] = CityManager.cityInfo[i]();
            }
            data.airport = {};
            for(var i in CityManager.airportInfo){
                data.airport[i] = CityManager.airportInfo[i]();
            }

            CityManager.ajaxSending = true;
            $.ajax({
                url: "/admin/dictionaries/cities/save/",
                dataType: 'json',
                type: 'post',
                data: data,
                timeout: 200000,
                success: function(data){
                    console.log('return',data);
                    CityManager.saveReturn(data.saveReturn);
                    if(data.cityId){
                        CityManager.cityInfo.id(data.cityId);
                        CityManager.cityInfo.countryId(data.cityCountryId);
                    }
                    if(data.airportId){
                        CityManager.airportInfo.id(data.airportId);
                        CityManager.airportInfo.cityId(data.airportCityId);
                    }
                    CityManager.ajaxSending = false;
                },
                error: function(){
                    CityManager.ajaxSending = false;
                }
            });
        }
    };


    CityManager.updateInfo = function(data){
        if(!data.error){
            for(var i in data.city){
                CityManager.cityInfo[i](data.city[i]);
            }
            for(var i in data.airport){
                CityManager.airportInfo[i](data.airport[i]);
            }
            CityManager.hotelbookIds([])
            if(data.hotelbookIds){
                CityManager.hotelbookIds(data.hotelbookIds);
            }
        }else{
            console.log('error',data.error,data);
        }
    };


    $(document).ready(function(){
        CityManager.init();
        ko.applyBindings(CityManager);
    });
</script>
<h1>Добавление городов и аэропортов</h1>
    <table style="width: 100%">
        <tr>
            <td>
                <div data-bind=""></div>
            </td>
        </tr>
        <tr>
            <td>

                <table style="width: 100%">
                    <tr>
                        <td>
                             <label class="checkbox"><input type="checkbox" checked="checked" class="stdInput" id="cityCheck" data-bind="checked: cityInfo.set">Добавить город</label>
                        </td>
                    </tr>
                    <tr>
                        <td>
                            <input type="text" class="stdInput" id="cityIata" placeholder="IATA code города" data-bind="value: cityInfo.code"><span data-bind="visible: cityInfo.id,text: '('+cityInfo.id() + ')'"></span>
                        </td>
                    </tr>
                    <tr>
                        <td>
                            <input type="text" class="stdInput" id="cityNameRu" placeholder="Назвоние города" data-bind="value: cityInfo.localRu">
                        </td>
                    </tr>
                    <tr>
                        <td>
                            <input type="text" class="stdInput" id="cityNameEn" placeholder="Назвоние города на английском" data-bind="value: cityInfo.localEn">
                        </td>
                    </tr>
                    <tr>
                        <td>
                            <input type="text" class="stdInput" id="cityCountryCode" placeholder="Код страны" data-bind="value: cityInfo.countryCode">
                            <input type="text" class="stdInput" id="cityCountry" placeholder="Назвоние страны" data-bind="value: cityInfo.countryName">
                        </td>
                    </tr>
                    <tr>
                        <td>
                            <label>Приоритет:<input type="text" class="stdInput" id="cityPosition" value="0" placeholder="Приоритет" data-bind="value: cityInfo.position">
                            </label>
                        </td>
                    </tr>
                    <tr>
                        <td>
                            <label>Количество аэропортов:<input type="text" class="stdInput" id="cityCountAirports" value="1"  data-bind="value: cityInfo.countAirports">
                            </label>
                        </td>
                    </tr>
                    <tr>
                        <td>
                            <label>Широта:<input type="text" class="stdInput" id="cityLatitude" value="" data-bind="value: cityInfo.latitude">

                            </label>
                        </td>
                    </tr>
                    <tr>
                        <td>
                            <label>Долгота:<input type="text" class="stdInput" id="cityLongitude" value="" data-bind="value: cityInfo.longitude">
                            </label>
                        </td>
                    </tr>
                    <tr>
                        <td>
                            <label>hotelbookId:<input type="text" class="stdInput" id="cityHotelbookId" value="" data-bind="value: cityInfo.hotelbookId">
                            </label>
                            <div data-bind="foreach: hotelbookIds">
                                <a data-bind="text: name,click: $parent.hotelItemClick"></a>
                            </div>
                        </td>
                    </tr>
                    <tr>
                        <td>
                            <label>metaphoneRu:<input type="text" class="stdInput" id="cityMetaphoneRu" value="" data-bind="value: cityInfo.metaphoneRu">
                            </label>
                        </td>
                    </tr>
                    <tr>
                        <td>
                            <input type="text" class="stdInput" id="cityStateCode" placeholder="Код региона" value="" data-bind="value: cityInfo.stateCode">

                        </td>
                    </tr>
                    <tr>
                        <td>
                            <label>Город Им.падеж:<input type="text" class="stdInput" id="cityCaseNom" value=""  data-bind="value: cityInfo.caseNom">
                            </label>
                        </td>
                    </tr>
                    <tr>
                        <td>
                            <label>Город Род.падеж(нет чего?):<input type="text" class="stdInput" id="cityCaseGen" value="" data-bind="value: cityInfo.caseGen">
                            </label>
                        </td>
                    </tr>
                    <tr>
                        <td>
                            <label>Город Дат.падеж(давать чему):<input type="text" class="stdInput" id="cityCaseDat" value="" data-bind="value: cityInfo.caseDat">
                            </label>
                        </td>
                    </tr>
                    <tr>
                        <td>
                            <label>Город Винит.падеж(винить что?):<input type="text" class="stdInput" id="cityCaseAcc" value="" data-bind="value: cityInfo.caseAcc">
                            </label>
                        </td>
                    </tr>
                    <tr>
                        <td>
                            <label>Город Твор.падеж(доволен чем?):<input type="text" class="stdInput" id="cityCaseIns" value="" data-bind="value: cityInfo.caseIns">
                            </label>
                        </td>
                    </tr>
                    <tr>
                        <td>
                            <label>Город Предл.падеж(думать о чем?):<input type="text" class="stdInput" id="cityCasePre" value="" data-bind="value: cityInfo.casePre">
                            </label>
                        </td>
                    </tr>
                    <tr>
                        <td>
                            mmm?<span data-bind="text: cityInfo.set"></span>
                        </td>
                    </tr>
                </table>

            </td>
            <td valign="top">
                <table>
                    <tr>
                        <td>
                            <label class="checkbox"><input type="checkbox" checked="checked" class="stdInput" id="airportCheck">Добавить аэропорт</label><span data-bind="visible: airportInfo.id,text: '('+airportInfo.id() + ')'"></span>
                        </td>
                    </tr>
                    <tr>
                        <td>
                            <input type="text" class="stdInput" id="airportIata" placeholder="airport IATA code" data-bind="value: airportInfo.code">
                        </td>
                    </tr>
                    <tr>
                        <td>
                            <input type="text" class="stdInput" id="airportIcao" placeholder="airport ICAO code" data-bind="value: airportInfo.icaoCode">
                        </td>
                    </tr>
                    <tr>
                        <td>
                            <input type="text" class="stdInput" id="airportLocalRu" placeholder="RU airport Name" data-bind="value: airportInfo.localRu">
                            <div data-bind="visible: airportInfo.localRu">
                                <a data-bind="text: airportInfo.localRu()+'(аэропорт)',attr:{href:'http://ru.wikipedia.org/wiki/'+airportInfo.localRu()+'_(аэропорт)'}"></a>
                            </div>
                        </td>
                    </tr>
                    <tr>
                        <td>
                            <input type="text" class="stdInput" id="airportLocalEn" placeholder="En airport Name" data-bind="value: airportInfo.localEn">
                        </td>
                    </tr>
                    <tr>
                        <td>
                            <input type="text" id="airportCityId" value="0" placeholder="city code" data-bind="value: airportInfo.cityCode">
                            <input type="text" class="stdInput" id="airportCityName" placeholder="City Name(default new city)"  data-bind="value: airportInfo.cityName">
                        </td>
                    </tr>
                    <tr>
                        <td>
                            <label>Широта:<input type="text" class="stdInput" id="airportLatitude" value="" data-bind="value: airportInfo.latitude">

                            </label>
                        </td>
                    </tr>
                    <tr>
                        <td>
                            <label>Долгота:<input type="text" class="stdInput" id="airportLongitude" value="" data-bind="value: airportInfo.longitude">
                            </label>
                        </td>
                    </tr>
                    <tr>
                        <td>
                            <label>Сайт аэропорта:<input type="text" class="stdInput" id="airportSite" value="" data-bind="value: airportInfo.site">
                            </label>
                        </td>
                    </tr>
                </table>
            </td>
        </tr>
        <tr>
            <td>
                <input type="submit" class="btn" value="save" data-bind="click: saveInfo">
            </td>
        </tr>
        <tr>
            <td colspan="2">
                <span data-bind="text: saveReturn"></span>
            </td>
        </tr>
        <tr>
            <td colspan="2">
                <span data-bind="visible: cityInfo.id">
                    INSERT INTO `city` (`id`, `position`, `countryId`, `code`, `localRu`, `localEn`, `countAirports`, `latitude`, `longitude`, `hotelbookId`,
                    `maxmindId`, `metaphoneRu`, `stateCode`, `caseNom`, `caseGen`, `caseDat`, `caseAcc`, `caseIns`, `casePre`) VALUES
                    ('<span  data-bind="text: cityInfo.id"></span>', '<span  data-bind="text: cityInfo.position"></span>', '<span  data-bind="text: cityInfo.countryId"></span>', '<span  data-bind="text: cityInfo.code"></span>', '<span  data-bind="text: cityInfo.localRu"></span>', '<span  data-bind="text: cityInfo.localEn"></span>', '<span  data-bind="text: cityInfo.countAirports"></span>', '<span  data-bind="text: cityInfo.latitude"></span>', '<span  data-bind="text: cityInfo.longitude"></span>',
                    '<span  data-bind="text: cityInfo.hotelbookId"></span>', NULL, '<span  data-bind="text: cityInfo.metaphoneRu"></span>', '<span  data-bind="text: cityInfo.stateCode"></span>',
                    '<span  data-bind="text: cityInfo.caseNom"></span>', '<span  data-bind="text: cityInfo.caseGen"></span>', '<span  data-bind="text: cityInfo.caseDat"></span>', '<span  data-bind="text: cityInfo.caseAcc"></span>', '<span  data-bind="text: cityInfo.caseIns"></span>', '<span  data-bind="text: cityInfo.casePre"></span>');<br/>
                </span>
                <span data-bind="visible: airportInfo.id">
                    INSERT INTO `airport` (`id`, `position`, `code`, `icaoCode`, `localRu`, `localEn`, `cityId`, `latitude`, `longitude`, `site`) VALUES
                    ('<span  data-bind="text: airportInfo.id"></span>', '<span  data-bind="text: airportInfo.position"></span>', '<span  data-bind="text: airportInfo.code"></span>', '<span  data-bind="text: airportInfo.icaoCode"></span>', '<span  data-bind="text: airportInfo.localRu"></span>', '<span  data-bind="text: airportInfo.localEn"></span>', '<span  data-bind="text: airportInfo.cityId"></span>', '<span  data-bind="text: airportInfo.latitude"></span>', '<span  data-bind="text: airportInfo.longitude"></span>', '<span  data-bind="text: airportInfo.site"></span>');
                </span>
            </td>
        </tr>
    </table>
