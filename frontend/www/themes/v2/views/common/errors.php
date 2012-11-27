<script id="errorpopup-e404-template" type="text/html">
<div id="errorpopup-e404" class="body-popup">
  <div id="layer">
    <div class="pv_cont error">
      <table cellspacing="0" cellpadding="0">
        <tbody>
          <tr>
            <td>
              <div id="pv_box">
            <div class="contentText">
              <div>
                <h1>Неверно заданы даты</h1>
                <p>Дата вылета туда не может быть раньше даты вылета обратно. Установите дату обратного перелета 14 августа или позднее.</p>
                <p align="center">
                    <a href="#" class="btnBackMain" style="margin-top:40px" data-bind="click: close">Перейти на главную</a>
                </p>
            </div>
              </div>
            </td>
          </tr>
        </tbody>
      </table>
    </div>
    <div class="pv_switch">
    </div>
  </div>
</div>
</script>
<script id="errorpopup-e500-template" type="text/html">
<div id="errorpopup-e500" class="body-popup">
  <div id="layer">
    <div class="pv_cont error">
      <table cellspacing="0" cellpadding="0">
        <tbody>
          <tr>
            <td>
              <div id="pv_box">
            <div class="contentText">
              <div>
                <h1>Ошибка</h1>
                <p data-bind="text: data"></p>
                <p align="center">
                    <a href="#" class="btnBackMain" style="margin-top:40px" data-bind="click: close">Перейти на главную</a>
                </p>
            </div>
              </div>
            </td>
          </tr>
        </tbody>
      </table>
    </div>
    <div class="pv_switch">
    </div>
  </div>
</div>
</script>
