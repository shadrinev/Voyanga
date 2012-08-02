<?php
class FlightAdultPassportForm extends BaseFlightPassportForm
{
    /**
     * @return FlightAdultPassportForm
     */
    public static function fillWithRandomData()
    {
        $lastNames = array('Ivanov','Petrov','Kovalev','Mihailov','Romanov','Matveev','Borisov','Sviridov','Fedorov','Nikitin','Grigoriev','Vasilev');
        $firstNames = array('Evgeniy','Kirill','Oleg','Mihail','Dmitriy','Roman','Denis','Artem','Danila','Viktor','Nikolay','Aleksey','Ruslan');

        $adult1 = new FlightAdultPassportForm();
        $adult1->genderId = FlightAdultPassportForm::GENDER_MALE;
        $randKey = rand(0,(count($firstNames) - 1));
        $randomFirstName = $firstNames[$randKey];
        $randKey = rand(0,(count($lastNames) - 1));
        $randomLastName = $lastNames[$randKey];
        $randomBirthDay = new DateTime('1971-01-01');
        $randKey = rand(0,365*20);
        $interval = new DateInterval('P'.$randKey.'D');
        $randomBirthDay->add($interval);
        $adult1->firstName = $randomFirstName;
        $adult1->lastName = $randomLastName;
        $adult1->birthday = $randomBirthDay->format('d.m.Y');
        $randKey = rand(1000,7000);
        $adult1->series = (string)$randKey;
        $randKey = rand(100000,999999);
        $adult1->number = (string)$randKey;
        $adult1->countryId = 174;
        return $adult1;
    }
}