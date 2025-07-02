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
            <h2 class="title">Receipt</h2>
        </div>
    
       
        <div class="center" style="margin-top:5px;">
     
                <table class="header"  style="width:100%; border-collapse: collapse;">
                    <tr>
                        <td style="text-align: left; font-weight: bold;">ISP :</td>
                        <td style="text-align: left;">{{ $receipt->isp->name }}</td>
                        <td style="text-align: right; font-weight: bold;">Receipt No. :</td>
                        <td style="text-align: right;">{{ $receipt->receiptRecord->receipt_number }}</td>
                    </tr>
                    <tr>
                        <td style="text-align: left; font-weight: bold;">Attention :</td>
                        <td style="text-align: left;">{{ $receipt->isp->contact_person }}</td>
                        <td style="text-align: right; font-weight: bold;">Issue Date :</td>
                        <td style="text-align: right;">{{ date("j F Y", strtotime($receipt->issue_date)) }}</td>
                    </tr>
                  
                    <tr>
                        <td style="text-align: left; font-weight: bold;">Description :</td>
                        <td style="text-align: left;">{{ $receipt->bill->name }}</td>
                        <td style="text-align: right; font-weight: bold;">Due Date :</td>
                        <td style="text-align: right;">{{ date("j F Y", strtotime($receipt->due_date)) }}</td>
                    </tr>
                    <tr>
                        <td style="text-align: left; font-weight: bold;">Address :</td>
                        <td colspan="3" style="text-align: left;">{{ $receipt->isp->address }}</td>
                    </tr>
                </table>
                    
                 
    

            <table class="collapse" style="width:100%; ">
                <thead>
                    <tr>
                        <td>No.</td>
                        <td>Description</td>
                   
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
                          
                            <td >{{ $invoiceItem->total_customers }}</td>
                            <td style="text-align: right;">{{ number_format($invoiceItem->total_amount) }}</td>
                        </tr>
                    @endforeach
           
                    @if($receipt->additional_description )
                    <tr>
                        <td>{{ $index++ }}</td>
                        <td class="left"> {{$receipt->additional_description}} </td>
                
                        <td>1</td>
                        <td  style="text-align: right;">{{number_format($receipt->additional_fees)}}</td>
                   
                    </tr>
                    @endif
                </tbody>
           
                <tfoot>
                    <tr>
                        <td class="title" colspan="3">Sub Total</td>
                        <td class="text right">{{ number_format($receipt->sub_total) }}</td>
                    </tr>
                    @if($receipt->discount_amount > 0)
                    <tr>
                        <td class="title" colspan="3">Discount</td>
                        <td class="text right">{{ number_format($receipt->discount_amount, 2, '.') }}</td>
                    </tr>
                    @endif
                    @if($receipt->tax_percent > 0)
                    <tr>
                        <td class="title" colspan="3">Commercial Tax ({{$receipt->tax_percent}})%</td>
                        <td class="text right">{{ number_format(($receipt->tax_amount), 2, '.') }}</td>
                    </tr>
                    @endif
                    <tr>
                        <td class="title" colspan="3">Grand Total</td>
                        <td class="text right">{{ number_format(($receipt->total_amount), 2, '.') }}</td>
                    </tr>
                </tfoot>
            </table>
            <table class="collapse" style="width:100%; margin-top: 30px;">
                <tr>
                    <td class="title" colspan="3">Collected Amount</td>
                    <td class="text right">{{ number_format($receipt->receiptRecord->collected_amount, 2, '.') }}</td>
                </tr>
                <tr>
                    <td class="title" colspan="3">Payment Status</td>
                    <td class="text right">
                        @if($receipt->receiptRecord->collected_amount >= $receipt->total_amount)
                            <span style="color: #059669;">Paid</span>
                        @elseif($receipt->receiptRecord->collected_amount > 0)
                            <span style="color: #d97706;">Partially Paid</span>
                        @else
                            <span style="color: #dc2626;">Unpaid</span>
                        @endif
                    </td>
                </tr>
                <tr>
                    <td class="title" colspan="3">Collected Person</td>
                    <td class="text right">{{ $receipt->receiptRecord->collectedPerson->name }}</td>
                </tr>
            </table>
            
            
         </div>
        
   
</body>

</html>