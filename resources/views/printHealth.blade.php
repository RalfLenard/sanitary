<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Health Cards PDF</title>
    <style>

        * {
            text-transform: uppercase;
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            display: flex;
            flex-direction: column;
            font-family: Arial, Helvetica, sans-serif;
            font-size: 13px;
            font-weight: bold;
            justify-content: center;
            align-items: center;
        }
        
        .card {
            display: flex;
            width: 7.4in;
            height: 5.4in;
        }

        .page-break {
            page-break-after: always;
        } 

        h2 {
            text-align: center;
        }

        .left {
            width: 50%;
            display: flex;
            flex-direction: column;
        }

        #form {
            position: absolute;
            top: 15px;
            left: 200px;
        }

        #scode {
            position: absolute;
            top: 68px;
            left: 245px;
        }

        .left {
            width: 50%;
            display: flex;
            flex-direction: column;
            position: relative; /* ✅ Fix absolute child elements */
        }

        #emp_data {
           position:relative;
           top: 140px;
            left: 70px;
        }

        #name {
            position: absolute;
            top: 58px;
            left: 10px;
            width: 280px;
            /* border: solid red 1px; */
            text-align: center;
        }

        #pos {
            position: absolute;
            top: 81px;
            left: 44px;
            /* height: 25px; */
            text-align: center;
            width: 250px;
            /* border: solid red 1px; */
           
            
        }

        #age {
            position: absolute;
            top: 105px;
            left: 8px;
            width: 40px;
            /* border: solid red 1px; */
            text-align: center;
            

            
          
            
        }

        #gender {
            position: absolute;
            top: 105px;
            left: 71px;
            width: 70px;
            /* border: solid red 1px; */
            text-align: center;
          
        
        }

        
        .fil {
            position: absolute;
            top: 105px;
            left: 195px;
           
            width: 105px;
            /* border: solid red 1px; */
            text-align: center;
         
        }
        
        #workplace {
            position: absolute;
            top: 155px;
            left: 66px;
            width: 240px;
            /* border: solid red 1px; */
            text-align: center;
           
           
        }

        .left #sig {
            position: absolute;
            display: flex;
            flex-direction: column;
            width: 100%;
            gap: 40px;
            top: 375px;
            left: 83px;
        }

        #doc {
            position: relative;
            top: -30px;
            left: 7px;
            font-size: 10px;
        }

        #ins{
            position: relative;
            top: -25px;
            left: 7px;
            font-size: 10px;
        }

        #ins, #doc {
            display: flex;
            border: 50px;
            justify-content: center;
            align-items: center;
        }



        .right {
            width: 50%;
            display: flex;
            flex-direction: column;
        }

        #date {
            gap: 48px;
            display: flex;
            flex-direction: row;
            justify-content: center;
            align-items: center;
            margin-top: 120px;
            margin-right: 18px;
        }

        /* .date1{
            position: absolute;
            top: 20px;
        } */

        .Meds{
            position: relative;
            display: flex;
            gap: 160px;
            width: 100%;
            justify-content: center;
            top: 241px;
            left: -16px;
        }

        .dos{
            text-transform: lowercase;
        }

        .med_result, .do {
            display: flex;
            flex-direction: column;
            gap: 12px;
        }

        #med1 {
            display: flex;
            justify-content: center;
            gap: 160px;
            width: 100%;
            position: relative;
            top: 166px;
            left: -16px;
        }

        .sir-a {
            width: 90px;
            height: 90px;
            position: absolute; /* Position the image absolutely */
            top: -50px; /* Adjust as needed */
        left: 135px; /* Adjust as needed */
        z-index: 1; /* Puts image behind the text */
      
    }

    .sir-g {
            width: 100px;
            height: 100px;
            position: absolute; /* Position the image absolutely */
            top: -60px; /* Adjust as needed */
        left: 130px; /* Adjust as needed */
        z-index: 1; /* Puts image behind the text */
      
    }

    .sir-rald {
            width: 110px;
            height: 180px;
            position: absolute; /* Position the image absolutely */
            top: -45px; /* Adjust as needed */
        left: 120px; /* Adjust as needed */
        z-index: 1; /* Puts image behind the text */
         /* filter: contrast(300%);
        filter: drop-shadow(1px 1px 0px black)  */
            /* drop-shadow(-1px -1px 0px black) */
            /* drop-shadow(1px -1px 0px black) */
            /* drop-shadow(-1px 1px 0px black); */
      
    }

    .emc {
            width: 135px;
            height: 90px;
            position: absolute; /* Position the image absolutely */
            top: -50px; /* Adjust as needed */
        left: 110px; /* Adjust as needed */
        z-index: 1; /* Puts image behind the text */
         /* filter: contrast(300%);
        filter: drop-shadow(1px 1px 0px black)  */
            /* drop-shadow(-1px -1px 0px black) */
            /* drop-shadow(1px -1px 0px black) */
            /* drop-shadow(-1px 1px 0px black); */
      
    }

    .sir-doc {
        width: 150px;
        height: 150px;
        position: absolute; /* Position the image absolutely */
        top: -35px; /* Adjust as needed */
        left: 135px; /* Adjust as needed */
        z-index: 1; /* Puts image behind the text */
        /* filter: contrast(300%);
        filter: drop-shadow(1px 1px 0px black)  */
            /* drop-shadow(-1px -1px 0px black) */
            /* drop-shadow(1px -1px 0px black) */
            /* drop-shadow(-1px 1px 0px black); */
      
    }

    .non {
    width: 330px;
    height: 210px;
    position: absolute; /* Position the image absolutely */
    top: 5px; /* Adjust as needed */
    left: 50px; /* Adjust as needed */
    z-index: -1; /* Puts image behind the text */
    transform: rotate(180deg); /* Rotates the image */
}

.non-2 {
    width: 330px;
    height: 210px;
    position: absolute; /* Position the image absolutely */
    top: 255px; /* Adjust as needed */
    right: -328px; /* Adjust as needed */
    z-index: -1; /* Puts image behind the text */
    transform: rotate(360deg); /* Rotates the image */
}

        
    </style>
</head>
<body>

    @foreach ($healthCards as $health)

    @php
        $img_type = [
            'non_food' => base64_encode(file_get_contents(public_path('images/non.png'))),
            'food' => base64_encode(file_get_contents(public_path('images/food.png'))),
            'others' => base64_encode(file_get_contents(public_path('images/non.png')))
        ];

        $imgSrc = $img_type[$health->health_card_type] ?? base64_encode(file_get_contents(public_path('images/non.png')));
    @endphp
        <div class="card">
            <div class="left">

                 <img class="non" src="data:image/png;base64,{{ $imgSrc }}" alt="Signature">
                 <img class="non-2" src="data:image/png;base64,{{ $imgSrc }}" alt="Signature">
                <p id="form">EHS FORM NO. 102-{{ $health->health_card_type == 'food' ? 'A' : 'B' }} </p>
                <p id="scode">{{ $health->print_code }}</p>
                <div id="emp_data">
                    <p id="name">{{ $health->full_name }}</p>
                    <p id="pos">{{ $health->designation }}</p>
                    <p id="age">{{ $health->age }}</p>
                    <p id="gender">{{ $health->gender }}</p>
                    <p class="fil">FILIPINO</p>
                    <p id="workplace" 
                        style="font-size: {{ strlen(str_replace(' ', '', $health->place_of_employment)) >= 20 ? '9px' : '13px' }};
                                position: absolute;
                                top: {{ strlen(str_replace(' ', '', $health->place_of_employment)) >= 20 ? '130px' : '125px' }};">
                            {{ $health->place_of_employment }}
                        </p>

                </div>

                <div id="sig">
                   
                    <p id="ins">{{ $health->inspector_name }}</p>
                    <p id="doc">BENJAMIN Q. BENGCO III, M.D.</p>

                    @if(Str::lower($health->inspector_name) === 'aaron jay c. gonzales')
                            <img class="sir-a" src="data:image/png;base64,{{ base64_encode(file_get_contents(public_path('images/A-sig.png'))) }}" alt="Signature">
                        @elseif(Str::lower($health->inspector_name) === 'gerald b. castro')
                            <img class="sir-rald" src="data:image/png;base64,{{ base64_encode(file_get_contents(public_path('images/sir-rald.png'))) }}" alt="Sir G">
                        @elseif(Str::lower($health->inspector_name) === 'michael christian d. muñoz')
                            <img class="emc" src="data:image/png;base64,{{ base64_encode(file_get_contents(public_path('images/emc.png'))) }}" alt="Sir G">
                        @elseif(Str::lower($health->inspector_name) === 'gregorio b. arceo')
                            <img class="sir-g" src="data:image/png;base64,{{ base64_encode(file_get_contents(public_path('images/sir-g.png'))) }}" alt="Sir G">

                        @endif



                
                    <img class="sir-doc" src="data:image/png;base64,{{ base64_encode(file_get_contents(public_path('images/DOC-B.png'))) }}" alt="Signature">
                   
                </div>
            </div>
            <div class="right">
          
                <div id="date">
             
                    <p class="date1">{{ \Carbon\Carbon::parse($health->date_of_issuance)->format('F d, Y') }}</p>
                    <p>{{ \Carbon\Carbon::parse($health->date_of_expiration)->format('F d, Y') }}</p>
                </div>
                <div id="med1">
                    <p>NORMAL</p>
                    <p class="dos">-do-</p>
                </div>
                <div class="Meds">
                    <div class="med_result">
                        <p>NORMAL</p>
                        <p>NORMAL</p>
                    </div>
                    <div class="do">
                        <p class="dos">-do-</p>
                        <p class="dos">-do-</p>
                    </div>
                </div>
             
            </div>
           

        </div>

      

        <div class="page-break"></div>
        <!-- Page Break After Each Card -->
    @endforeach

</body>
</html>