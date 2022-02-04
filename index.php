<?php

require 'vendor/autoload.php';



use Symfony\Component\DomCrawler\Crawler;


   $url= "https://www.audi.de/gebrauchtwagenboerse/ergebnisse.html";
   $url2= "https://www.autoscout24.de/lst/audi?sort=standard&amp;desc=0&amp;ustate=N%2CU&amp;cy=D&amp;atype=C%22";


   // Getting the content of the two URLs
   $html = file_get_contents($url);
   $html2 = file_get_contents($url2);


   // Crawling the URLs using symfony dom crawler component
   $crawler = new Crawler($html);
   $crawler2=  new Crawler($html2);

   //Crawling the first URL
   $crawler->filter('ul li')->each(function (Crawler $li) use(&$cars) {
 $cars=[];
       //Connection with the database
       $dbServername="localhost";
       $dbUsername="root";
       $dbPassword="";
       $dbName="cars";
       $con =mysqli_connect($dbServername,$dbUsername,$dbPassword,$dbName);

       //Retrieving the infomation of the cars using the css selector component

        $car_title =$li->filter('h3[class=audi-headline-order-3]')->text('');
        $car_price = $li->filter('p.sc-car-tile-price-numbers audi-copy-m')->text('');
        $fuel = $li->filter('p.sc-car-tile-info audi-copy-s')->text('');
        $registration_date = $li->filter('span.sc-car-tile-info audi-copy-s')->text('');
        $mileage=$li->filter('span[class=sc-car-tile-info audi-copy-s]')->text('');
        $power=$li->filter('span[class=sc-car-tile-info audi-copy-s]')->text('');
        $fuel_consumption=$li->filter('p[class=sc-pbxSd kwbOGv]')->text('');
        $co2_emission =$li ->filter('p[class=sc-pbxSd kwbOGv]')->text('');
       $transmission_type=$li->filter('span.sc-car-tile-info audi-copy-s')->text('');
        $image =$li->filter('img[class="sc-j-swipe-gallery-slide-item-image"]')->attr('src');

       //adjusting the registration date from the scraper to meet the requirments of a date
       $time="01/". $registration_date;

       //Converting the registration date  to date
       $date =strtotime($time);

       //fixing the date to compare with
       $old_date ="01/01/2012";

       //converting the fixing date to a date type in order to compare it with the registration date
       $new_date = strtotime($old_date);

       //fisxing the statement to be compared to the co2 emmision
       $str2 = "0 g/km (komb.)2";
       $str3 ="- (g/km)";


      //Adding the information to the database following the conditions
       //car needs to have a value for the emission
       //car registration must be 2012 or newer

        if (($date>$new_date) && (strcmp($co2_emission, $str2)!= 0) && (strcmp($co2_emission, $str3)!= 0)){
           $sql="INSERT INTO car (title,price,fuel,registration_date,mileage,power,fuel_consumption,co2_emission,transmission_type,image)
     VALUES ('$car_title','$car_price','$fuel','$time','$mileage','$power', '$fuel_consumption','$co2_emission','$transmission_type','$image');";
           $result=mysqli_query($con,$sql);}



});
//Crawling the seconde URL
$crawler2->filter('article')->each(function (Crawler $li) use(&$cars){

    //making the database connection
    $dbServername="localhost";
    $dbUsername="root";
    $dbPassword="";
    $dbName="cars";
    $con =mysqli_connect($dbServername,$dbUsername,$dbPassword,$dbName);


        //Retrieving the infomation of the cars using the css selector component
        $car_title = $li->filter('h2')->text('');
        $car_price = $li->filter('span.css-113e8xo')->text('');
        $fuel = $li->filter('span[type=fuel-category]')->text('');
        $registration_date = $li->filter('span[type=registration-date] ')->text('');
        $mileage=$li->filter('span[type=mileage]')->text('');
        $power=$li->filter('span[type=power]')->text('');
        $fuel_consumption=$li->filter('span[type=fuel-consumption]')->text('');
        $co2_emission =$li ->filter('span[type=co2-emission]')->text('');
        $transmission_type=$li->filter('span[type=transmission-type]')->text('');
        $image =$li->filter('img')->attr('src');

        //adjusting the registration date from the scraper to meet the requirments of a date
        $time="01/". $registration_date;

        //Converting the registration date  to date
        $date =strtotime($time);

        //fixing the date to compare with
        $old_date ="01/01/2012";

        //converting the fixing date to a date type in order to compare it with the registration date
        $new_date = strtotime($old_date);

        //fisxing the statement to be compared to the co2 emmision
        $str2 = "0 g/km (komb.)2";
        $str3 ="- (g/km)";


    //Adding the information to the database following the conditions
    //car needs to have a value for the emission
    //car registration must be 2012 or newer

    if (($date>$new_date) && (strcmp($co2_emission, $str2)!= 0) && (strcmp($co2_emission, $str3)!= 0)){
        $sql="INSERT INTO car (title,price,fuel,registration_date,mileage,power,fuel_consumption,co2_emission,transmission_type,image)
     VALUES ('$car_title','$car_price','$fuel','$time','$mileage','$power', '$fuel_consumption','$co2_emission','$transmission_type','$image');";
     $result=mysqli_query($con,$sql);}
    
});









