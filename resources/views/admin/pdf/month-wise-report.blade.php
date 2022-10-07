<!DOCTYPE  html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">

<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <title>Monthwise Report (LNSEL-RPT)</title>
    <style type="text/css">
        * {
            margin: 0;
            padding: 0;
            text-indent: 0;
        }
        
        h1 {
            color: black;
            font-family: Verdana, sans-serif;
            font-style: normal;
            font-weight: bold;
            text-decoration: none;
            font-size: 8pt;
        }
        
        .s1 {
            color: black;
            font-family: "Times New Roman", serif;
            font-style: normal;
            font-weight: normal;
            text-decoration: none;
            font-size: 8pt;
        }
        
        .s2 {
            color: black;
            font-family: Verdana, sans-serif;
            font-style: normal;
            font-weight: normal;
            text-decoration: none;
            font-size: 7.5pt;
        }
        
        .s3 {
            color: black;
            font-family: "Times New Roman", serif;
            font-style: normal;
            font-weight: normal;
            text-decoration: none;
            font-size: 7.5pt;
        }
        
        .h2 {
            color: black;
            font-family: Verdana, sans-serif;
            font-style: normal;
            font-weight: bold;
            text-decoration: none;
            font-size: 7.5pt;
        }
        
        .s4 {
            color: black;
            font-family: Verdana, sans-serif;
            font-style: normal;
            font-weight: bold;
            text-decoration: none;
            font-size: 6.5pt;
        }
        
        .s5 {
            color: black;
            font-family: Verdana, sans-serif;
            font-style: normal;
            font-weight: bold;
            text-decoration: none;
            font-size: 7.5pt;
        }
        
        .s6 {
            color: black;
            font-family: "Times New Roman", serif;
            font-style: normal;
            font-weight: normal;
            text-decoration: none;
            font-size: 7.5pt;
        }
        
        .s7 {
            color: black;
            font-family: Verdana, sans-serif;
            font-style: normal;
            font-weight: bold;
            text-decoration: none;
            font-size: 5.5pt;
        }
        
        .s8 {
            color: black;
            font-family: Verdana, sans-serif;
            font-style: normal;
            font-weight: normal;
            text-decoration: none;
            font-size: 5.5pt;
        }
        
        p {
            color: black;
            font-family: Verdana, sans-serif;
            font-style: normal;
            font-weight: normal;
            text-decoration: none;
            font-size: 5.5pt;
            margin: 0pt;
        }
        
        .s9 {
            color: black;
            font-family: "Times New Roman", serif;
            font-style: normal;
            font-weight: normal;
            text-decoration: none;
            font-size: 5.5pt;
        }
        
        table,
        tbody {
            vertical-align: top;
            overflow: visible;
        }
    </style>
</head>

<body>
    <h1 style="padding-top: 3pt;padding-left: 11pt;text-indent: 0pt;text-align: left;">Daily Receipts and Sales</h1>
    <p class="s3" style="padding-top: 1pt;padding-left: 11pt;text-indent: 0pt;text-align: left;"><span class="s2">Name of Licence : BAZIMAT F.L. (OFF) SHOP</span></p>
    <p class="s3" style="padding-top: 2pt;padding-bottom: 2pt;padding-left: 11pt;text-indent: 0pt;text-align: left;"><span class="s2">For The Month of : Sep-2022</span></p>
    <table style="border-collapse:collapse;margin-left:5.8pt" cellspacing="0">
        <tr style="height:23pt">
            <td style="width:48pt;border-top-style:solid;border-top-width:1pt;border-left-style:solid;border-left-width:1pt;border-bottom-style:solid;border-bottom-width:1pt;border-right-style:solid;border-right-width:1pt" rowspan="2">
                <p class="s4" style="padding-top: 6pt;padding-left: 14pt;text-indent: 0pt;text-align: left;">Date</p>
            </td>
            @forelse ($categories as $category)
                <td style="width:240pt;border-top-style:solid;border-top-width:1pt;border-left-style:solid;border-left-width:1pt;border-bottom-style:solid;border-bottom-width:1pt;border-right-style:solid;border-right-width:1pt" colspan="4">
                    <p class="s5" style="padding-top: 6pt;padding-left: 55pt;text-indent: 0pt;text-align: left;">{{$category->name}} Bulk Litr.</p>
                </td>
            @empty
                
            @endforelse
           {{--  <td style="width:240pt;border-top-style:solid;border-top-width:1pt;border-left-style:solid;border-left-width:1pt;border-bottom-style:solid;border-bottom-width:1pt;border-right-style:solid;border-right-width:1pt" colspan="4">
                <p class="s5" style="padding-top: 6pt;padding-left: 55pt;text-indent: 0pt;text-align: left;">Foreign Liquor Bulk Litr.</p>
            </td>
            <td style="width:240pt;border-top-style:solid;border-top-width:1pt;border-left-style:solid;border-left-width:1pt;border-bottom-style:solid;border-bottom-width:1pt;border-right-style:solid;border-right-width:1pt" colspan="4">
                <p class="s5" style="padding-top: 6pt;padding-left: 55pt;text-indent: 0pt;text-align: left;">Country Spirits Bulk Litr.</p>
            </td>
            <td style="width:240pt;border-top-style:solid;border-top-width:1pt;border-left-style:solid;border-left-width:1pt;border-bottom-style:solid;border-bottom-width:1pt;border-right-style:solid;border-right-width:1pt" colspan="4">
                <p class="s5" style="padding-top: 6pt;padding-left: 82pt;padding-right: 95pt;text-indent: 0pt;text-align: center;">Beer Bulk Litr.</p>
            </td> --}}
        </tr>
        <tr style="height:14pt">
            @forelse ( $categories as $category )
                <td style="width:60pt;border-top-style:solid;border-top-width:1pt;border-left-style:solid;border-left-width:1pt;border-bottom-style:solid;border-bottom-width:1pt;border-right-style:solid;border-right-width:1pt">
                    <p class="s7" style="padding-top: 4pt;padding-left: 15pt;text-indent: 0pt;text-align: left;">Opening</p>
                </td>
                <td style="width:60pt;border-top-style:solid;border-top-width:1pt;border-left-style:solid;border-left-width:1pt;border-bottom-style:solid;border-bottom-width:1pt;border-right-style:solid;border-right-width:1pt">
                    <p class="s7" style="padding-top: 4pt;padding-left: 14pt;text-indent: 0pt;text-align: left;">Purchase</p>
                </td>
                <td style="width:60pt;border-top-style:solid;border-top-width:1pt;border-left-style:solid;border-left-width:1pt;border-bottom-style:solid;border-bottom-width:1pt;border-right-style:solid;border-right-width:1pt">
                    <p class="s7" style="padding-top: 4pt;padding-left: 22pt;padding-right: 22pt;text-indent: 0pt;text-align: center;">Sale</p>
                </td>
                <td style="width:60pt;border-top-style:solid;border-top-width:1pt;border-left-style:solid;border-left-width:1pt;border-bottom-style:solid;border-bottom-width:1pt;border-right-style:solid;border-right-width:1pt">
                    <p class="s7" style="padding-top: 4pt;padding-left: 17pt;text-indent: 0pt;text-align: left;">Closing</p>
                </td>
            @empty
                
            @endforelse
            
            
        </tr>
        <tr style="height:13pt">
            <td style="width:48pt;border-top-style:solid;border-top-width:1pt;border-left-style:solid;border-left-width:1pt;border-bottom-style:solid;border-bottom-width:1pt;border-right-style:solid;border-right-width:1pt">
                <p class="s8" style="padding-top: 3pt;padding-right: 7pt;text-indent: 0pt;text-align: right;">01-Sep-22</p>
            </td>
            <td style="width:60pt;border-top-style:solid;border-top-width:1pt;border-left-style:solid;border-left-width:1pt;border-bottom-style:solid;border-bottom-width:1pt;border-right-style:solid;border-right-width:1pt">
                <p class="s8" style="padding-top: 3pt;padding-right: 5pt;text-indent: 0pt;text-align: right;">3136.415</p>
            </td>
            <td style="width:60pt;border-top-style:solid;border-top-width:1pt;border-left-style:solid;border-left-width:1pt;border-bottom-style:solid;border-bottom-width:1pt;border-right-style:solid;border-right-width:1pt">
                <p class="s8" style="padding-top: 3pt;padding-right: 5pt;text-indent: 0pt;text-align: right;">0.000</p>
            </td>
            <td style="width:60pt;border-top-style:solid;border-top-width:1pt;border-left-style:solid;border-left-width:1pt;border-bottom-style:solid;border-bottom-width:1pt;border-right-style:solid;border-right-width:1pt">
                <p class="s8" style="padding-top: 3pt;padding-right: 5pt;text-indent: 0pt;text-align: right;">181.175</p>
            </td>
            <td style="width:60pt;border-top-style:solid;border-top-width:1pt;border-left-style:solid;border-left-width:1pt;border-bottom-style:solid;border-bottom-width:1pt;border-right-style:solid;border-right-width:1pt">
                <p class="s8" style="padding-top: 3pt;padding-right: 5pt;text-indent: 0pt;text-align: right;">2955.240</p>
            </td>
            <td style="width:60pt;border-top-style:solid;border-top-width:1pt;border-left-style:solid;border-left-width:1pt;border-bottom-style:solid;border-bottom-width:1pt;border-right-style:solid;border-right-width:1pt">
                <p class="s8" style="padding-top: 3pt;padding-right: 5pt;text-indent: 0pt;text-align: right;">2365.200</p>
            </td>
            <td style="width:60pt;border-top-style:solid;border-top-width:1pt;border-left-style:solid;border-left-width:1pt;border-bottom-style:solid;border-bottom-width:1pt;border-right-style:solid;border-right-width:1pt">
                <p class="s8" style="padding-top: 3pt;padding-right: 5pt;text-indent: 0pt;text-align: right;">1200.000</p>
            </td>
            <td style="width:60pt;border-top-style:solid;border-top-width:1pt;border-left-style:solid;border-left-width:1pt;border-bottom-style:solid;border-bottom-width:1pt;border-right-style:solid;border-right-width:1pt">
                <p class="s8" style="padding-top: 3pt;padding-right: 5pt;text-indent: 0pt;text-align: right;">598.200</p>
            </td>
            <td style="width:60pt;border-top-style:solid;border-top-width:1pt;border-left-style:solid;border-left-width:1pt;border-bottom-style:solid;border-bottom-width:1pt;border-right-style:solid;border-right-width:1pt">
                <p class="s8" style="padding-top: 3pt;padding-right: 5pt;text-indent: 0pt;text-align: right;">2967.000</p>
            </td>
            <td style="width:60pt;border-top-style:solid;border-top-width:1pt;border-left-style:solid;border-left-width:1pt;border-bottom-style:solid;border-bottom-width:1pt;border-right-style:solid;border-right-width:1pt">
                <p class="s8" style="padding-top: 3pt;padding-right: 5pt;text-indent: 0pt;text-align: right;">3828.990</p>
            </td>
            <td style="width:60pt;border-top-style:solid;border-top-width:1pt;border-left-style:solid;border-left-width:1pt;border-bottom-style:solid;border-bottom-width:1pt;border-right-style:solid;border-right-width:1pt">
                <p class="s8" style="padding-top: 3pt;padding-right: 5pt;text-indent: 0pt;text-align: right;">0.000</p>
            </td>
            <td style="width:60pt;border-top-style:solid;border-top-width:1pt;border-left-style:solid;border-left-width:1pt;border-bottom-style:solid;border-bottom-width:1pt;border-right-style:solid;border-right-width:1pt">
                <p class="s8" style="padding-top: 3pt;padding-right: 5pt;text-indent: 0pt;text-align: right;">428.080</p>
            </td>
            <td style="width:60pt;border-top-style:solid;border-top-width:1pt;border-left-style:solid;border-left-width:1pt;border-bottom-style:solid;border-bottom-width:1pt;border-right-style:solid;border-right-width:1pt">
                <p class="s8" style="padding-top: 3pt;padding-right: 5pt;text-indent: 0pt;text-align: right;">3400.910</p>
            </td>
            <td style="width:60pt;border-top-style:solid;border-top-width:1pt;border-left-style:solid;border-left-width:1pt;border-bottom-style:solid;border-bottom-width:1pt;border-right-style:solid;border-right-width:1pt">
                <p class="s8" style="padding-top: 3pt;padding-right: 5pt;text-indent: 0pt;text-align: right;">3828.990</p>
            </td>
            <td style="width:60pt;border-top-style:solid;border-top-width:1pt;border-left-style:solid;border-left-width:1pt;border-bottom-style:solid;border-bottom-width:1pt;border-right-style:solid;border-right-width:1pt">
                <p class="s8" style="padding-top: 3pt;padding-right: 5pt;text-indent: 0pt;text-align: right;">0.000</p>
            </td>
            <td style="width:60pt;border-top-style:solid;border-top-width:1pt;border-left-style:solid;border-left-width:1pt;border-bottom-style:solid;border-bottom-width:1pt;border-right-style:solid;border-right-width:1pt">
                <p class="s8" style="padding-top: 3pt;padding-right: 5pt;text-indent: 0pt;text-align: right;">428.080</p>
            </td>
            <td style="width:60pt;border-top-style:solid;border-top-width:1pt;border-left-style:solid;border-left-width:1pt;border-bottom-style:solid;border-bottom-width:1pt;border-right-style:solid;border-right-width:1pt">
                <p class="s8" style="padding-top: 3pt;padding-right: 5pt;text-indent: 0pt;text-align: right;">3400.910</p>
            </td>
           
            
        </tr>
        <tr style="height:13pt">
            <td style="width:48pt;border-top-style:solid;border-top-width:1pt;border-left-style:solid;border-left-width:1pt;border-bottom-style:solid;border-bottom-width:1pt;border-right-style:solid;border-right-width:1pt">
                <p class="s8" style="padding-top: 3pt;padding-right: 7pt;text-indent: 0pt;text-align: right;">01-Sep-22</p>
            </td>
            <td style="width:60pt;border-top-style:solid;border-top-width:1pt;border-left-style:solid;border-left-width:1pt;border-bottom-style:solid;border-bottom-width:1pt;border-right-style:solid;border-right-width:1pt">
                <p class="s8" style="padding-top: 3pt;padding-right: 5pt;text-indent: 0pt;text-align: right;">3136.415</p>
            </td>
            <td style="width:60pt;border-top-style:solid;border-top-width:1pt;border-left-style:solid;border-left-width:1pt;border-bottom-style:solid;border-bottom-width:1pt;border-right-style:solid;border-right-width:1pt">
                <p class="s8" style="padding-top: 3pt;padding-right: 5pt;text-indent: 0pt;text-align: right;">0.000</p>
            </td>
            <td style="width:60pt;border-top-style:solid;border-top-width:1pt;border-left-style:solid;border-left-width:1pt;border-bottom-style:solid;border-bottom-width:1pt;border-right-style:solid;border-right-width:1pt">
                <p class="s8" style="padding-top: 3pt;padding-right: 5pt;text-indent: 0pt;text-align: right;">181.175</p>
            </td>
            <td style="width:60pt;border-top-style:solid;border-top-width:1pt;border-left-style:solid;border-left-width:1pt;border-bottom-style:solid;border-bottom-width:1pt;border-right-style:solid;border-right-width:1pt">
                <p class="s8" style="padding-top: 3pt;padding-right: 5pt;text-indent: 0pt;text-align: right;">2955.240</p>
            </td>
            <td style="width:60pt;border-top-style:solid;border-top-width:1pt;border-left-style:solid;border-left-width:1pt;border-bottom-style:solid;border-bottom-width:1pt;border-right-style:solid;border-right-width:1pt">
                <p class="s8" style="padding-top: 3pt;padding-right: 5pt;text-indent: 0pt;text-align: right;">2365.200</p>
            </td>
            <td style="width:60pt;border-top-style:solid;border-top-width:1pt;border-left-style:solid;border-left-width:1pt;border-bottom-style:solid;border-bottom-width:1pt;border-right-style:solid;border-right-width:1pt">
                <p class="s8" style="padding-top: 3pt;padding-right: 5pt;text-indent: 0pt;text-align: right;">1200.000</p>
            </td>
            <td style="width:60pt;border-top-style:solid;border-top-width:1pt;border-left-style:solid;border-left-width:1pt;border-bottom-style:solid;border-bottom-width:1pt;border-right-style:solid;border-right-width:1pt">
                <p class="s8" style="padding-top: 3pt;padding-right: 5pt;text-indent: 0pt;text-align: right;">598.200</p>
            </td>
            <td style="width:60pt;border-top-style:solid;border-top-width:1pt;border-left-style:solid;border-left-width:1pt;border-bottom-style:solid;border-bottom-width:1pt;border-right-style:solid;border-right-width:1pt">
                <p class="s8" style="padding-top: 3pt;padding-right: 5pt;text-indent: 0pt;text-align: right;">2967.000</p>
            </td>
            <td style="width:60pt;border-top-style:solid;border-top-width:1pt;border-left-style:solid;border-left-width:1pt;border-bottom-style:solid;border-bottom-width:1pt;border-right-style:solid;border-right-width:1pt">
                <p class="s8" style="padding-top: 3pt;padding-right: 5pt;text-indent: 0pt;text-align: right;">3828.990</p>
            </td>
            <td style="width:60pt;border-top-style:solid;border-top-width:1pt;border-left-style:solid;border-left-width:1pt;border-bottom-style:solid;border-bottom-width:1pt;border-right-style:solid;border-right-width:1pt">
                <p class="s8" style="padding-top: 3pt;padding-right: 5pt;text-indent: 0pt;text-align: right;">0.000</p>
            </td>
            <td style="width:60pt;border-top-style:solid;border-top-width:1pt;border-left-style:solid;border-left-width:1pt;border-bottom-style:solid;border-bottom-width:1pt;border-right-style:solid;border-right-width:1pt">
                <p class="s8" style="padding-top: 3pt;padding-right: 5pt;text-indent: 0pt;text-align: right;">428.080</p>
            </td>
            <td style="width:60pt;border-top-style:solid;border-top-width:1pt;border-left-style:solid;border-left-width:1pt;border-bottom-style:solid;border-bottom-width:1pt;border-right-style:solid;border-right-width:1pt">
                <p class="s8" style="padding-top: 3pt;padding-right: 5pt;text-indent: 0pt;text-align: right;">3400.910</p>
            </td>
            <td style="width:60pt;border-top-style:solid;border-top-width:1pt;border-left-style:solid;border-left-width:1pt;border-bottom-style:solid;border-bottom-width:1pt;border-right-style:solid;border-right-width:1pt">
                <p class="s8" style="padding-top: 3pt;padding-right: 5pt;text-indent: 0pt;text-align: right;">3828.990</p>
            </td>
            <td style="width:60pt;border-top-style:solid;border-top-width:1pt;border-left-style:solid;border-left-width:1pt;border-bottom-style:solid;border-bottom-width:1pt;border-right-style:solid;border-right-width:1pt">
                <p class="s8" style="padding-top: 3pt;padding-right: 5pt;text-indent: 0pt;text-align: right;">0.000</p>
            </td>
            <td style="width:60pt;border-top-style:solid;border-top-width:1pt;border-left-style:solid;border-left-width:1pt;border-bottom-style:solid;border-bottom-width:1pt;border-right-style:solid;border-right-width:1pt">
                <p class="s8" style="padding-top: 3pt;padding-right: 5pt;text-indent: 0pt;text-align: right;">428.080</p>
            </td>
            <td style="width:60pt;border-top-style:solid;border-top-width:1pt;border-left-style:solid;border-left-width:1pt;border-bottom-style:solid;border-bottom-width:1pt;border-right-style:solid;border-right-width:1pt">
                <p class="s8" style="padding-top: 3pt;padding-right: 5pt;text-indent: 0pt;text-align: right;">3400.910</p>
            </td>     
        </tr>

    </table>
    <p style="text-indent: 0pt;text-align: left;"><br/></p>
    <p style="padding-left: 5pt;text-indent: 0pt;line-height: 1pt;text-align: left;" />
    <p style="padding-top: 1pt;padding-left: 11pt;text-indent: 0pt;text-align: left;">Printed<span class="s9"> </span>On<span class="s9"> </span>:<span class="s9"> </span>12-Sep-22<span class="s9"> </span>At<span class="s9"> </span>:<span class="s9"> </span>4:08:50PM</p>
    <p style="padding-top: 1pt;padding-left: 11pt;text-indent: 0pt;text-align: left;">Generated<span class="s9"> </span>By<span class="s9"> </span>SAP</p>
    <!-- <p style="padding-left: 11pt;text-indent: 0pt;text-align: left;">Page<span class="s9"> </span>1<span class="s9"> </span>of<span class="s9"> </span>1</p> -->
</body>

</html>