<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <title>USPA - International Study Permit</title>
    <style>
        body{
            line-height: 1.1rem;
            font-size: 14px;
            margin: 130px 30px 30px 30px;
        }
        header { position: fixed; top: 0px; left: 0px; right: 0px; height: 120px; border-bottom: #0b2e13 1px solid; }
        footer { font-family: sans-serif; position: fixed; bottom: 12px; left: 0px; right: 0px; height: 80px; font-size: 10px;
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
    </style>
</head>
<body>
    <header>
        <table>
            <tr>
                <td style="text-align: left; width: 25%;"><img src="{{ public_path('/images/bc_sq_logo.png') }}"></td>
                <td style="text-align: center; width: 50%;"></td>
                <td style="text-align: left; width: 25%;"></td>
            </tr>
        </table>
    </header>

    <table>
        <tr><td style="text-align: right;">{{ $now_d }}</td></tr>
        <tr>
            <td>
                Student name: {{ $attestation->student_name }}<br/>
                Attestation ID: <strong>{{ $attestation->guid }}</strong><br/>
                Issue date/time: {{ $attestation->created_at }}<br/>
                Expiry date: {{ $attestation->expiry_date }}<br/>
                <br/><br/><br/>
                Dear {{ $attestation->student_name }}:<br/><br/>

                <h1>Some body</h1>
                <table>
                    <tr>
                        <td align="right" width="100">Institution name: </td>
                        <td>{{ $attestation->institution->name }}</td>
                    </tr>
                    <tr>
                        <td align="right">Institution address: </td>
                        <td>{{ $attestation->institution->address1 }}</td>
                    </tr>
                    @if(!is_null($attestation->institution->address2))
                    <tr>
                        <td align="right"></td>
                        <td><strong>{{ $attestation->institution->address2 }}</strong></td>
                    </tr>
                    @endif
                    <tr>
                        <td align="right"></td>
                        <td>{{ $attestation->institution->city }} {{ $attestation->institution->province }} {{ $attestation->institution->postal_code }}</td>
                    </tr>
                    <tr>
                        <td align="right"></td>
                        <td>Canada</td>
                    </tr>
                </table>





                <br/><br/>Sincerely,<br/><br/><br/>
            </td>
        </tr>

    </table>
    <footer>
        <table>
            <tr>
                <td style="width: 25%;">Ministry of Post-Secondary Education and Future Skills</td>
                <td style="width: 20%;">StudentAid BC</td>
                <td style="width: 30%;">
                    Mailing Address:<br/>
                    PO Box 9173 Stn Prov Govt<br/>
                    Victoria BC V8W 9H7<br/>
                    (250) 387-6100<br/>
                    1-800-561-1818 (Toll-free in Canada/USA)<br/>
                    1-800-561-1818 (B.C. Lower Mainland)<br/>
                </td>
                <td style="width: 25%;">
                    Courier Address:<br/>
                    4th Fl, 835 Humboldt St<br/>
                    Victoria BC V8W 9H2<br/>
                    Fax: (250) 387-4858<br/>
                    Toll-free Fax: 1-866-312-3322
                </td>
            </tr>
        </table>
    </footer>

</body>
</html>
