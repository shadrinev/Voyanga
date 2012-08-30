/**
 * Created with JetBrains PhpStorm.
 * User: oleg
 * Date: 28.08.12
 * Time: 11:13
 * To change this template use File | Settings | File Templates.
 */
Date.fromIso = function (dateIsoString){
    var initArray = dateIsoString.split('-');
    return new Date(initArray[0],(initArray[1]-1),initArray[2]);
}