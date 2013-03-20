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
        CityManager.cityInfo.lacalRu = ko.observable('');
        CityManager.cityInfo.lacalEn = ko.observable('');
        CityManager.cityInfo.countryCode = ko.observable('');
        CityManager.cityInfo.countryName = ko.observable('');
        CityManager.cityInfo.position = ko.observable(0);
        CityManager.cityInfo.countAirports = ko.observable(1);
        CityManager.cityInfo.latitude = ko.observable('');
        CityManager.cityInfo.longitude = ko.observable('');
        CityManager.cityInfo.hotelbookId = ko.observable('');
        CityManager.cityInfo.metaphoneRu = ko.observable('');
        CityManager.cityInfo.caseNom = ko.observable('');
        CityManager.cityInfo.caseGen = ko.observable('');
        CityManager.cityInfo.caseDat = ko.observable('');
        CityManager.cityInfo.caseAcc = ko.observable('');
        CityManager.cityInfo.caseIns = ko.observable('');
        CityManager.cityInfo.casePre = ko.observable('');
        CityManager.hotelbookIds = ko.observableArray();
        CityManager.airportInfo.set = ko.observable(true);
        CityManager.airportInfo.code = ko.observable('');
        CityManager.airportInfo.icaoCode = ko.observable('');
        CityManager.airportInfo.lacalRu = ko.observable('');
        CityManager.airportInfo.lacalEn = ko.observable('');
        CityManager.airportInfo.cityCode = ko.observable('');
        CityManager.airportInfo.cityName = ko.observable('');
        CityManager.airportInfo.position = ko.observable(0);
        CityManager.airportInfo.latitude = ko.observable('');
        CityManager.airportInfo.longitude = ko.observable('');
        CityManager.airportInfo.site = ko.observable('');
    }

    CityManager.updateInfo = function(data){
        CityManager.ajaxSending = false;
        CityManager.cityInfo.set = ko.observable(true);
        CityManager.cityInfo.code = ko.observable('');
        CityManager.cityInfo.lacalRu = ko.observable('');
        CityManager.cityInfo.lacalEn = ko.observable('');
        CityManager.cityInfo.countryCode = ko.observable('');
        CityManager.cityInfo.countryName = ko.observable('');
        CityManager.cityInfo.position = ko.observable(0);
        CityManager.cityInfo.countAirports = ko.observable(1);
        CityManager.cityInfo.latitude = ko.observable('');
        CityManager.cityInfo.longitude = ko.observable('');
        CityManager.cityInfo.hotelbookId = ko.observable('');
        CityManager.cityInfo.metaphoneRu = ko.observable('');
        CityManager.cityInfo.caseNom = ko.observable('');
        CityManager.cityInfo.caseGen = ko.observable('');
        CityManager.cityInfo.caseDat = ko.observable('');
        CityManager.cityInfo.caseAcc = ko.observable('');
        CityManager.cityInfo.caseIns = ko.observable('');
        CityManager.cityInfo.casePre = ko.observable('');
        CityManager.hotelbookIds = ko.observableArray();
        CityManager.airportInfo.set = ko.observable(true);
        CityManager.airportInfo.code = ko.observable('');
        CityManager.airportInfo.icaoCode = ko.observable('');
        CityManager.airportInfo.lacalRu = ko.observable('');
        CityManager.airportInfo.lacalEn = ko.observable('');
        CityManager.airportInfo.cityCode = ko.observable('');
        CityManager.airportInfo.cityName = ko.observable('');
        CityManager.airportInfo.position = ko.observable(0);
        CityManager.airportInfo.latitude = ko.observable('');
        CityManager.airportInfo.longitude = ko.observable('');
        CityManager.airportInfo.site = ko.observable('');
    }

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
                            <input type="text" class="stdInput" id="cityIata" placeholder="IATA code города" data-bind="value: cityInfo.code">
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
                            <label>Приоритет:<input type="text" class="stdInput" id="cityPosition" value="0" placeholder="Приоритет">
                            </label>
                        </td>
                    </tr>
                    <tr>
                        <td>
                            <label>Количество аэропортов:<input type="text" class="stdInput" id="cityCountAirports" value="1">
                            </label>
                        </td>
                    </tr>
                    <tr>
                        <td>
                            <label>Широта:<input type="text" class="stdInput" id="cityLatitude" value="">

                            </label>
                        </td>
                    </tr>
                    <tr>
                        <td>
                            <label>Долгота:<input type="text" class="stdInput" id="cityLongitude" value="">
                            </label>
                        </td>
                    </tr>
                    <tr>
                        <td>
                            <label>hotelbookId:<input type="text" class="stdInput" id="cityHotelbookId" value="">
                            </label>
                        </td>
                    </tr>
                    <tr>
                        <td>
                            <label>metaphoneRu:<input type="text" class="stdInput" id="cityMetaphoneRu" value="">
                            </label>
                        </td>
                    </tr>
                    <tr>
                        <td>
                            <label>Город Им.падеж:<input type="text" class="stdInput" id="cityCaseNom" value="">
                            </label>
                        </td>
                    </tr>
                    <tr>
                        <td>
                            <label>Город Род.падеж(нет чего?):<input type="text" class="stdInput" id="cityCaseGen" value="">
                            </label>
                        </td>
                    </tr>
                    <tr>
                        <td>
                            <label>Город Дат.падеж(давать чему):<input type="text" class="stdInput" id="cityCaseDat" value="">
                            </label>
                        </td>
                    </tr>
                    <tr>
                        <td>
                            <label>Город Винит.падеж(винить что?):<input type="text" class="stdInput" id="cityCaseAcc" value="">
                            </label>
                        </td>
                    </tr>
                    <tr>
                        <td>
                            <label>Город Твор.падеж(доволен чем?):<input type="text" class="stdInput" id="cityCaseIns" value="">
                            </label>
                        </td>
                    </tr>
                    <tr>
                        <td>
                            <label>Город Предл.падеж(думать о чем?):<input type="text" class="stdInput" id="cityCasePre" value="">
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
                            <label class="checkbox"><input type="checkbox" checked="checked" class="stdInput" id="airportCheck">Добавить аэропорт</label>
                        </td>
                    </tr>
                    <tr>
                        <td>
                            <input type="text" class="stdInput" id="airportIata" placeholder="airport IATA code">
                        </td>
                    </tr>
                    <tr>
                        <td>
                            <input type="text" class="stdInput" id="airportIcao" placeholder="airport ICAO code">
                        </td>
                    </tr>
                    <tr>
                        <td>
                            <input type="text" class="stdInput" id="airportLocalRu" placeholder="RU airport Name">
                        </td>
                    </tr>
                    <tr>
                        <td>
                            <input type="text" class="stdInput" id="airportLocalEn" placeholder="En airport Name">
                        </td>
                    </tr>
                    <tr>
                        <td>
                            <input type="hidden" id="airportCityId" value="0">
                            <input type="text" class="stdInput" id="airportCityName" placeholder="City Name(default new city)">
                        </td>
                    </tr>
                    <tr>
                        <td>
                            <label>Широта:<input type="text" class="stdInput" id="airportLatitude" value="">

                            </label>
                        </td>
                    </tr>
                    <tr>
                        <td>
                            <label>Долгота:<input type="text" class="stdInput" id="airportLongitude" value="">
                            </label>
                        </td>
                    </tr>
                    <tr>
                        <td>
                            <label>Сайт аэропорта:<input type="text" class="stdInput" id="airportSite" value="">
                            </label>
                        </td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>
