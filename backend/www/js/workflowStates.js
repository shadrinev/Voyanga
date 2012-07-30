/**
 * Created with JetBrains PhpStorm.
 * User: oleg
 * Date: 18.07.12
 * Time: 12:15
 * To change this template use File | Settings | File Templates.
 */
document.showWfInfo = function (id){
    id = id.substr(1);
    $('#popupInfo .modal-body').html("<p>Идет запрос данных...</p>");
    $('#popupInfo').modal('show');
    $.getJSON("/admin/logging/workflowStates/getInfo/id/"+id)
        .done(function(data){
            //console.log(data);

            textHtml = "";
            textHtml = textHtml + "<p>Тип объекта:"+data.class+"</p>";
            textHtml = textHtml + "<p>ID:"+data.objectId+"</p>";
            textHtml = textHtml + "<p>Таблица состояний:</p>";
            textHtml = textHtml + "<table class='table table-bordered'><thead><tr><th>Состояние</th><th>Время</th></tr></thead><tbody>";
            for(var i in data.stages){
                textHtml = textHtml + "<tr class='"+((i % 2 == 0) ? 'odd' : '')+"'><td>"+data.stages[i].stageName+"</td><td>"+data.stages[i].time+"</td></tr>";
                if(data.stages[i].requestIds.length > 0){
                    textHtml = textHtml + "<tr class='"+((i % 2 == 0) ? 'odd' : '')+"'><td colspan='2'><div class='logRequests'>";
                    for(var j in data.stages[i].requestIds){
                        textHtml = textHtml + "<div class='logRequest'><div class='logRequestDescription' onclick='showWfRequestInfo(this,\""+data.stages[i].requestIds[j].class+"\",\""+data.stages[i].requestIds[j].keyName+"\",\""+data.stages[i].requestIds[j].key+"\")'><div class='symbol btn'>+</div>метод:"+data.stages[i].requestIds[j].methodName+" Описание:"+data.stages[i].requestIds[j].description+"</div><div class='logRequestFullInfo'></div></div>";
                    }
                    textHtml = textHtml + "</div></td></tr>";
                }
            }
            textHtml = textHtml + "</tbody></table>";
            $('#popupInfo .modal-body').html(textHtml);
            //btn.button("reset");
            //var two = data.priceTo + data.priceBack;
            //btn.append("&nbsp; <b>Цена: </b>"+Math.min(data.priceTo + data.priceBack, data.priceToBack) + " руб. (2 билета = " + two + " руб., туда-обратно = " + data.priceToBack + " руб.)");
        })
        .fail(function(data){
            $('#popupInfo .modal-body').html("Ошибка сервера");
            //btn.button("reset");
            //btn.html("Произошёл сбой");
            //e.preventDefault();
        });
};
document.showWfRequestInfo = function (obj,className,keyName,key){
    var jObj = $(obj);
    var oLength = jObj.parent().find('.logRequestFullInfo').html().length;
    jObj.parent().find('.symbol').html('-');
    if(oLength <=1){
        jObj.parent().find('.logRequestFullInfo').html("<p>Идет запрос данных...</p>");
        $.getJSON("/admin/logging/workflowStates/getRequestInfo/className/"+className+"/keyName/"+keyName+"/key/"+key)
            .done(function(data){
                console.log(data);
                textHtml = "";
                textHtml = textHtml + "<p>Метод:"+data.methodName+"</p>";
                textHtml = textHtml + "<p>Url запроса:"+data.requestUrl+"</p>";
                textHtml = textHtml + "<p>Время отправки запроса:"+data.timestamp+"</p>";
                textHtml = textHtml + "<p>XML запроса:</p>";
                textHtml = textHtml + data.requestXml;
                textHtml = textHtml + "<p>Время ожидания ответа:"+data.executionTime+" сек</p>";
                if(!jQuery.isEmptyObject(data.errorDescription)){
                    textHtml = textHtml + "<p>Описание ошибки:"+data.errorDescription+"</p>";
                }
                if(!jQuery.isEmptyObject(data.responseXml)){
                    textHtml = textHtml + "<p>XML ответа:</p>";
                    textHtml = textHtml + data.responseXml;
                }
                jObj.parent().find('.logRequestFullInfo').html(textHtml);
                //$('#popupInfo .modal-body').html(textHtml);
                //btn.button("reset");
                //var two = data.priceTo + data.priceBack;
                //btn.append("&nbsp; <b>Цена: </b>"+Math.min(data.priceTo + data.priceBack, data.priceToBack) + " руб. (2 билета = " + two + " руб., туда-обратно = " + data.priceToBack + " руб.)");
            })
            .fail(function(data){
                jObj.parent().find('.logRequestFullInfo').html("Ошибка сервера");
                //btn.button("reset");
                //btn.html("Произошёл сбой");
                //e.preventDefault();
            });
    }else{
        jObj.parent().find('.logRequestFullInfo').html('');
        jObj.parent().find('.symbol').html('+');
    }
};