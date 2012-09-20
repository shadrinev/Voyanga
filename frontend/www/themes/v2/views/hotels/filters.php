<script id="hotels-filters" type="text/html">
    <div class="filter-content">
      Hello filters
        <div class="div-filter">
            <h4>Дополнительно <a href="#" class="clean" data-bind="click: results.allFilters.servicesFilter.reset">Очистить</a></h4>

            <ul data-bind="foreach: results.allFilters.servicesFilter.services">
                <!-- ko if: $index() < 3 -->
                <li><input type="checkbox" data-bind="checked: active,attr:{id: 'hserv-'+$index()}"> <label data-bind="text: name,attr:{for: 'hserv-'+$index()}">Аэрофлот</label></li>
                <!-- /ko -->
            </ul>
            <!-- ko if: results.allFilters.servicesFilter.services.length > 3 -->
            <div id="more-services" class="more-filters">
                <ul data-bind="foreach: results.allFilters.servicesFilter.services">
                    <!-- ko if: $index() >= 3 -->
                    <li><input type="checkbox" data-bind="checked: active,attr:{id: 'hserv-'+$index()}"> <label data-bind="text: name,attr:{for: 'hserv-'+$index()}">Аэрофлот</label></li>
                    <!-- /ko -->
                </ul>
            </div>
            <div class="all-list">
                <a href="#" onclick="return AviaFilters.showMoreDiv(this,'more-services')">Все услуги</a>
            </div>
            <!-- /ko -->

        </div>
    </div>
</script>
