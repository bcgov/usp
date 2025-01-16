<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <title>USPA - International Study Permit</title>
    <style>
        @font-face {
            font-family: "DejaVu Sans";
            src: url("' . public_path('fonts/DejaVuSans.ttf') . '") format("truetype");
        }
        body{
            font-family: "DejaVu Sans", sans-serif;
            line-height: 1rem;
            font-size: 13px;
            margin: 130px 30px 30px 30px;
            position: relative;
            /* Ensure the watermark covers the entire page */
            min-height: 100vh;
        }
        header { position: fixed; top: 0px; left: 0px; right: 0px; height: 120px; border-bottom: #0b2e13 1px solid; }
        footer { position: fixed; bottom: 12px; left: 0px; right: 0px; height: 80px; font-size: 9px;
            border-top: #0b2e13 1px solid; line-height: 1rem;}
        td{
            vertical-align: top;
        }
        ul{
            margin-top: 0; padding-top: 0;
            margin-bottom: 0; padding-bottom: 0;
        }
        table{
            width: 100%;
        }
        img{
            width: 125px;
        }
        .watermark {
            position: absolute;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%) rotate(-45deg);
            font-size: 10em;
            color: rgba(0, 0, 0, 0.2);
            pointer-events: none;
        }
    </style>
</head>
<body>
    <header>
        <table>
            <tr>
                <td style="text-align: left; width: 25%;"><img src="{{ public_path('/images/bc_sq_logo.png') }}"></td>
                <td style="text-align: center; width: 50%;"></td>
                <td style="text-align: left; width: 25%;"><strong>{{ $attestation->fed_guid }}</strong></td>
            </tr>
        </table>
    </header>

    <table>
        <tr><td>{{ $now_d }}<br/><br/></td></tr>
        <tr>
            <td>
                Student name: {{ $attestation->first_name }} {{ $attestation->last_name }}<br/>
                Attestation ID: <strong>{{ $attestation->fed_guid }}</strong><br/>
                Issue date: {{ $attestation->issue_date }}<br/>
                Expiry date: {{ $attestation->expiry_date }}<br/>
                <br/><br/>
                Dear {{ $attestation->first_name }} {{ $attestation->last_name }},<br/><br/>
                This provincial attestation letter confirms that you have a space within British Columbia’s 2024 allocation period for study permit applications, based on the information that you provided:<br/>
                <strong>Student:</strong>
                <ul>
                    <li>Student Name: {{ $attestation->last_name }}, {{ $attestation->first_name }}</li>
                    <li>Student Date of Birth: {{ $attestation->dob }}</li>
                    <li>Student's Current Residence Address: {{ $attestation->address1 }}, {{ $attestation->city }}, {{ $attestation->country }}</li>
                </ul>
                <br/>
                <strong>Study Program:</strong>
                <ul>
                    <li>Designated Learning Institution (DLI) name: {{ $attestation->institution->name }}</li>
                    <li>DLI number: {{ $attestation->institution->dli }}</li>
                    <li>Program name: {{ $attestation->program->program_name }}</li>
                    <li>{{ $attestation->program->program_graduate ? 'Graduate' : 'Undergraduate' }} Program</li>
                </ul>
                <br/>
                This provincial attestation letter is valid until {{ $attestation->expiry_date }}, or until the study permit application cap is reached.<br/><br/>
                You must include this provincial attestation letter when you submit your study permit application to
                Immigration, Refugees and Citizenship Canada (IRCC). <strong>A study permit application that does not include a
                provincial attestation letter, or meet an exception as outlined in the Ministerial Instructions, will
                    not be accepted for processing.</strong> Please refer to <a href="https://www.canada.ca/en/immigration-refugees-citizenship/services/study-canada.html">IRCC’s web site</a> for study permit application information.

                <br/><br/><br/>{{ $utils['PAL Signature Name'][0]->field_name }}<br/>{{ $utils['PAL Signature Position'][0]->field_name }}
                <br/>{{ $utils['PAL Signature Division'][0]->field_name }}<br/>{{ $utils['Ministry Name'][0]->field_name }}
            </td>
        </tr>

    </table>
    <footer>
        <table>
            <tr>
                <td style="width: 35%;">{{ $utils['Ministry Name'][0]->field_name }}<br/>
                    {{ $utils['Ministry Branch'][0]->field_name }}<br/><a href="{{$utils['Ministry Branch Url'][0]->field_name}}">{{ $utils['Ministry Branch Url'][0]->field_name }}</a></td>
                <td style="width: 30%;">
                    Mailing Address:<br/>
                    {!! $utils['Ministry Mailing Address'][0]->field_name !!}<br/>
                    {{ $utils['Ministry Tele Victoria'][0]->field_name }}<br/>
                    {{ $utils['Ministry Tele Toll-free'][0]->field_name }} (Toll-free in Canada/USA)<br/>
                    {{ $utils['Ministry Tele Toll-free'][0]->field_name }} (B.C. Lower Mainland)<br/>
                </td>
                <td style="width: 25%;">
                    Courier Address:<br/>
                    {!! $utils['Ministry Courier Address'][0]->field_name !!}<br/>
                    Fax: {{ $utils['Ministry Fax'][0]->field_name }}<br/>
                    Toll-free Fax: {{ $utils['Ministry Fax Toll-free'][0]->field_name }}
                </td>
            </tr>
        </table>
    </footer>
    @if($draft)
        <div class="watermark">SAMPLE</div>
    @endif

</body>
</html>
