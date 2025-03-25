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
        table.header {
        width: 100%;
        }

        table.header td {
           
            padding: 5px;
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
            float:right;
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
            <img src="{{ public_path('storage/images/invoice-header.png') }}" class="header-img" />
            <h2 class="title">Invoice</h2>
        </div>
    
       
        <div class="center" style="margin-top:5px;">
     
                <table class="header"  style="width:100%; border-collapse: collapse;">
                    <tr>
                        <td style="text-align: left; font-weight: bold;">ISP :</td>
                        <td style="text-align: left;">{{ $invoice->isp->name }}</td>
                        <td style="text-align: right; font-weight: bold;">Invoice No. :</td>
                        <td style="text-align: right;">{{ $invoice->invoice_number }}</td>
                    </tr>
                    <tr>
                        <td style="text-align: left; font-weight: bold;">Attention :</td>
                        <td style="text-align: left;">{{ $invoice->isp->contact_person }}</td>
                        <td style="text-align: right; font-weight: bold;">Issue Date :</td>
                        <td style="text-align: right;">{{ date("j F Y", strtotime($invoice->issue_date)) }}</td>
                    </tr>
                  
                    <tr>
                        <td style="text-align: left; font-weight: bold;">Description :</td>
                        <td style="text-align: left;">{{ $invoice->bill->name }}</td>
                        <td style="text-align: right; font-weight: bold;">Due Date :</td>
                        <td style="text-align: right;">{{ date("j F Y", strtotime($invoice->due_date)) }}</td>
                    </tr>
                    <tr>
                        <td style="text-align: left; font-weight: bold;">Address :</td>
                        <td colspan="3" style="text-align: left;">{{ $invoice->isp->address }}</td>
                    </tr>
                </table>
                    
                 
    

            <table class="collapse" style="width:100%; ">
                <thead>
                    <tr>
                        <td>No.</td>
                        <td>Description</td>
                        <td>Unit Price (MMK)</td>
                        <td>QTY</td>
                        <td>Total (MMK)</td>
                    </tr>
                </thead>
                   
                    <tbody>
                        @php 
                        $index = 1;
                    @endphp
                    @foreach ($invoiceItems as $invoiceItem)
                        <tr>
                            <td>{{ $index++ }}</td>
                            <td class="left"> {{$invoiceItem->category}} </td>
                            <td style="text-align: right;">{{ number_format($invoiceItem->unit_price) }}</td>
                            <td >{{ $invoiceItem->total_customers }}</td>
                            <td style="text-align: right;">{{ number_format($invoiceItem->total_amount) }}</td>
                        </tr>
                    @endforeach
           
                    @if($invoice->additional_description )
                    <tr>
                        <td>{{ $index++ }}</td>
                        <td class="left"> {{$invoice->additional_description}} </td>
                        <td  style="text-align: right;">{{$invoice->additional_fees}}</td>
                        <td>1</td>
                        <td  style="text-align: right;">{{number_format($invoice->additional_fees)}}</td>
                   
                    </tr>
                    @endif
                </tbody>
           
                <tfoot>
                    <tr>
                        <td class="title" colspan="4">Sub Total</td>
                        <td class="text right">{{ number_format($invoice->sub_total) }}</td>
                    </tr>
                    @if($invoice->discount_amount > 0)
                    <tr>
                        <td class="title" colspan="4">Discount</td>
                        <td class="text right">{{ number_format($invoice->discount_amount, 2, '.') }}</td>
                    </tr>
                    @endif
                    @if($invoice->tax_percent > 0)
                    <tr>
                        <td class="title" colspan="4">Commercial Tax ({{$invoice->tax_percent}})%</td>
                        <td class="text right">{{ number_format(($invoice->tax_amount), 2, '.') }}</td>
                    </tr>
                    @endif
                    <tr>
                        <td class="title" colspan="4">Grand Total</td>
                        <td class="text right">{{ number_format(($invoice->total_amount), 2, '.') }}</td>
                    </tr>
                </tfoot>
            </table>
            
            
         </div>
        
   
</body>

</html>