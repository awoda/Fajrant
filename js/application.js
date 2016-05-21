// This is a manifest file that'll be compiled into application.js, which will include all the files
// listed below.
//
// Any JavaScript/Coffee file within this directory, lib/assets/javascripts, vendor/assets/javascripts,
// or any plugin's vendor/assets/javascripts directory can be referenced here using a relative path.
//
// It's not advisable to add code directly here, but if you do, it'll appear at the bottom of the
// compiled file.
//
// Read Sprockets README (https://github.com/rails/sprockets#sprockets-directives) for details
// about supported directives.
//
//= require jquery
//= require jquery_ujs
//= require turbolinks
//= requi

/*zmienne wykorzystywane

 holidays -  liczba swiat
 workdays - liczba dni pracujacych w miesiacu
 swieta[] - tablica w ktorej przetrzymywane sa numery dni swiatecznych
 dni_w_miesiacu - ilosc dni w miesiacu
 dzienmiesiaca[] - tablica w ktorych przetrzymywane sa dni z ich wytycznymi


 dzienmiesiaca[][0] - dzien miesiaca
 dzienmiesiaca[][1] - rozpoczecie pracy
 dzienmiesiaca[][2] - zakonczenie pracy
 dzienmiesiaca[][3] - identyfikator dnia // 0- pracujacy 1- swieto 2-niedziela 3-sobota
 dzienmiesiaca[][4] - nadgodziny // 0 - normalne 1 - nadgodziny
 dzienmiesiaca[][5] - czas pracy w minutach
 dzienmiesiaca[][6] - dzien tygodnia  // 1 - poniedzialek, 2 - wtorek ...

 nadgodziny_w_miesiacu - ilosc nadgodzin nadgodzin
 czas_pracy_w_miesiacu - ilosc godzin wprowadzonych
 godzin_w_etacie - ilosc godzin do przepracowania dla zadanego etatu

 etat[] - wymiar etatu // 0-licznik 1-mianownik
 system_pracy - typ systemu pracy // 1-podstawowy 2-rownowazny 3-weekendowy 4-ciagly 5-umowa cywilnoprawna
 */


//zmienne globalne
var holidays,
    workdays,
    nadgodzin_w_miesiacu,
    czas_pracy_w_miesiacu,
    godzin_w_etacie,
    system,
    dni_w_miesiacu;

var swieta = new Array();
var dzienmiesiaca = new Array();
var etat = new Array();
var tydzien = new Array();
var verify = "";



function test(){

    holidays =  $('.holiday').size()/2;
    workdays = $('.workday').size()/2;
    workdays = workdays - holidays;

    var counter = 0;
    $(".holiday").each(function(){  //zliczenie dni swiatecznych
        if($(this).hasClass("clicktoset")){
            swieta[counter] = $(this).attr('id');
            counter++;
        }
    });
    $("#swieta").text("Dni świąteczne: "+swieta);

    counter = 1;
    $(".clicktoset").each(function() {

        dzienmiesiaca[counter] = new Array();

        var time = $(this).attr("value");
        time = time.split("-");

        dzienmiesiaca[counter][0] = $(this).attr("id");
        if($(this).hasClass("inwork")){

            dzienmiesiaca[counter][1]= time[0];
            dzienmiesiaca[counter][2]= time[1];

            if($(this).attr("absence")==4){
                dzienmiesiaca[counter][4]= 1;
            }
            else{
                dzienmiesiaca[counter][4]= 0;
            }
        }

        if($(this).hasClass("sunday")){
            dzienmiesiaca[counter][3] = "niedziela";
        }
        else if($(this).hasClass("holiday")){
            dzienmiesiaca[counter][3] = "swieto";
        }
        else if($(this).hasClass("saturday")){
            dzienmiesiaca[counter][3] = "sobota";
        }
        else{
            dzienmiesiaca[counter][3] = "roboczy";
        }
        counter ++;
    });


    //wprowadzenie do tablicy dzienmiesiaca informacji o dniu tygodnia
    for(var x = 1; x<dzienmiesiaca.length; x++){

        if(dzienmiesiaca[x][3] == "niedziela"){
            dzienmiesiaca[x][6] = 7;

            if(x<=7){ //jesli sprawdzam pierwszy tydzien to musze wypelnic dni w tył

                var zmiennaStop = x;
                for(var y = 1; y<zmiennaStop; y++){

                    switch(y){

                        case 1:
                            dzienmiesiaca[x-y][6] = 6;
                            break;
                        case 2:
                            dzienmiesiaca[x-y][6] = 5;
                            break;
                        case 3:
                            dzienmiesiaca[x-y][6] = 4;
                            break;
                        case 4:
                            dzienmiesiaca[x-y][6] = 3;
                            break;
                        case 5:
                            dzienmiesiaca[x-y][6] = 2;
                            break;
                        case 6:
                            dzienmiesiaca[x-y][6] = 1;
                            break;
                    }
                }
            }
            var zmiennaStop = 7;
            for(var y = 1; y<zmiennaStop; y++){
                if (typeof dzienmiesiaca[x+y] !== 'undefined') {

                    switch(y){

                        case 1:
                            dzienmiesiaca[x+y][6] = 1;
                            break;
                        case 2:
                            dzienmiesiaca[x+y][6] = 2;
                            break;
                        case 3:
                            dzienmiesiaca[x+y][6] = 3;
                            break;
                        case 4:
                            dzienmiesiaca[x+y][6] = 4;
                            break;
                        case 5:
                            dzienmiesiaca[x+y][6] = 5;
                            break;
                        case 6:
                            dzienmiesiaca[x+y][6] = 6;
                            break;
                    }
                }
            }
        }
    }




    //wyliczenie ile godzin w których uzytkownik jest w pracy
    dni_w_miesiacu = dzienmiesiaca.length-1;
    czas_pracy_w_miesiacu = 0;
    nadgodzin_w_miesiacu = 0;
    for(var x=1; x<=dni_w_miesiacu; x++){

        if(dzienmiesiaca[x][1]!=null || dzienmiesiaca[x][2]!=null){
            var czasrozpoczecia = dzienmiesiaca[x][1].split(":");
            var czaszakonczenia = dzienmiesiaca[x][2].split(":");

            var godzinarozpoczecia = czasrozpoczecia[0];
            var minutarozpoczecia = czasrozpoczecia[1];
            var godzinazakonczenia = czaszakonczenia[0];
            var minutazakonczenia = czaszakonczenia[1];

            var minutyZakonczenia = (godzinazakonczenia*60 + parseInt(minutazakonczenia));
            var minutyRozpoczecia = (godzinarozpoczecia*60 + parseInt(minutarozpoczecia));

            var czaspracy =  minutyZakonczenia - minutyRozpoczecia;

            if(minutyZakonczenia<minutyRozpoczecia){  //warunek jesli dniowka przechodzi w nastepny dzien
                czaspracy = czaspracy + 1440;
            }

            dzienmiesiaca[x][5] = czaspracy;

            var czaspracyGodziny = czaspracy/60;

            if(dzienmiesiaca[x][4]==1){
                nadgodzin_w_miesiacu += czaspracy;
            }
            else{
                czas_pracy_w_miesiacu += czaspracy;
            }
        }
    }

    //tutaj zamieniam czas ktory byl w stringach dzienmiesiaca[][1,2] na minuty jako int
    for(var x=1; x<dzienmiesiaca.length; x++){
        if(dzienmiesiaca[x][1] != null && dzienmiesiaca[x][2] != null){
            var temp1 = dzienmiesiaca[x][1].split(":");
            var temp2 = dzienmiesiaca[x][2].split(":");
            dzienmiesiaca[x][1] = parseInt(temp1[0]*60) + parseInt(temp1[1]);
            dzienmiesiaca[x][2] = parseInt(temp2[0]*60) + parseInt(temp2[1]);
        }
    }



    //wyliczenie ile godzin musi wypracowac uzytkownik aby bylo zgodnie z etatem
    etat =  $("#etat").attr("value");
    system = parseInt($("#umowa").attr("value"));

    utworzTydzien();

    etat = etat.split("/");
    etat[0] = parseInt(etat[0]);
    etat[1] = parseInt(etat[1]);

    godzin_w_etacie = workdays*8*etat[0]/etat[1];

    switch(system) {
        case 1:
            sprawdzPodstawowy();
            break;
        case 2:
            sprawdzRownowazny();
            break;
        case 3:
            sprawdzWeekendowy();
            break;
        case 4:
            sprawdzCiagly();
            break;
        case 5:
            sprawdzInny();
            break;
    }

    verify = "";

    if(holidays == 0){
        $("#swieta").hide();
    }
    $("#liczbaswiat").text("Liczba dni świątecznych: " + holidays);
    $("#liczbadni").text("Liczba dni roboczych: " + workdays);
    $("#liczbagodzin").text("Liczba godzin dla pracownika: "+ godzin_w_etacie);
    $("#ilegodzin").text("Obecnie przypisano: "+ parseInt(czas_pracy_w_miesiacu/60) + ":"+ ("0" + czas_pracy_w_miesiacu%60).slice(-2) + " godzin pracy");

}
function sprawdzPodstawowy(){
    //alert("podstawowy");
    //cleanOutput();

    /*
     System podstawowy:
     - (ok) Nie więcej niż 8h na dobę
     - (ok) Nie więcej niż 40h na tydzień na pelny etat
     - (ok) Nie więcej niż 48h z nadgodzinami na tydzień niezaleznie od etatu
     - (ok) Dzienny odpoczynek minimum 11h
     - (ok) Raz na tydzień ciągły odpoczynek minimum 35h – ewentualnie 24h jeśli przechodzi na wcześniejszą zmianę   (ok)
     - (ok) Za przepracowany dzien w niedziele/swieto nalezy sie dzien wolny w dniu roboczym(w ciagu 6 dni przed/po  swieto - w ciagu okresu rozliczeniowego)
     */

    //-----------------------------------------------------------------------------------------------
    //-------------------------sprawdzam czy któraś dniówka ma wiecej niz 8h-------------------------
    //-----------------------------------------------------------------------------------------------
    {
        var osiemgodzin = 8*60;
        var flaga = true;
        var text = "<span style='color:orange'>UWAGA! W dniach ";
        for(var x=1; x<dzienmiesiaca.length; x++){
            if(dzienmiesiaca[x][5]>osiemgodzin){
                text += x + ", ";
                flaga = false;
            }
        }
        text = text.slice(0, -2);
        text += " miesiaca przekroczono czas pracy dla etatu!</span>";

        if(!flaga){
            //console.log(text);
            verify += text + "<br>";
        }
        else{
            //console.log("Dlugość pracy w poszczególne dni zgodna z kodeksem!")
        }
    }
    //-----------------------------------------------------------------------------------------------
    //--sprawdzam czy w tygodniu nie przekroczona maksymalna ilosc godzin i nadgodzin (40h oraz 8h)--
    //-----------------------------------------------------------------------------------------------
    {
        verify += "<br>";

        var czterdziescigodzin = 40 * 60;
        var osiemnadgodzin = 8 * 60;
        var flagaOgolna = true;
        var flagaNadgodzin = true;

        var wymiar_etatu = etat[0]/etat[1];

        for (var y = 0; y < tydzien.length; y++) {  //petla odliczajaca poszczegolne tygodnie

            var sumagodzin = 0;
            var sumanadgodzin = 0;
            for (var z = 0; z < tydzien[y].length; z++) { //petla odliczajaca dni tygodnia

                if(tydzien[y][z][4]==1 || tydzien[y][z][4]==0){
                    sumagodzin += tydzien[y][z][5]; //zliczenie wszystkich godzin na grafiku
                }

            }

            if ((sumagodzin) > (osiemnadgodzin + czterdziescigodzin)) {
                text = "<span style='color:red'>UWAGA! Ilość wprowadzonych godzin(wraz z nadgodzinami) w tygodniu " + parseInt(y + 1) + " przekracza ustawowe normy! Zmniejsz ilość godzin! (" + (sumagodzin) / 60 + ")</span>";
                //console.log(text);
                verify += text + "<br>";
                flagaNadgodzin = false
            }
            else if (sumagodzin > (czterdziescigodzin*wymiar_etatu)) {
                text = "<span style='color:orange'>UWAGA! Ilość wprowadzonych godzin w tygodniu " + parseInt(y + 1) + " jest za duża dla etatu pracownika! (" + sumagodzin / 60 + ")</span>"
                //console.log(text);
                verify += text + "<br>";
                flagaOgolna = false;
            }
        }
        if (flagaOgolna) {
            //console.log("Ilość wprowadzonych godzin w ujęciu tygodniowym zgodna z kodeksem pracy! Nie wprowadzono nadgodzin")
        }
        else if(!flagaOgolna && flagaNadgodzin){
            //console.log("Przekroczono wymiar etatu dla pracownika! Nie wprowadzono nadgodzin")
        }
        else if (!flagaOgolna && !flagaNadgodzin) {
            //console.log("Przekroczono maksymalny wymiar pracy wraz z liczbą nadgodzin")
        }
    }
    //-----------------------------------------------------------------------------------------------
    //---------------------sprawdzam czy zachowano minimalny dobowy odpoczynek (11h)-----------------
    //-----------------------------------------------------------------------------------------------
    {
        verify += "<br>";

        var odpoczynekFlaga = true;
        
        
        var odpoczynekString = "<span style='color:red '>UWAGA! Odpoczynek dobowy pomiędzy dniami: ";
        for(var x=1; x<dzienmiesiaca.length-1; x++){  //-1 poniewaz dzienny ostatni dzien to koniec okresu rozliczeniowego

            /*
             dzienmiesiaca[][0] - dzien miesiaca
             dzienmiesiaca[][1] - rozpoczecie pracy w minutach
             dzienmiesiaca[][2] - zakonczenie pracy w minutach
             dzienmiesiaca[][3] - identyfikator dnia // 0- pracujacy 1- swieto 2-niedziela 3-sobota
             dzienmiesiaca[][4] - nadgodziny // 0 - normalne 1 - nadgodziny
             dzienmiesiaca[][5] - czas pracy w minutach
             */


            var czas;

            if(dzienmiesiaca[x][1] == null || dzienmiesiaca[x][2] == null){
                //jesli nie pracuje w tym dniu to nic nie rob
                czas=24*60;
            }
            else{
                if(dzienmiesiaca[x+1][1] == null || dzienmiesiaca[x+1][2] == null){
                    //jesli w nastepnym dniu jest wolne to pomijam
                    czas = 24*60;
                }
                else{

                    //console.log(dzienmiesiaca[x][1] + " , " + dzienmiesiaca[x][2]);
                    if(dzienmiesiaca[x][1]>dzienmiesiaca[x][2]){
                        //jesli dniowka przechodzi na nastepny dzien
                        //alert("dniowka przechodzi")
                        czas = dzienmiesiaca[x+1][1] - dzienmiesiaca[x][2];
                        //alert(czas);
                    }
                    else{
                        //jesli praca odbywa sie w obrebie jednego dnia kalendarzowego
                        //alert("dniowka normalna");

                        czas = 24*60 - dzienmiesiaca[x][2];
                        czas += dzienmiesiaca[x+1][1];
                        //alert(czas);

                    }
                }

                if(czas<(11*60)){
                    //jesli odpoczynek dobowy mniejszy od 11h

                    odpoczynekString += x + " a " + parseInt(x+1) + ", ";
                    odpoczynekFlaga = false;
                }
            }
        }
        odpoczynekString = odpoczynekString.slice(0, -2);
        odpoczynekString += " nie został zachowany!</span>";

        if(!odpoczynekFlaga){
            //console.log(odpoczynekString);
            verify += odpoczynekString + "<br>";
        }

    }
    //-----------------------------------------------------------------------------------------------
    //--------sprawdzam czy zachowano minimalny ciagły odpoczynek tygodniowy (35h lub 24h)-----------
    //-----------------------------------------------------------------------------------------------
    {
        for(var x=0; x<tydzien.length; x++) {

            var flagaOdpoczynekTygodniowy = 0; //0 - nok  1 - >=24h  2 - >=36h
            var czasMax = 0;

            if(tydzien[x].length == 7 ){
                //jeśli tydzien nie jest pełny to pomijam

                console.log(x);
                for (var y = 0; y < tydzien[x].length; y++) {

                    /*
                     będzie trzeba sprawdzic wszystkie 3 dni pod rzad, bo 36h moze byc rozbite na 3 dni kalendarzowe:
                     - pierwszy dzien nalezy sprawdzic, ile czasu wolnego po pracy i czy w pracy
                     - drugi ile czasu przed praca, lub czy wolny
                     - trzeci, jesli jeszcze nie spelniono warunku >36h oraz drugi dzien wolny, sprawdzenie pracy przed lub dnia wolnego
                     */



                    //------------------------//
                    //sprawdzenie 1szego dnia //
                    //------------------------//

                    if (tydzien[x][y][1] == null || tydzien[x][y][2] == null) {
                        //jesli w nastepnym dniu jest wolne to pomijam
                        czas = 24 * 60;
                    }
                    else {

                        if (tydzien[x][y][1] > tydzien[x][y][2]) {
                            //jesli dniowka przechodzi na nastepny dzien
                            czas = -(tydzien[x][y][2]);  // czas jest ujemny
                        }
                        else {
                            //jesli praca odbywa sie w obrebie jednego dnia kalendarzowego
                            czas = 24 * 60 - tydzien[x][y][2];
                        }
                    }

                    //------------------------//
                    //sprawdzenie 2giego dnia //
                    //------------------------//

                    if (typeof tydzien[x][y + 1] !== 'undefined') {

                        if (tydzien[x][y + 1][1] == null || tydzien[x][y + 1][2] == null) {
                            //jesli w nastepnym dniu jest wolne to pomijam
                            czas += 24 * 60;
                            //console.log("drugi wolny - " + czas/60);
                        }
                        else {
                            //nie sprawdzam czy dniowka przechodzi, bo jesli jest tu jakis dzien to nie sprawdzam nastepnego dnia a tylko czas od polnocy do rozpoczecia pracy
                            czas += tydzien[x][y + 1][1];
                        }
                    }

                    //---------------------//
                    //sprawdzenie 3go dnia //
                    //---------------------//

                    if(czas<36){ // sprawdzamy 3ci dzien tylko gdy jeszcze nie przekroczono wymaganego czasu

                        if (typeof tydzien[x][y + 2] !== 'undefined') {

                            if (tydzien[x][y + 2][1] == null || tydzien[x][y + 2][2] == null) {
                                //jesli w nastepnym dniu jest wolne to pomijam
                                czas += 24 * 60;
                            }
                            else {
                                //nie sprawdzam czy dniowka przechodzi, bo jesli jest tu jakis dzien to nie sprawdzam nastepnego dnia a tylko czas od polnocy do rozpoczecia pracy
                                czas += tydzien[x][y + 2][1];
                            }
                        }
                    }

                    //-------------------------------//
                    //podsumowanie tygodnia i wyniki //
                    //-------------------------------//

                    //console.log(czas/60);
                    if(czas > czasMax){
                        czasMax = czas;
                        if (czasMax >= 24 * 60) {

                            flagaOdpoczynekTygodniowy = 1;
                        }
                        if (czasMax >= (36 * 60)) {
                            flagaOdpoczynekTygodniowy = 2;
                        }
                    }
                }
                switch (flagaOdpoczynekTygodniowy) {
                    case 0:
                        text="<span style='color:red'>UWAGA! W tygodniu " + (x + 1) + " odpoczynek tygodniowy nie został zachowany! (<24h)</span>";
                        verify += text + "<br><br>";
                        break;
                    case 1:
                        text = "<span style='color:orange'>UWAGA! W tygodniu " + (x + 1) + " odpoczynek tygodniowy został zmniejszony (24-36h).";
                        verify += text + "<br><br>";
                        break;
                    case 2:

                }
            }
            else{
                console.log("Tydzien " + (x+1) + " jest za krótki, nie obliczam odpoczynku tygodniowego");
            }


        }
    }
    //-----------------------------------------------------------------------------------------------
    //--------sprawdzam czy liczba dni wolnych od pracy = liczbie sobot, niedziel i swiąt------------
    //-----------------------------------------------------------------------------------------------
    {

        //workdays = liczba dni roboczych

        var licznikDniWolnych = 0;
        for(var x = 1 ; x<dzienmiesiaca.length ; x++){
            if(typeof dzienmiesiaca[x][5] == "undefined"){
                licznikDniWolnych ++ ;
            }
        }

        var liczbaDniWolnch = parseInt(dzienmiesiaca.length - workdays - 1);
        if(licznikDniWolnych >= liczbaDniWolnch){
            console.log("Liczba dni wolnych odpowiada liczbie sobot, niedziel i swiat");
        }
        else{
            verify += "<span style='color:red'>UWAGA! Liczba wolnych dni od pracy jest mniejsza od liczby wymaganych dni wolnych w miesiącu (soboty, niedziele, święta)</span>";
            verify += "<br>";
        }
    }

    $("#verify").html(verify);

}
function sprawdzRownowazny(){
    //alert("rownowazny");

    /*
     System równoważny:
     - (ok) Na dobę maksymalnie 24h
     - (ok) Przy dniówkach większych niż 12h odpoczynek dobowy nie może być krótszy od dniówki, jesli mniejsza to 11h
     - (ok) Łączna liczba dni wolnych od pracy >= Łączna liczba niedziel, świąt i dni wolnych.
     - (ok) Co najmniej raz na 4 tygodnie 1 wolna niedziela (czyli 1 wolna niedziela na miesiac)
     - (ok) za niedziele wolne do oddania w ciągu 6 dni w przód lub tył (ewentualnie do końca miesiąca jeśli nie ma innej możliwości)
     - (ok) za pracę w święto dzień wolny w ciągu okresu rozliczeniowego   (constant ok, wynika z punktu 3.)
     - (ok) odpoczynek tygodniowy 35h lub 24h
     */

    //-----------------------------------------------------------------------------------------------
    //-------------------------sprawdzam czy któraś dniówka ma wiecej niz 24h------------------------
    //-----------------------------------------------------------------------------------------------
    {
        var dwadziesciaczterygodziny = 24*60;
        var flaga = true;
        var text = "<span style='color:RED'>UWAGA! W dniach ";
        for(var x=1; x<dzienmiesiaca.length; x++){
            if(dzienmiesiaca[x][5]>dwadziesciaczterygodziny){
                text += x + ", ";
                flaga = false;
            }
        }
        text = text.slice(0, -2);
        text += " miesiaca przekroczono maksymalny czas pracy (24h)!</span><br>";

        if(!flaga){
            //console.log(text);
            verify += text;
            verify += "<br>";
        }
        else{
            //console.log("Dlugość pracy w poszczególne dni zgodna z kodeksem!")
        }
    }
    //-----------------------------------------------------------------------------------------------
    //---------------------------sprawdzam czy odpoczynek dobowy sie zgadza--------------------------
    //-----------------------------------------------------------------------------------------------
    {
        var odpoczynekFlaga = true;
        var odpoczynekString = "<span style='color:red'>UWAGA! Odpoczynek dobowy pomiędzy dniami: ";
        for(var x=1; x<dzienmiesiaca.length-1; x++){  //-1 poniewaz dzienny ostatni dzien to koniec okresu rozliczeniowego

            /*
             dzienmiesiaca[][0] - dzien miesiaca
             dzienmiesiaca[][1] - rozpoczecie pracy w minutach
             dzienmiesiaca[][2] - zakonczenie pracy w minutach
             dzienmiesiaca[][3] - identyfikator dnia // 0- pracujacy 1- swieto 2-niedziela 3-sobota
             dzienmiesiaca[][4] - nadgodziny // 0 - normalne 1 - nadgodziny
             dzienmiesiaca[][5] - czas pracy w minutach
             */

            var czas;

            if(dzienmiesiaca[x][1] == null || dzienmiesiaca[x][2] == null){
                //jesli nie pracuje w tym dniu to nic nie rob
                czas=24*60;
            }
            else{
                if(dzienmiesiaca[x+1][1] == null || dzienmiesiaca[x+1][2] == null){
                    //jesli w nastepnym dniu jest wolne to pomijam
                    czas = 24*60;
                }
                else{

                    //console.log(dzienmiesiaca[x][1] + " , " + dzienmiesiaca[x][2]);
                    if(dzienmiesiaca[x][1]>dzienmiesiaca[x][2]){
                        //jesli dniowka przechodzi na nastepny dzien
                        //alert("dniowka przechodzi")
                        czas = dzienmiesiaca[x+1][1] - dzienmiesiaca[x][2];
                        //alert(czas);
                    }
                    else{
                        //jesli praca odbywa sie w obrebie jednego dnia kalendarzowego
                        //alert("dniowka normalna");

                        czas = 24*60 - dzienmiesiaca[x][2];
                        czas += dzienmiesiaca[x+1][1];
                        //alert(czas);

                    }
                }

                var czasOdpoczynku = 0;
                if(dzienmiesiaca[x][5]>(12*60)){
                    czasOdpoczynku = dzienmiesiaca[x][5];
                }
                else{
                    czasOdpoczynku = 11*60;
                }

                if(czas<czasOdpoczynku){
                    //jesli odpoczynek dobowy mniejszy od...
                    odpoczynekString += x + " a " + parseInt(x+1) + ", ";
                    odpoczynekFlaga = false;
                }
            }
        }
        odpoczynekString = odpoczynekString.slice(0, -2);
        odpoczynekString += " nie został zachowany! </span><br>";

        if(!odpoczynekFlaga){
            console.log(odpoczynekString);
            verify += odpoczynekString;
            verify += "<br>";
        }

    }
    //-----------------------------------------------------------------------------------------------
    //--------------------------sprawdzam czy jest najmniej 1 wolna niedziela------------------------
    //-----------------------------------------------------------------------------------------------
    {
        var counter = 0;   //licznik wszystkich
        var counter2 = 0;  //licznik wolnych niedziel

        for(var x=1; x<dzienmiesiaca.length; x++){

            if(dzienmiesiaca[x][3]=="niedziela"){
                counter++;

                if(dzienmiesiaca[x][1] == null && dzienmiesiaca[x][2] == null){
                    counter2++;
                }
            }
        }

        //console.log("wszystkie niedziele: " + counter + " wolne niedziele: " + counter2)

        if(counter2 < 1){

            console.log("<span style='color:red'>UWAGA! Brakuje jednej wolnej niedzieli!</span>");
            verify += "<span style='color:red'>UWAGA! Brakuje jednej wolnej niedzieli!</span><br>";
            verify += "<br>";

        }
    }
    //-----------------------------------------------------------------------------------------------
    //--------sprawdzam czy zachowano minimalny ciagły odpoczynek tygodniowy (35h lub 24h)-----------
    //-----------------------------------------------------------------------------------------------
    {
        for(var x=0; x<tydzien.length; x++) {

            var flagaOdpoczynekTygodniowy = 0; //0 - nok  1 - >=24h  2 - >=36h
            var czasMax = 0;

            if(tydzien[x].length == 7 ){
                //jeśli tydzien nie jest pełny to pomijam

                console.log(x);
                for (var y = 0; y < tydzien[x].length; y++) {

                    /*
                     będzie trzeba sprawdzic wszystkie 3 dni pod rzad, bo 36h moze byc rozbite na 3 dni kalendarzowe:
                     - pierwszy dzien nalezy sprawdzic, ile czasu wolnego po pracy i czy w pracy
                     - drugi ile czasu przed praca, lub czy wolny
                     - trzeci, jesli jeszcze nie spelniono warunku >36h oraz drugi dzien wolny, sprawdzenie pracy przed lub dnia wolnego
                     */



                    //------------------------//
                    //sprawdzenie 1szego dnia //
                    //------------------------//

                    if (tydzien[x][y][1] == null || tydzien[x][y][2] == null) {
                        //jesli w nastepnym dniu jest wolne to pomijam
                        czas = 24 * 60;
                    }
                    else {

                        if (tydzien[x][y][1] > tydzien[x][y][2]) {
                            //jesli dniowka przechodzi na nastepny dzien
                            czas = -(tydzien[x][y][2]);  // czas jest ujemny
                        }
                        else {
                            //jesli praca odbywa sie w obrebie jednego dnia kalendarzowego
                            czas = 24 * 60 - tydzien[x][y][2];
                        }
                    }

                    //------------------------//
                    //sprawdzenie 2giego dnia //
                    //------------------------//

                    if (typeof tydzien[x][y + 1] !== 'undefined') {

                        if (tydzien[x][y + 1][1] == null || tydzien[x][y + 1][2] == null) {
                            //jesli w nastepnym dniu jest wolne to pomijam
                            czas += 24 * 60;
                            //console.log("drugi wolny - " + czas/60);
                        }
                        else {
                            //nie sprawdzam czy dniowka przechodzi, bo jesli jest tu jakis dzien to nie sprawdzam nastepnego dnia a tylko czas od polnocy do rozpoczecia pracy
                            czas += tydzien[x][y + 1][1];
                        }
                    }

                    //---------------------//
                    //sprawdzenie 3go dnia //
                    //---------------------//

                    if(czas<36){ // sprawdzamy 3ci dzien tylko gdy jeszcze nie przekroczono wymaganego czasu

                        if (typeof tydzien[x][y + 2] !== 'undefined') {

                            if (tydzien[x][y + 2][1] == null || tydzien[x][y + 2][2] == null) {
                                //jesli w nastepnym dniu jest wolne to pomijam
                                czas += 24 * 60;
                            }
                            else {
                                //nie sprawdzam czy dniowka przechodzi, bo jesli jest tu jakis dzien to nie sprawdzam nastepnego dnia a tylko czas od polnocy do rozpoczecia pracy
                                czas += tydzien[x][y + 2][1];
                            }
                        }
                    }

                    //-------------------------------//
                    //podsumowanie tygodnia i wyniki //
                    //-------------------------------//

                    //console.log(czas/60);
                    if(czas > czasMax){
                        czasMax = czas;
                        if (czasMax >= 24 * 60) {

                            flagaOdpoczynekTygodniowy = 1;
                        }
                        if (czasMax >= (36 * 60)) {
                            flagaOdpoczynekTygodniowy = 2;
                        }
                    }
                }
                switch (flagaOdpoczynekTygodniowy) {
                    case 0:
                        text="<span style='color:red'>UWAGA! W tygodniu " + (x + 1) + " odpoczynek tygodniowy nie został zachowany! (<24h)</span>";
                        verify += text + "<br><br>";
                        break;
                    case 1:
                        text = "<span style='color:orange'>UWAGA! W tygodniu " + (x + 1) + " odpoczynek tygodniowy został zmniejszony (24-36h).</span>";
                        verify += text + "<br><br>";
                        break;
                    case 2:

                }
            }
            else{
                console.log("Tydzien " + (x+1) + " jest za krótki, nie obliczam odpoczynku tygodniowego");
            }


        }
    }
    //-----------------------------------------------------------------------------------------------
    //--------sprawdzam czy liczba dni wolnych od pracy = liczbie sobot, niedziel i swiąt------------
    //-----------------------------------------------------------------------------------------------
    {

        //workdays = liczba dni roboczych

        var licznikDniWolnych = 0;
        for(var x = 1 ; x<dzienmiesiaca.length ; x++){
            if(typeof dzienmiesiaca[x][5] == "undefined"){
                licznikDniWolnych ++ ;
            }
        }

        var liczbaDniWolnch = parseInt(dzienmiesiaca.length - workdays - 1);
        if(licznikDniWolnych >= liczbaDniWolnch){
            console.log("Liczba dni wolnych odpowiada liczbie sobot, niedziel i swiat");
        }
        else{
            verify += "<span style='color:red'>UWAGA! Liczba wolnych dni od pracy jest mniejsza od liczby wymaganych dni wolnych w miesiącu (soboty, niedziele, święta)</span>";
            verify += "<br>";
        }
    }

    $("#verify").html(verify);

}
function sprawdzWeekendowy(){
    //alert("weekendowy")

    /*
     System weekendowy:
     - Na dobę maksymalnie 12h   (ok)
     - Pracownik powinien byc zatrudniony w niepełnym etacie   (ok)
     - Praca tylko w piątki, soboty, niedziele i święta   (ok)
     - minimalny dobowy odpoczynek 11h (bo nie mozna pracowac wiecej niz 12h)   (ok)
     */

    //-----------------------------------------------------------------------------------------------
    //-------------------------sprawdzam czy któraś dniówka ma wiecej niz 12h------------------------
    //-----------------------------------------------------------------------------------------------
    {
        var dwanasciegodzin = 12*60;
        var flaga = true;
        var text = "<span style='color:orange'>UWAGA! W dniach ";
        for(var x=1; x<dzienmiesiaca.length; x++){
            if(dzienmiesiaca[x][5]>dwanasciegodzin){
                text += x + ", ";
                flaga = false;
            }
        }
        text = text.slice(0, -2);
        text += " miesiaca przekroczono maksymalny czas pracy!</span><br>";

        if(!flaga){
            console.log(text);
            verify += text;
            verify += "<br>";
        }
        else{
            //console.log("Dlugość pracy w poszczególne dni zgodna z kodeksem!")
        }
    }
    //-----------------------------------------------------------------------------------------------
    //---------------------------------sprawdzam czy etat nie jest pełny-----------------------------
    //-----------------------------------------------------------------------------------------------
    {

        if((etat[0]/etat[1])==1){

            //console.log("UWAGA! Wymiar etatu dla pracownika nie pozwala na wypracowanie pełnego miesiaca w systemie weekendowym!")
            verify += "<span style='color:red'>UWAGA! Wymiar etatu dla pracownika nie pozwala na wypracowanie pełnego miesiaca w systemie weekendowym!</span>";
            verify += "<br>";
        }

    }
    //-----------------------------------------------------------------------------------------------
    //----------------------sprawdzam czy pracownik pracuje tylko w weekendy i swieta----------------
    //-----------------------------------------------------------------------------------------------
    {

        var output = "<span style='color:red'>UWAGA! Na grafiku wprowadzono dni: ";
        var flaga = true;
        for(var x = 1 ; x<dzienmiesiaca.length; x++){

            //console.log(dzienmiesiaca[x][3]);

            if(dzienmiesiaca[x][3]=="roboczy" && dzienmiesiaca[x][6] != 5){


                if(dzienmiesiaca[x][1] != null && dzienmiesiaca[x][2] != null){

                    output += x + ", "
                    flaga = false;
                }
            }
        }
        output = output.slice(0,-2);
        output += " które nie są ani weekendowe ani świąteczne!</span>";

        if(!flaga){
            //console.log(output);
            verify += output;
            verify += "<br>";

        }
    }
    //-----------------------------------------------------------------------------------------------
    //---------------------sprawdzam czy zachowano minimalny dobowy odpoczynek (11h)-----------------
    //-----------------------------------------------------------------------------------------------
    {
        var odpoczynekFlaga = true;
        var odpoczynekString = "<span style='color:red'>UWAGA! Odpoczynek dobowy pomiędzy dniami: ";
        for(var x=1; x<dzienmiesiaca.length-1; x++){  //-1 poniewaz dzienny ostatni dzien to koniec okresu rozliczeniowego

            /*
             dzienmiesiaca[][0] - dzien miesiaca
             dzienmiesiaca[][1] - rozpoczecie pracy w minutach
             dzienmiesiaca[][2] - zakonczenie pracy w minutach
             dzienmiesiaca[][3] - identyfikator dnia // 0- pracujacy 1- swieto 2-niedziela 3-sobota
             dzienmiesiaca[][4] - nadgodziny // 0 - normalne 1 - nadgodziny
             dzienmiesiaca[][5] - czas pracy w minutach
             */


            var czas;

            if(dzienmiesiaca[x][1] == null || dzienmiesiaca[x][2] == null){
                //jesli nie pracuje w tym dniu to nic nie rob
                czas=24*60;
            }
            else{
                if(dzienmiesiaca[x+1][1] == null || dzienmiesiaca[x+1][2] == null){
                    //jesli w nastepnym dniu jest wolne to pomijam
                    czas = 24*60;
                }
                else{

                    //console.log(dzienmiesiaca[x][1] + " , " + dzienmiesiaca[x][2]);
                    if(dzienmiesiaca[x][1]>dzienmiesiaca[x][2]){
                        //jesli dniowka przechodzi na nastepny dzien
                        //alert("dniowka przechodzi")
                        czas = dzienmiesiaca[x+1][1] - dzienmiesiaca[x][2];
                        //alert(czas);
                    }
                    else{
                        //jesli praca odbywa sie w obrebie jednego dnia kalendarzowego
                        //alert("dniowka normalna");

                        czas = 24*60 - dzienmiesiaca[x][2];
                        czas += dzienmiesiaca[x+1][1];
                        //alert(czas);

                    }
                }

                if(czas<(11*60)){
                    //jesli odpoczynek dobowy mniejszy od 11h

                    odpoczynekString += x + " a " + parseInt(x+1) + ", ";
                    odpoczynekFlaga = false;
                }
            }
        }
        odpoczynekString = odpoczynekString.slice(0, -2);
        odpoczynekString += " nie został zachowany!</span>";

        if(!odpoczynekFlaga){
            //console.log(odpoczynekString);
            verify += odpoczynekString;
            verify += "<br>";
        }

    }


    $("#verify").html(verify);
}
function sprawdzCiagly(){
    //alert("ciagly");

     /*
     System ciągły:
     - (ok) wydłużenie maksymalnego tygodniowego czasu do 43h co przekłada się na 3 tygodnie w których jedna dniówka zamiast 8h ma 12h. Efektem jest 3x4 czyli 12h więcej w miesiącu jako nadgodziny
     - (ok) okres rozliczeniowy nie dluzszy niż 4 tygodnie (1 miesiac jest ok)(constant ok)
     - (ok) minimalny dobowy odpoczynek 11h
     - (ok) tygodniowy odpoczynek 35h lub 24
     - (ok) liczba dni wolnych >= liczba swiat, niedziel i sobót
     */

    //-----------------------------------------------------------------------------------------------
    //---------------------sprawdzam czy zachowano minimalny dobowy odpoczynek (11h)-----------------
    //-----------------------------------------------------------------------------------------------
    {
        var odpoczynekFlaga = true;
        var odpoczynekString = "<span style='color:red'>UWAGA! Odpoczynek dobowy pomiędzy dniami: ";
        for(var x=1; x<dzienmiesiaca.length-1; x++){  //-1 poniewaz dzienny ostatni dzien to koniec okresu rozliczeniowego

            /*
             dzienmiesiaca[][0] - dzien miesiaca
             dzienmiesiaca[][1] - rozpoczecie pracy w minutach
             dzienmiesiaca[][2] - zakonczenie pracy w minutach
             dzienmiesiaca[][3] - identyfikator dnia // 0- pracujacy 1- swieto 2-niedziela 3-sobota
             dzienmiesiaca[][4] - nadgodziny // 0 - normalne 1 - nadgodziny
             dzienmiesiaca[][5] - czas pracy w minutach
             */


            var czas;

            if(dzienmiesiaca[x][1] == null || dzienmiesiaca[x][2] == null){
                //jesli nie pracuje w tym dniu to nic nie rob
                czas=24*60;
            }
            else{
                if(dzienmiesiaca[x+1][1] == null || dzienmiesiaca[x+1][2] == null){
                    //jesli w nastepnym dniu jest wolne to pomijam
                    czas = 24*60;
                }
                else{

                    //console.log(dzienmiesiaca[x][1] + " , " + dzienmiesiaca[x][2]);
                    if(dzienmiesiaca[x][1]>dzienmiesiaca[x][2]){
                        //jesli dniowka przechodzi na nastepny dzien
                        //alert("dniowka przechodzi")
                        czas = dzienmiesiaca[x+1][1] - dzienmiesiaca[x][2];
                        //alert(czas);
                    }
                    else{
                        //jesli praca odbywa sie w obrebie jednego dnia kalendarzowego
                        //alert("dniowka normalna");

                        czas = 24*60 - dzienmiesiaca[x][2];
                        czas += dzienmiesiaca[x+1][1];
                        //alert(czas);

                    }
                }

                if(czas<(11*60)){
                    //jesli odpoczynek dobowy mniejszy od 11h

                    odpoczynekString += x + " a " + parseInt(x+1) + ", ";
                    odpoczynekFlaga = false;
                }
            }
        }
        odpoczynekString = odpoczynekString.slice(0, -2);
        odpoczynekString += " nie został zachowany!</span>";

        if(!odpoczynekFlaga){
            //console.log(odpoczynekString);
            verify += odpoczynekString;
            verify += "<br>";
        }

    }
    //----------------------------------------------------------------------------------------------------
    //--sprawdzam czy w nie przekroczono 12h nadgodzin z uwagi na tryb ciagly (srednio 4tygodnie po 43h)--
    //----------------------------------------------------------------------------------------------------
    {
        var czterdziescigodzin = 40 * 60;
        var flagaOgolna = true;

        var wymiar_etatu = etat[0]/etat[1];

        for (var y = 1; y < dzienmiesiaca.length; y++) {  //petla odliczajaca poszczegolne tygodnie

            var sumagodzin = 0;

            if(dzienmiesiaca[y][4]==1 || dzienmiesiaca[y][4]==0){
                sumagodzin += dzienmiesiaca[y][5]; //zliczenie wszystkich godzin na grafiku
            }


            if (sumagodzin > ((czterdziescigodzin*wymiar_etatu) + 12)) {

                var text = "<span style='color:red'>UWAGA: Ilość wprowadzonych godzin(wraz z nadgodzinami) w tygodniach: " + parseInt(y + 1) + " przekracza ustawowe normy! Zmniejsz ilość godzin! (" + (sumagodzin) / 60 + ")";
                verify += text;
                verify += "<br>";
                flagaOgolna = false;
            }

        }

    }
    //-----------------------------------------------------------------------------------------------
    //--------sprawdzam czy zachowano minimalny ciagły odpoczynek tygodniowy (35h lub 24h)-----------
    //-----------------------------------------------------------------------------------------------
    {
        for(var x=0; x<tydzien.length; x++) {

            var flagaOdpoczynekTygodniowy = 0; //0 - nok  1 - >=24h  2 - >=36h
            var czasMax = 0;

            if(tydzien[x].length == 7 ){
                //jeśli tydzien nie jest pełny to pomijam

                console.log(x);
                for (var y = 0; y < tydzien[x].length; y++) {

                    /*
                     będzie trzeba sprawdzic wszystkie 3 dni pod rzad, bo 36h moze byc rozbite na 3 dni kalendarzowe:
                     - pierwszy dzien nalezy sprawdzic, ile czasu wolnego po pracy i czy w pracy
                     - drugi ile czasu przed praca, lub czy wolny
                     - trzeci, jesli jeszcze nie spelniono warunku >36h oraz drugi dzien wolny, sprawdzenie pracy przed lub dnia wolnego
                     */

                    //------------------------//
                    //sprawdzenie 1szego dnia //
                    //------------------------//

                    if (tydzien[x][y][1] == null || tydzien[x][y][2] == null) {
                        //jesli w nastepnym dniu jest wolne to pomijam
                        czas = 24 * 60;
                    }
                    else {

                        if (tydzien[x][y][1] > tydzien[x][y][2]) {
                            //jesli dniowka przechodzi na nastepny dzien
                            czas = -(tydzien[x][y][2]);  // czas jest ujemny
                        }
                        else {
                            //jesli praca odbywa sie w obrebie jednego dnia kalendarzowego
                            czas = 24 * 60 - tydzien[x][y][2];
                        }
                    }

                    //------------------------//
                    //sprawdzenie 2giego dnia //
                    //------------------------//

                    if (typeof tydzien[x][y + 1] !== 'undefined') {

                        if (tydzien[x][y + 1][1] == null || tydzien[x][y + 1][2] == null) {
                            //jesli w nastepnym dniu jest wolne to pomijam
                            czas += 24 * 60;
                            //console.log("drugi wolny - " + czas/60);
                        }
                        else {
                            //nie sprawdzam czy dniowka przechodzi, bo jesli jest tu jakis dzien to nie sprawdzam nastepnego dnia a tylko czas od polnocy do rozpoczecia pracy
                            czas += tydzien[x][y + 1][1];
                        }
                    }

                    //---------------------//
                    //sprawdzenie 3go dnia //
                    //---------------------//

                    if(czas<36){ // sprawdzamy 3ci dzien tylko gdy jeszcze nie przekroczono wymaganego czasu

                        if (typeof tydzien[x][y + 2] !== 'undefined') {

                            if (tydzien[x][y + 2][1] == null || tydzien[x][y + 2][2] == null) {
                                //jesli w nastepnym dniu jest wolne to pomijam
                                czas += 24 * 60;
                            }
                            else {
                                //nie sprawdzam czy dniowka przechodzi, bo jesli jest tu jakis dzien to nie sprawdzam nastepnego dnia a tylko czas od polnocy do rozpoczecia pracy
                                czas += tydzien[x][y + 2][1];
                            }
                        }
                    }

                    //-------------------------------//
                    //podsumowanie tygodnia i wyniki //
                    //-------------------------------//

                    //console.log(czas/60);
                    if(czas > czasMax){
                        czasMax = czas;
                        if (czasMax >= 24 * 60) {

                            flagaOdpoczynekTygodniowy = 1;
                        }
                        if (czasMax >= (36 * 60)) {
                            flagaOdpoczynekTygodniowy = 2;
                        }
                    }
                }
                switch (flagaOdpoczynekTygodniowy) {
                    case 0:
                        text="<span style='color:red'>UWAGA! W tygodniu " + (x + 1) + " odpoczynek tygodniowy nie został zachowany! (<24h)</span>";
                        verify += text + "<br><br>";
                        break;
                    case 1:
                        text = "<span style='color:orange'>UWAGA! W tygodniu " + (x + 1) + " odpoczynek tygodniowy został zmniejszony (24-36h).";
                        verify += text + "<br><br>";
                        break;
                    case 2:

                }
            }
            else{
                console.log("Tydzien " + (x+1) + " jest za krótki, nie obliczam odpoczynku tygodniowego");
            }


        }
    }
    //-----------------------------------------------------------------------------------------------
    //--------sprawdzam czy liczba dni wolnych od pracy = liczbie sobot, niedziel i swiąt------------
    //-----------------------------------------------------------------------------------------------
    {

        //workdays = liczba dni roboczych

        var licznikDniWolnych = 0;
        for(var x = 1 ; x<dzienmiesiaca.length ; x++){
            if(typeof dzienmiesiaca[x][5] == "undefined"){
                licznikDniWolnych ++ ;
            }
        }

        var liczbaDniWolnch = parseInt(dzienmiesiaca.length - workdays - 1);
        if(licznikDniWolnych >= liczbaDniWolnch){
            console.log("Liczba dni wolnych odpowiada liczbie sobot, niedziel i swiat");
        }
        else{
            verify += "<span style='color:orange'>UWAGA! Liczba wolnych dni od pracy jest mniejsza od liczby wymaganych dni wolnych w miesiącu (soboty, niedziele, święta)</span>";
            verify += "<br>";
        }
    }

    $("#verify").html(verify);
}
function sprawdzInny(){
    //alert("inny");

}
function utworzTydzien(){
    /*
    // przypisanie dni do tygodni (jesli tydzien liczymy od pon do nd)-----------------
    var counter = 0;
    var counter2 = 0;
    tydzien[0] = new Array();
    for(var y=1; y<=dni_w_miesiacu; y++){
        tydzien[counter][counter2] = dzienmiesiaca[y];
        counter2++
        if(dzienmiesiaca[y][3]=="niedziela"){
            counter++;
            counter2 = 0;
            tydzien[counter] = new Array();
        }
    }
    //---------------------------------------------------------------------------------
    */
    // przypisanie dni do tygodni (jesli tydzien liczymy 7 dni od poczatku mca)--------
    var counter = 0;
    var counter2 = 0;
    tydzien[0] = new Array();
    for(var y=1; y<=dni_w_miesiacu; y++){
        tydzien[counter][counter2] = dzienmiesiaca[y];
        counter2++
        if(counter2%7 == 0){
            counter++;
            counter2 = 0;
            tydzien[counter] = new Array();
        }
    }
    //--------------------------------------------------------------------------------
}
function cleanOutput(){
    console.log("\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n\n");
}
function showVerify(){
/*
    $("#verify").toggle("fast");
    $("#liczbaswiat").toggle("fast");
    $("#swieta").toggle("fast");
    $("#liczbadni").toggle("fast");
    $("#liczbagodzin").toggle("fast");
    $("#ilegodzin").toggle("fast");
    */
    $("#weryfikacja").toggle("fast");

}
function alert_fadeOut(){
    $(this).fadeOut();
    alert("dupa");
}

//-----------------FUNKCJE ODPOWIEDZIALNE ZA OBSŁUGĘ KALENDARZA---------------------//
function calendarupdate(){
    var valueString = ""
    var bool = false;
    $(".inwork").each(function(){

        if(bool==false){
            valueString=valueString+$(this).attr('id')+"="+$(this).attr("value")+"="+$(this).attr("absence");
            bool=true;
        }
        else{
            valueString=valueString+"|"+$(this).attr('id')+"="+$(this).attr("value")+"="+$(this).attr("absence");
        }

    });
    $("#dniowki").val(valueString);  //Zapisanie spisu do wartosci value w ukrytym polu input celem przeslania do serwera php
    //-----------------------------------------------------------------
    $("#test").text(valueString);  //pole w formularzu do debugowania pod kalendarzem
}
function workdaycount() {
    var holidaycount =  $('.holiday').size();
    var workdaycount = $('.workday').size();

    workdaycount = workdaycount - holidaycount;                                  //oblicznie liczby dni pracujacych

    $("#liczbaswiat").text("Zaznaczonych " + holidaycount + " świąt!");
    $("#liczbagodzin").text("Liczba dni roboczych: " + workdaycount + " Liczba godzin roboczych: "+ workdaycount*8);
    $("#swieta").text("");

    var counter = 0;
    $(".holiday").each(function(){

        if(counter==0){
            $( "#swieta" ).append($(this).attr('id'));
            counter++;
        }
        else {
            $("#swieta").append(":" + $(this).attr('id'));
        }

    });

    $("#input-swieta").val($("#swieta").text());

}
function clearcalendar(){

    $(".inwork").each(function(){
        $(this).removeClass("inwork");
        $(this).attr("value", "");
        var idDnia = $(this).attr('id');
        $(this).html("<b>"+idDnia+"</b>");  // wprowadzenie czas na widok kalendarza

    });

    calendarupdate();
    test();

}

$(document).ready(function () {
    $('[data-toggle="tooltip"]').tooltip();
    $(".clickable").click(function () {
        $(this).toggleClass("holiday");
        workdaycount();
    });
    $(".clicktoset").click(function () {

        //----------------CZĘŚĆ ODPOWIEDZIALNA ZA WYLICZENIE GODZINY ZAKONCZENIA PO DODANIU GODZINY DO ROZPOCZECIA CZASU PRACY-------------
        var worktime = $("#godzinastart option:selected" ).text()+":"+$( "#minutastart option:selected" ).text()+"-"+$( "#godzinakoniec option:selected" ).text()+":"+$( "#minutakoniec option:selected" ).text()

        var godziny = worktime.split("-");
        var godzinaRozpoczecia = godziny[0];
        var czasPracy = godziny[1];
        var godzina = godzinaRozpoczecia.split(":");


        godzina[0] = parseInt(godzina[0]);
        godzina[1] = parseInt(godzina[1]);

        var minutyRozpoczecia = godzina[0] * 60 + godzina[1];
        var godzina = czasPracy.split(":");

        godzina[0] = parseInt(godzina[0]);
        godzina[1] = parseInt(godzina[1]);

        var minutyPracy = godzina[0] * 60 + godzina[1];

        //alert(minutyPracy);
        //alert(minutyRozpoczecia);
        var minutyZakonczenia = minutyRozpoczecia + minutyPracy;

        if(minutyZakonczenia>1440){
            minutyZakonczenia = minutyZakonczenia - 1440;
        }

        var godzinaZakonczenia = parseInt(minutyZakonczenia/60) + ":" + ("0" + minutyZakonczenia%60).slice(-2);
        //--------------------------------------------------------------------------------------------------------------------------------

        var worktime = ("0" + $("#godzinastart option:selected" ).text()).slice(-2)+":"+$( "#minutastart option:selected" ).text()+"-"+godzinaZakonczenia;
        var urlop = $("#urlop option:selected" ).val();  // pobranie value z pola wyboru urlopu


        if($(this).hasClass("inwork")){
            var czaspracy = "";
        }
        else{

            if(urlop == 1){
                var czaspracy = "<span class='naZadanie'><small>"+worktime+"<br>Na żądanie</small></span>";
            }
            else if(urlop == 2){
                var czaspracy = "<span class='urlop'><small>"+worktime+"<br>Urlop";
            }
            else if(urlop == 3){
                var czaspracy = "<spam class='chorobowe'><small>"+worktime+"<br>Chorobowe</small></span>";
            }
            else if(urlop == 4){
                var czaspracy = "<spam class='nadgodziny'><small>"+worktime+"<br>Nadgodziny</small></spam>";
            }
            else{
                var czaspracy = "<small>"+worktime+"</small>";
            }

        }
        $(this).toggleClass("inwork");

        var idDnia = $(this).attr('id');
        $(this).html("<b>"+idDnia+"</b><br><br>"+czaspracy);  // wprowadzenie czas na widok kalendarza
        $(this).attr("value", worktime);
        $(this).attr("absence", urlop);

        calendarupdate();
        test()

    });
    $(".clicktoset-request").click(function () {
        var worktime = $("#godzinastart option:selected" ).text()+":"+$( "#minutastart option:selected" ).text()+"-"+$( "#godzinakoniec option:selected" ).text()+":"+$( "#minutakoniec option:selected" ).text()

        var urlop = $("#urlop option:selected" ).val();  // pobranie value z pola wyboru urlopu


        if($(this).hasClass("inwork")){
            var czaspracy = "";
        }
        else{

            if(urlop == 0){
                var czaspracy = "<span class='niedostepnosc'><small>"+worktime+"<br>Niedostępność</small></span>";
            }
            else if(urlop == 1){
                var czaspracy = "<span class='dostepnosc'><small>"+worktime+"<br>Dostępność</small></span>";
            }
            else if(urlop == 2){
                var czaspracy = "<spam class='urlop'><small>"+worktime+"<br>Urlop</small></span>";
            }

        }
        $(this).toggleClass("inwork");

        var idDnia = $(this).attr('id');
        $(this).html("<b>"+idDnia+"</b><br><br>"+czaspracy);  // wprowadzenie czas na widok kalendarza
        $(this).attr("value", worktime);
        $(this).attr("absence", urlop);

        calendarupdate();

    });
    $(".selectable").click(function () {

        var idDnia = $(this).attr('id');
        var godziny = $(this).attr('value');
        var godzinykopia = godziny;

        godziny = godziny.split("-");
        var godzinaRozpoczecia = godziny[0];
        var godzinaZakonczenia = godziny[1];

        var godzina = godzinaRozpoczecia.split(":");

        godzina[0] = parseInt(godzina[0]);
        godzina[1] = parseInt(godzina[1]);

        var minutyRozpoczecia = godzina[0] * 60 + godzina[1];
        var godzina = godzinaZakonczenia.split(":");

        godzina[0] = parseInt(godzina[0]);
        godzina[1] = parseInt(godzina[1]);

        var minutyZakonczenia = godzina[0] * 60 + godzina[1];
        var czaspracy = minutyZakonczenia - minutyRozpoczecia;

        if(minutyZakonczenia<minutyRozpoczecia){
            czaspracy = czaspracy + 1440;
        }

        var valueString ="";

        $(".free").each(function(){
            valueString = valueString + $(this).attr('id') + ";";
        });

        if($(this).parent().hasClass("warning")){
            $(".warning").each(function(){
                $(this).removeClass("warning");
            });
            $("#submit").prop("disabled", true);
            $('#dzien').val("");
            $('#czaspracy').val("");
            $('#dniwolne').val("");
            $('#godzinypracy').val("");
        }
        else{
            $(".warning").each(function(){
                $(this).removeClass("warning");
            });
            $("#submit").prop("disabled", false);
            $(this).parent().addClass("warning");

            $('#dzien').val(idDnia);
            $('#czaspracy').val(czaspracy);
            $('#dniwolne').val(valueString);
            $('#godzinypracy').val(godzinykopia);
        };
    });
    $(".selectableAll").click(function () {

        $(this).toggleClass("warning")

        var flaga = false;
        var valueString = "";
        $(".warning").each(function(){
            flaga=true;

            var idDnia = $(this).attr('id');
            var godziny = $(this).attr('value');
            valueString += idDnia + "|";
        });
        $("#propozycje").val(valueString);

        if(flaga==true){
            $("#submit").prop("disabled", false)
        }
        else{
            $("#submit").prop("disabled", true)
        }
    });
});