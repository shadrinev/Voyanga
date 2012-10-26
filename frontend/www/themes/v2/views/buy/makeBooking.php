<div id="content">
		<!--=== ===-->
		<div class="oneBlock">
		<!--=== ===-->
			<div class="paybuyContent">
				<h1>Ваши покупки</h1>
				<!-- ALL TICKETS DIV -->
				<div class="allTicketsDIV">
					<table class="aviaTickets">
						<tr>
							<td class="tdICO"></td>
							<td class="tdFrom">
								<div class="what">Вылет</div>
								<div class="city"><span>Санкт-Петербург</span>, <span class="airport">Пулково-1</span></div>
								<div class="time"><span>2:35</span>, <span class="date">28 мая</span></div>
							</td>
							<td class="tdPath"> 3 </td>
							<td class="tdTo">
								<div class="what">Прилет</div>
								<div class="city"><span>Санкт-Петербург</span>, <span class="airport">Пулково-1</span></div>
								<div class="time"><span>2:35</span>, <span class="date">28 мая</span></div>
							</td>
							<td class="tdAvia">
								<div class="airline">Аэрофлот</div>
								<div class="voyage">Рейс: <span class="number">S7-76</span></div>
								<div class="class">Класс: <span class="classMine">Эконом</span></div>
							</td>
							<td class="tdPrice">
								<div class="price">12 500 <span class="rur">o</span></div>
								<div class="priceSale">
									<!-- <div class="lastPrice">13 000 <span class="rur">o</span></div> <span class="icoTours"></span> -->
								</div>
								<div class="moreDetails"><a href="javascript:void(0);">Подробнее</a></div>
							</td>
						</tr>
					</table>
					<table class="aviaTickets">
						<tr>
							<td class="tdICO"></td>
							<td class="tdFrom">
								<div class="what">Вылет</div>
								<div class="city"><span>Санкт-Петербург</span>, <span class="airport">Пулково-1</span></div>
								<div class="time"><span>2:35</span>, <span class="date">28 мая</span></div>
							</td>
							<td class="tdPath"> 3 </td>
							<td class="tdTo">
								<div class="what">Прилет</div>
								<div class="city"><span>Санкт-Петербург</span>, <span class="airport">Пулково-1</span></div>
								<div class="time"><span>2:35</span>, <span class="date">28 мая</span></div>
							</td>
							<td class="tdAvia">
								<div class="airline">Аэрофлот</div>
								<div class="voyage">Рейс: <span class="number">S7-76</span></div>
								<div class="class">Класс: <span class="classMine">Эконом</span></div>
							</td>
							<td class="tdPrice">
								<div class="price">12 500 <span class="rur">o</span></div>
								<div class="priceSale">
									<!-- <div class="lastPrice">13 000 <span class="rur">o</span></div> <span class="icoTours"></span> -->
								</div>
								<div class="moreDetails"><a href="javascript:void(0);">Подробнее</a></div>
							</td>
						</tr>
					</table>
					<!-- HOTEL TICKETS -->
					<table class="hotelTickets">
						<tr>
							<td class="tdICO"></td>
							<td class="tdHotel">
								<div class="what">Вылет</div>
								<div class="nameHostel">Коринтия Крестовский Парк</div>
								<div class="howPlace">Двухместный номер люкс</div>
							</td>
							<td class="tdInfo">
								<div class="adress"><span class="name">Адрес:</span> <span>Санкт-Петербург, Крестовский остров, 28/7</span></div>
								<div class="dateFrom"><span class="name">Дата заезда:</span> 14 июля 2012, с 15:00</div>
								<div class="dateTo"><span class="name">Дата выезда:</span> 15 июля 2012, до 17:00</div>
							</td>
							<td class="tdPrice">
								<div class="price">12 500 <span class="rur">o</span></div>
								<div class="priceSale">
									<div class="lastPrice">13 000 <span class="rur">o</span></div> <span class="icoTours"></span>
								</div>
								<div class="moreDetails"><a href="javascript:void(0);">Подробнее</a></div>
							</td>
						</tr>
					</table>
				</div>
				<!-- END ALL TICKETS DIV -->
				<div class="theSum">
					<div class="left">2 авиабилета, 1 гостиница</div>
					<div class="right">
						Итоговая стоимость <div class="price">37 500 <span class="rur">o</span></div>
					</div>
				</div>				
				<!-- END -->
			</div>
		<!--=== ===-->
		</div>
		<!--=== ===-->
        <form method="post" id="passport_form">
        <?php if(!$flightAmbiguous): ?>
		<div class="oneBlock">
		<!--=== ===-->

			<div class="paybuyContent">
				<h2><span class="ico-fly"></span> Перелет Санкт-Петербург - Москва</h2>
				<h3>Данные пассажиров</h3>

				<table class="infoPassengers">
					<thead>
						<tr>
							<td class="tdName">
								Имя
							</td>
							<td class="tdLasname">
								Фамилия
							</td>
							<td class="tdSex">
								Пол
							</td>
							<td class="tdBirthday">
								Дата рождения
							</td>
							<td class="tdNationality">
								Гражданство
							</td>
							<td class="tdDocumentNumber">
								Серия и № документа
							</td>
							<td class="tdDuration">
								Срок действия
							</td>
						</tr>
					</thead>
					<tbody>
                    <?php foreach($elements as $key=>$elInfo):?>
						<tr>
							<td class="tdName">
								<input type="text" name="name[<?php echo $key;?>]">
							</td>
							<td class="tdLastname">
								<input type="text" name="lastname[<?php echo $key;?>]">
							</td>
							<td class="tdSex">
								<div class="male active"></div>
								<div class="female"></div>
							</td>
							<td class="tdBirthday">
								<input type="text" name="dd[<?php echo $key;?>]" placeholder="ДД" class="dd" maxlength="2">
								<input type="text" name="mm[<?php echo $key;?>]" placeholder="ММ" class="mm" maxlength="2">
								<input type="text" name="yy[<?php echo $key;?>]" placeholder="ГГГГ" class="yy" maxlength="4">
							</td>
							<td class="tdNationality">
								<input type="text" name="nationality[<?php echo $key;?>]">
							</td>
							<td class="tdDocumentNumber">
								<input type="text" name="documentNumber[<?php echo $key;?>]">
							</td>
							<td class="tdDuration">
								<input type="text" name="dd_exp[<?php echo $key;?>]" placeholder="ДД" class="dd" maxlength="2">
								<input type="text" name="mm_exp[<?php echo $key;?>]" placeholder="ММ" class="mm" maxlength="2">
								<input type="text" name="yy_exp[<?php echo $key;?>]" placeholder="ГГГГ" class="yy" maxlength="4">
							</td>
						</tr>
						<tr>
							<td class="tdName">
								<input type="checkbox" name="bonus[<?php echo $key;?>]" id="bonus<?php echo $key;?>">
								<label for="bonus<?php echo $key;?>">Есть бонусная карта</label>
							</td>
							<td class="tdLastname">
								
							</td>
							<td class="tdSex">
								
							</td>
							<td class="tdBirthday">
								
							</td>
							<td class="tdNationality">
								
							</td>
							<td class="tdDocumentNumber">
								
							</td>
							<td class="tdDuration">
								<input type="checkbox" checked="checked" name="srok[<?php echo $key;?>]" id="srok<?php echo $key;?>">
								<label for="srok<?php echo $key;?>">Без срока</label>
							</td>
						</tr>
                        <?php endforeach; ?>
						<!-- NEW USER -->

					</tbody>
				</table>
			</div>

		<!--=== ===-->
		</div>
        <?php endif;/* Endif flightAmbiguous*/ ?>
        <?php if($flightAmbiguous): ?>
		<!--=== ===-->
		<div class="oneBlock">
		<!--=== ===-->
			<div class="paybuyContent">
				<h2><span class="ico-hotel"></span> Перелет Санкт-Петербург - Москва</h2>
				<h3>Данные гостей	</h3>
				<table class="infoPassengers">
					<thead>
						<tr>
							<td class="tdName">
								Имя
							</td>
							<td class="tdLasname">
								Фамилия
							</td>
							<td class="tdSex">
								
							</td>
							<td class="tdBirthday">
								
							</td>
							<td class="tdNationality">
								
							</td>
							<td class="tdDocumentNumber">
								
							</td>
							<td class="tdDuration">
								
							</td>
						</tr>
					</thead>
					<tbody>
						<tr>
							<td class="tdName">
								<input type="text" name="name">	
							</td>
							<td class="tdLastname">
								<input type="text" name="lastname">	
							</td>
							<td class="tdSex">
								
							</td>
							<td class="tdBirthday">
								
							</td>
							<td class="tdNationality">
								
							</td>
							<td class="tdDocumentNumber">
									
							</td>
							<td class="tdDuration">
								
							</td>
						</tr>
					</tbody>
				</table>
				<a href="#" class="addGuest">Добавить имена остальных гостей</a>
			</div>
		<!--=== ===-->
		</div>
		<!--=== ===-->
		<!--=== ===-->
		<div class="oneBlock">
		<!--=== ===-->
			<div class="paybuyContent">
				<h2><span class="ico-fly"></span> Перелет Санкт-Петербург - Москва</h2>
				<h3>Данные пассажиров</h3>
				<table class="infoPassengers">
					<thead>
						<tr>
							<td class="tdName">
								Имя
							</td>
							<td class="tdLasname">
								Фамилия
							</td>
							<td class="tdSex">
								Пол
							</td>
							<td class="tdBirthday">
								Дата рождения
							</td>
							<td class="tdNationality">
								Гражданство
							</td>
							<td class="tdDocumentNumber">
								Серия и № документа
							</td>
							<td class="tdDuration">
								Срок действия
							</td>
						</tr>
					</thead>
					<tbody>
						<tr>
							<td class="tdName">
								<input type="text" name="name">	
							</td>
							<td class="tdLastname">
								<input type="text" name="lastname">	
							</td>
							<td class="tdSex">
								<div class="male active"></div>
								<div class="female"></div>
							</td>
							<td class="tdBirthday">
								<input type="text" name="dd" placeholder="ДД" class="dd" maxlength="2">
								<input type="text" name="mm" placeholder="ММ" class="mm" maxlength="2">
								<input type="text" name="yy" placeholder="ГГГГ" class="yy" maxlength="4">
							</td>
							<td class="tdNationality">
								<input type="text" name="nationality">
							</td>
							<td class="tdDocumentNumber">
								<input type="text" name="documentNumber">	
							</td>
							<td class="tdDuration">
								<input type="text" name="dd" placeholder="ДД" class="dd" maxlength="2">
								<input type="text" name="mm" placeholder="ММ" class="mm" maxlength="2">
								<input type="text" name="yy" placeholder="ГГГГ" class="yy" maxlength="4">
							</td>
						</tr>
						<tr>
							<td class="tdName">
								<input type="checkbox" name="bonus" id="bonus">	
								<label for="bonus">Есть бонусная карта</label>
							</td>
							<td class="tdLastname">
								
							</td>
							<td class="tdSex">
								
							</td>
							<td class="tdBirthday">
								
							</td>
							<td class="tdNationality">
								
							</td>
							<td class="tdDocumentNumber">
								
							</td>
							<td class="tdDuration">
								<input type="checkbox" checked="checked" name="srok" id="srok">	
								<label for="srok">Без срока</label>
							</td>
						</tr>
						<!-- NEW USER -->
						<tr>
							<td class="tdName">
								<input type="text" name="name">	
							</td>
							<td class="tdLastname">
								<input type="text" name="lastname">	
							</td>
							<td class="tdSex">
								<div class="male active"></div>
								<div class="female"></div>
							</td>
							<td class="tdBirthday">
								<input type="text" name="dd" placeholder="ДД" class="dd" maxlength="2">
								<input type="text" name="mm" placeholder="ММ" class="mm" maxlength="2">
								<input type="text" name="yy" placeholder="ГГГГ" class="yy" maxlength="4">
							</td>
							<td class="tdNationality">
								<input type="text" name="nationality">
							</td>
							<td class="tdDocumentNumber">
								<input type="text" name="documentNumber">	
							</td>
							<td class="tdDuration">
								<input type="text" name="dd" placeholder="ДД" class="dd" maxlength="2">
								<input type="text" name="mm" placeholder="ММ" class="mm" maxlength="2">
								<input type="text" name="yy" placeholder="ГГГГ" class="yy" maxlength="4">
							</td>
						</tr>
						<tr>
							<td class="tdName">
								<input type="checkbox" name="bonus" id="bonus">	
								<label for="bonus">Есть бонусная карта</label>
							</td>
							<td class="tdLastname">
								
							</td>
							<td class="tdSex">
								
							</td>
							<td class="tdBirthday">
								
							</td>
							<td class="tdNationality">
								
							</td>
							<td class="tdDocumentNumber">
								
							</td>
							<td class="tdDuration">
								<input type="checkbox" checked="checked" name="srok" id="srok">	
								<label for="srok">Без срока</label>
							</td>
						</tr>
					</tbody>
				</table>
			</div>
		<!--=== ===-->
		</div>
		<!--=== ===-->
        <?php endif; ?>
		<!--=== ===-->
		<div class="oneBlock">
		<!--=== ===-->
			<div class="paybuyContent">
				<h2>Покупатель</h2>
				<table class="dopInfoPassengers">
					<thead>
						<tr>
							<td>Адрес электронной почты</td>
							<td>Номер телефона</td>
							<td></td>
						<tr>
					</thead>
					<tbody>
						<tr>
							<td class="tdEmail">
								<input type="text" name="contact_email">
							</td>
							<td class="tdTelefon">
								<input type="text" name="contact_phone">
							</td>
							<td class="tdText">
							Чтобы мы знали куда прислать электронный билет и куда звонить в случае каких-либо изменений
							</td>
						</tr>
					</tbody>
				</table>
			</div>
		<!--=== ===-->
		</div>
        <div class="paybuyEnd">
            <div class="btnBlue" onclick="$('#passport_form').submit()">
                <span>OK</span>
            </div>
            <div class="clear"></div>
        </div>
        </form>
		<!--=== ===-->
		<div class="payCardPal">
			&nbsp;
		</div>
		<div class="paybuyEnd">
			<div class="info">После нажатия кнопки «Купить» данные пассажиров попадут в систему бронирования, билет будет оформлен и выслан вам на указанный электронный адрес в течение нескольких минут. Нажимая «Купить», вы соглашаетесь с условиями использования, правилами IATA и правилами тарифов.</div>
			<div class="clear"></div>
		</div>
		<div class="paybuyEnd">
				<div class="btnBlue">
					<span>Забронировать</span>&nbsp;&nbsp;
					<span class="price">33 770</span> 
					<span class="rur">o</span>
					
					<span class="l"></span>
				</div>
			<div class="clear"></div>
		</div>
		<div class="paybuyEnd">
			<div class="armoring">
				<div class="btnBlue">
					<span>Бронирование</span>
					<div class="dotted"></div>
					<span class="l"></span>
				</div>
				<div class="text">
					Процесс бронирования может занять до 45 секунд...
				</div>
			</div>
			<div class="clear"></div>
		</div>
</div>