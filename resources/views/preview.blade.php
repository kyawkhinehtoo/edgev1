<!DOCTYPE html>
<html lang="en">

<head>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta http-equiv="Content-type" content="text/html; charset=utf-8" />
    <meta charset="UTF-8" />
  
    <title>{{ config('app.name', 'Laravel') }}</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link rel="stylesheet" href="{{ asset('storage/css/burglish.css')}}">
<style>
        @media print {
            body {
                width: 21cm;
                height: 29.7cm;
                margin: 30mm 45mm 30mm 45mm;
                page-break-inside: avoid;
            }
            .container {
                width: 100%;
                top: 0;
                position: absolute;
            }
        }

    
    
        .header-img {
            width: 100%;
            margin-bottom: 50px;
            height: auto;
        }

     
 
        .container {
            width: 100%;
            min-height: 100%;
            position: relative;
        }

        .footer {
            position: absolute;
            bottom: 0;
            left: 0;
            width: 100%;
            margin-top: 50px;
            color: #ffffff;
            text-align: center;
        }

        /* Update body to ensure full height */
        html,
        body {
            margin: 0 auto;
            min-height: 100%;
            height: 297mm;
            width: 210mm;
            font-family: sans-serif;
            font-size: 0.9rem;
            position: relative;
        }
        .center {
            margin: 0 auto;
            width: 85%;
        }
      
        .center table {
            margin: 0 auto;
            width: 100%;
        }
        table.head{
            border-spacing: 0;
        }
       .text-bold {
            font-weight: 800;
        }

        .text-semibold {
            font-weight: 600;
        }

        .border {
            border: 2px solid #000000;
        }

        .border-top {
            border-top: 2px solid #000000;
        }

        .border-bottom {
            border-bottom: 2px solid #000000;
        }

        .font-medium {
            font-size: 1.4rem;
        }

        .font-small {
            font-size: 1rem;
        }

        .collapse {
            border-collapse: collapse;
            padding: 0;
            border-spacing: 0;
            text-align: center;
            margin-top: 20px;
            float: left;
            display: table;
        }
       
        .collapse > tbody > tr {
            display: table-row;
        }
    
        tbody td{
            text-align: center;
            vertical-align: top;
        }
        .collapse th,
        .collapse  td {
            border:1px solid #000000;
            height: 1em;
            width: auto;
            padding: 10px;
            color: #484848;
            font-weight: 600;
        }

        tr td.fix {
            width: 1%;
            padding: 0 20px;
            white-space: nowrap;
        }

        .left {
            text-align: left;
        }
        .right {
            text-align: right;
        }

        .space {

            border-spacing: 10px;

        }

        table.head td.text{
            overflow: hidden;
            position: relative;
        }
        table.head td.text { 
         width: 90%;
         text-align:left; padding:10px 0; 
        }
        table.head td.text:after{
            content: " ....................................................................................................................... ";
            position: absolute;
            padding-left: 5px;
        }
        .orange_bg{
            color:#000000;
            background-color: #1e3a8a;
        }
  

        .header h2.title {
            padding: 25px 0 ;
            color: #1e3a8a;
            text-align: center;
            margin:0;
            font-size: xx-large;
            text-transform: uppercase;
            font-family: 'Times New Roman', Times, serif;
        }
        .sign_area{
            overflow: hidden;
        }
        .signature{
            float: left;
            margin-top: 40px;
            width: 100%;
            text-align: left;
            overflow: hidden;
            position: relative;
        }
        .signature .txt:after{
            content: " ....................................................................................................................... ";
            position: absolute;
            padding-left: 5px;
        }
        .label{
            float: left;
            margin-top: 10px;
            width: 100%;
            text-align: left;
        }
        .header_warapper div{
            float:left;
            width: calc(50% - 10px);
            padding:10px 5px;
            font-size: medium;
            font-weight: bold;
        }
       .header_warapper{
            float: left;
            position: relative;
            width: calc(100% - 2px);
            border:1px solid #000000;
            border-bottom: 0;
       }
       td.title{
        text-align: right;
       }
       td.thankyou{
            padding: 25px 0 ;
            color: #1e3a8a !important;
            text-align: center;
            margin:0;
            font-size: x-large;
            text-transform: capitalize;
            font-family: sans-serif;
       }
    </style>
   
</head>

<body class="font-sans antialiased" style="border-top:0 !important">
    <div class="container">
        <div class="header">
            <img src="{{ asset('storage/images/invoice-header.png') }}" class="header-img" />
            <h2 class="title">Invoice</h2>
        </div>
    
       
        <div class="center" style="margin-top:5px;">
     
            <div class="header_warapper">
                    <div>Attention : {{$isp->isp->name}}</div>
                  
                    <div>Bill Number : {{$isp->tempBill->bill_number}}</div>
                    <div>Address : {{$isp->isp->address}}</div>
                    <div>Invoice No. : </div>
                    
                    <div>Issue Date : {{ date("j F Y", strtotime($isp->issue_date)) }}</div>
                    <div>Description : {{ $isp->tempBill->name }}</div>
                    <div>Due Date : {{ date("j F Y", strtotime($isp->due_date)) }}</div>
                 
                
                        @if ($isp->tempBill->exchange_rate > 1)
                        <div>Exchange Rate : {{$isp->tempBill->exchange_rate}}</div>
                        @endif
                    
                 
                    
                 
            </div>

            <table class="collapse" style="width:100%; ">
                <thead>
                    <tr >
                        <td>No.</td>
                        <td>Description</td>
         
                       
                        <td>QTY</td>
                        <td class="right">Total (MMK)</td>
                    </tr>
                </thead>
                   
                    <tbody>
                        @php 
                        $index =1;
                        @endphp
                    @foreach ($tempInvoices as $tempInvoice )
                        
      
                    <tr>
                        <td>{{ $index++ }}</td>
                        <td class="left"> {{$tempInvoice->category}} </td>
                        
                        <td>{{ $tempInvoice->total_customers }}</td>
                        <td class="right">{{number_format($tempInvoice->total_amount)}}</td>
                   
                    </tr>
                    @endforeach
                    @if($isp->additional_description )
                    <tr>
                        <td>{{ $index++ }}</td>
                        <td class="left"> {{$isp->additional_description}} </td>
                        <td>1</td>
                    
                        <td class="right">{{number_format($isp->additional_fees)}}</td>
                   
                    </tr>
                    @endif
                </tbody>
           
                <tfoot>
                    <tr>
                        <td class="title" colspan="3">Sub Total</td>
                        <td class="text right">{{number_format($isp->sub_total) }}</td>
                    </tr>
                    @if($isp->discount_amount > 0)
                    <tr>
                        <td class="title" colspan="3">Discount</td>
                        <td class="text right">{{number_format($isp->discount_amount, 2, '.')}}</td>
                    </tr>
                    @endif
                    @if($isp->tax_percent > 0)
                    <tr>
                        <td class="title" colspan="3">Commercial Tax ({{$isp->tax_percent}})%</td>
                        <td class="text right">{{number_format(($isp->tax_amount), 2, '.')}}</td>
                    </tr>
                    @endif
                    <tr>
                        <td class="title" colspan="3">Grand Total</td>
                        <td class="text right">{{number_format(($isp->total_amount ), 2, '.')}}</td>
                    </tr>
                    <tr>
                        <td class="thankyou" colspan="4">
                            Thank you For Choosing Our Services.
                        </td>
                    </tr>
                </tfoot>
            </table>
        </div>         
            
         </div>
        <div class="footer">
        <img src="{{ asset('storage/images/invoice-footer.png') }}" class="header-img" />
        </div>
   
</body>

</html>