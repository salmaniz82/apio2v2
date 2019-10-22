<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xmlns:o="urn:schemas-microsoft-com:office:office" xmlns:v="urn:schemas-microsoft-com:vml">
 <head>
  <title>Robust Designs</title>
  <meta content="IE=edge" http-equiv="X-UA-Compatible" />
  <meta content="width=device-width, initial-scale=1" name="viewport" />
  <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
  <meta content="yes" name="apple-mobile-web-app-capable" />
  <meta content="black" name="apple-mobile-web-app-status-bar-style" />
  <meta content="telephone=no" name="format-detection" />

  <!--[if !mso]><!-->
  <meta http-equiv="X-UA-Compatible" content="IE=edge" />
  <!--<![endif]-->

  <!--[if gte mso 9]>
<xml>
<o:OfficeDocumentSettings>
  <o:AllowPNG/>
  <o:PixelsPerInch>96</o:PixelsPerInch>
</o:OfficeDocumentSettings>
</xml>
<![endif]-->
  <!--[if mso]>
<style>
body, 
table tr,
table td, 
a, span, 
table.MsoNormalTable {
  font-family: Helvetica, sans-serif, arial !important; 
}
</style>
<![endif]-->
  <style type="text/css">
 @font-face {
   font-family: 'Noto Sans';
   font-style: normal;
   font-weight: 400;
   src: local('Noto Sans'), local('NotoSans'), url(https://fonts.gstatic.com/s/notosans/v6/LeFlHvsZjXu2c3ZRgBq9nFtXRa8TVwTICgirnJhmVJw.woff2) format('woff2');
   unicode-range: U+0000-00FF, U+0131, U+0152-0153, U+02C6, U+02DA, U+02DC, U+2000-206F, U+2074, U+20AC, U+2212, U+2215;
 }
 @font-face {
   font-family: 'Noto Sans';
   font-style: normal;
   font-weight: 700;
   src: local('Noto Sans Bold'), local('NotoSans-Bold'), url(https://fonts.gstatic.com/s/notosans/v6/PIbvSEyHEdL91QLOQRnZ1-gdm0LZdjqr5-oayXSOefg.woff2) format('woff2');
   unicode-range: U+0000-00FF, U+0131, U+0152-0153, U+02C6, U+02DA, U+02DC, U+2000-206F, U+2074, U+20AC, U+2212, U+2215;
 }
 @font-face {
   font-family: 'Noto Sans';
   font-style: italic;
   font-weight: 400;
   src: local('Noto Sans Italic'), local('NotoSans-Italic'), url(https://fonts.gstatic.com/s/notosans/v6/ByLA_FLEa-16SpQuTcQn4I4P5ICox8Kq3LLUNMylGO4.woff2) format('woff2');
   unicode-range: U+0000-00FF, U+0131, U+0152-0153, U+02C6, U+02DA, U+02DC, U+2000-206F, U+2074, U+20AC, U+2212, U+2215;
 }
 @font-face {
   font-family: 'Montserrat';
   font-style: normal;
   font-weight: 400;
   src: local('Montserrat-Regular'), url(https://fonts.gstatic.com/s/montserrat/v7/zhcz-_WihjSQC0oHJ9TCYPk_vArhqVIZ0nv9q090hN8.woff2) format('woff2');
   unicode-range: U+0000-00FF, U+0131, U+0152-0153, U+02C6, U+02DA, U+02DC, U+2000-206F, U+2074, U+20AC, U+2212, U+2215, U+E0FF, U+EFFD, U+F000;
 }
 @font-face {
   font-family: 'Montserrat';
   font-style: normal;
   font-weight: 700;
   src: local('Montserrat-Bold'), url(https://fonts.gstatic.com/s/montserrat/v7/IQHow_FEYlDC4Gzy_m8fcoWiMMZ7xLd792ULpGE4W_Y.woff2) format('woff2');
   unicode-range: U+0000-00FF, U+0131, U+0152-0153, U+02C6, U+02DA, U+02DC, U+2000-206F, U+2074, U+20AC, U+2212, U+2215, U+E0FF, U+EFFD, U+F000;
 }
 @font-face {
   font-family: 'Open Sans';
   font-style: normal;
   font-weight: 300;
   src: local('Open Sans Light'), local('OpenSans-Light'), url(http://fonts.gstatic.com/s/opensans/v10/DXI1ORHCpsQm3Vp6mXoaTegdm0LZdjqr5-oayXSOefg.woff2) format('woff2'), url(http://fonts.gstatic.com/s/opensans/v10/DXI1ORHCpsQm3Vp6mXoaTXhCUOGz7vYGh680lGh-uXM.woff) format('woff');
 }
 @font-face {
   font-family: 'Open Sans';
   font-style: normal;
   font-weight: 400;
   src: local('Open Sans'), local('OpenSans'), url(http://fonts.gstatic.com/s/opensans/v10/cJZKeOuBrn4kERxqtaUH3VtXRa8TVwTICgirnJhmVJw.woff2) format('woff2'), url(http://fonts.gstatic.com/s/opensans/v10/cJZKeOuBrn4kERxqtaUH3T8E0i7KZn-EPnyo3HZu7kw.woff) format('woff');
 }
 @font-face {
   font-family: 'Open Sans';
   font-style: normal;
   font-weight: 600;
   src: local('Open Sans Semibold'), local('OpenSans-Semibold'), url(http://fonts.gstatic.com/s/opensans/v10/MTP_ySUJH_bn48VBG8sNSugdm0LZdjqr5-oayXSOefg.woff2) format('woff2'), url(http://fonts.gstatic.com/s/opensans/v10/MTP_ySUJH_bn48VBG8sNSnhCUOGz7vYGh680lGh-uXM.woff) format('woff');
 }
 @font-face {
   font-family: 'Open Sans';
   font-style: normal;
   font-weight: 700;
   src: local('Open Sans Bold'), local('OpenSans-Bold'), url(http://fonts.gstatic.com/s/opensans/v10/k3k702ZOKiLJc3WVjuplzOgdm0LZdjqr5-oayXSOefg.woff2) format('woff2'), url(http://fonts.gstatic.com/s/opensans/v10/k3k702ZOKiLJc3WVjuplzHhCUOGz7vYGh680lGh-uXM.woff) format('woff');
 }
 @font-face {
   font-family: 'Open Sans';
   font-style: normal;
   font-weight: 800;
   src: local('Open Sans Extrabold'), local('OpenSans-Extrabold'), url(http://fonts.gstatic.com/s/opensans/v10/EInbV5DfGHOiMmvb1Xr-hugdm0LZdjqr5-oayXSOefg.woff2) format('woff2'), url(http://fonts.gstatic.com/s/opensans/v10/EInbV5DfGHOiMmvb1Xr-hnhCUOGz7vYGh680lGh-uXM.woff) format('woff');
 }
 @font-face {
   font-family: 'Open Sans';
   font-style: italic;
   font-weight: 300;
   src: local('Open Sans Light Italic'), local('OpenSansLight-Italic'), url(http://fonts.gstatic.com/s/opensans/v10/PRmiXeptR36kaC0GEAetxko2lTMeWA_kmIyWrkNCwPc.woff2) format('woff2'), url(http://fonts.gstatic.com/s/opensans/v10/PRmiXeptR36kaC0GEAetxh_xHqYgAV9Bl_ZQbYUxnQU.woff) format('woff');
 }
 @font-face {
   font-family: 'Open Sans';
   font-style: italic;
   font-weight: 400;
   src: local('Open Sans Italic'), local('OpenSans-Italic'), url(http://fonts.gstatic.com/s/opensans/v10/xjAJXh38I15wypJXxuGMBo4P5ICox8Kq3LLUNMylGO4.woff2) format('woff2'), url(http://fonts.gstatic.com/s/opensans/v10/xjAJXh38I15wypJXxuGMBobN6UDyHWBl620a-IRfuBk.woff) format('woff');
 }
 @font-face {
   font-family: 'Open Sans';
   font-style: italic;
   font-weight: 600;
   src: local('Open Sans Semibold Italic'), local('OpenSans-SemiboldItalic'), url(http://fonts.gstatic.com/s/opensans/v10/PRmiXeptR36kaC0GEAetxl2umOyRU7PgRiv8DXcgJjk.woff2) format('woff2'), url(http://fonts.gstatic.com/s/opensans/v10/PRmiXeptR36kaC0GEAetxn5HxGBcBvicCpTp6spHfNo.woff) format('woff');
 }
 @font-face {
   font-family: 'Open Sans';
   font-style: italic;
   font-weight: 700;
   src: local('Open Sans Bold Italic'), local('OpenSans-BoldItalic'), url(http://fonts.gstatic.com/s/opensans/v10/PRmiXeptR36kaC0GEAetxolIZu-HDpmDIZMigmsroc4.woff2) format('woff2'), url(http://fonts.gstatic.com/s/opensans/v10/PRmiXeptR36kaC0GEAetxjqR_3kx9_hJXbbyU8S6IN0.woff) format('woff');
 }
 @font-face {
   font-family: 'Open Sans';
   font-style: italic;
   font-weight: 800;
   src: local('Open Sans Extrabold Italic'), local('OpenSans-ExtraboldItalic'), url(http://fonts.gstatic.com/s/opensans/v10/PRmiXeptR36kaC0GEAetxnibbpXgLHK_uTT48UMyjSM.woff2) format('woff2'), url(http://fonts.gstatic.com/s/opensans/v10/PRmiXeptR36kaC0GEAetxkCDe67GEgBv_HnyvHTfdew.woff) format('woff');
 }
  @font-face {
  font-family: 'Source Sans Pro';
  font-style: normal;
  font-weight: 300;
  src: local('Source Sans Pro Light'), local('SourceSansPro-Light'), url(http://fonts.gstatic.com/s/sourcesanspro/v9/toadOcfmlt9b38dHJxOBGOode0-EuMkY--TSyExeINg.woff2) format('woff2'), url(http://fonts.gstatic.com/s/sourcesanspro/v9/toadOcfmlt9b38dHJxOBGNbE_oMaV8t2eFeISPpzbdE.woff) format('woff');
  }
  @font-face {
  font-family: 'Source Sans Pro';
  font-style: normal;
  font-weight: 400;
  src: local('Source Sans Pro'), local('SourceSansPro-Regular'), url(http://fonts.gstatic.com/s/sourcesanspro/v9/ODelI1aHBYDBqgeIAH2zlNV_2ngZ8dMf8fLgjYEouxg.woff2) format('woff2'), url(http://fonts.gstatic.com/s/sourcesanspro/v9/ODelI1aHBYDBqgeIAH2zlBM0YzuT7MdOe03otPbuUS0.woff) format('woff');
  }
  @font-face {
  font-family: 'Source Sans Pro';
  font-style: normal;
  font-weight: 650;
  src: local('Source Sans Pro Semibold'), local('SourceSansPro-Semibold'), url(http://fonts.gstatic.com/s/sourcesanspro/v9/toadOcfmlt9b38dHJxOBGCOFnW3Jk0f09zW_Yln67Ac.woff2) format('woff2'), url(http://fonts.gstatic.com/s/sourcesanspro/v9/toadOcfmlt9b38dHJxOBGJ6-ys_j0H4QL65VLqzI3wI.woff) format('woff');
  }
  @font-face {
  font-family: 'Source Sans Pro';
  font-style: normal;
  font-weight: 700;
  src: local('Source Sans Pro Bold'), local('SourceSansPro-Bold'), url(http://fonts.gstatic.com/s/sourcesanspro/v9/toadOcfmlt9b38dHJxOBGEo0As1BFRXtCDhS66znb_k.woff2) format('woff2'), url(http://fonts.gstatic.com/s/sourcesanspro/v9/toadOcfmlt9b38dHJxOBGFkQc6VGVFSmCnC_l7QZG60.woff) format('woff');
  }
  @font-face {
  font-family: 'Source Sans Pro';
  font-style: normal;
  font-weight: 900;
  src: local('Source Sans Pro Black'), local('SourceSansPro-Black'), url(http://fonts.gstatic.com/s/sourcesanspro/v9/toadOcfmlt9b38dHJxOBGHZhYM0_6AejPZE-OqA592o.woff2) format('woff2'), url(http://fonts.gstatic.com/s/sourcesanspro/v9/toadOcfmlt9b38dHJxOBGHiec-hVyr2k4iOzEQsW1iE.woff) format('woff');
  }
 .ReadMsgBody {width: 100%; background-color: #f2f2f2;}
 .ExternalClass {width: 100%; background-color: #f2f2f2;}
 body { background-color: #f2f2f2;  -webkit-font-smoothing: antialiased; }
 table { border-collapse:collapse; mso-table-lspace:0pt; mso-table-rspace:0pt; }
 img{-ms-interpolation-mode:bicubic;}
 p {margin-bottom:0; margin:0}
 a[x-apple-data-detectors] {
   color:inherit !important;
   text-decoration:none !important;
   font-size:inherit !important;
   font-family:inherit !important;
   font-weight:inherit !important;
   line-height:inherit !important;
 }
 @media only screen and (max-width: 640px)  {
         body[yahoo] .contenttable,
         * [lang~="x-content"] { width:440px !important; padding:0; }
         body[yahoo] .center { text-align: center !important; }
         table[class=tablemobile],
         * [lang~="x-tablem"] {
         width: 100% !important;
         min-width: 100% !important;
         display: table !important;
         height:auto;
         }
         img[class=imgfix],
         * [lang~="x-normage"] {
         width: 100% !important;
         min-width: 100%;
         height: auto;
         }
         img[class=smallimg],
         * [lang~="x-simage"] {
         width: 50% !important;
         height: auto;
         }
         img[class=medimg],
         * [lang~="x-mimage"] {
         width: 60% !important;
         height: auto;
         }
     }
     
 @media only screen and (max-width: 479px) {
         body[yahoo] .contenttable,
         * [lang~="x-content"] { width:280px !important; padding:0; }
         body[yahoo] .center { text-align: center !important; }
         table[class=tablemobile],
         * [lang~="x-tablem"] {
         width: 100% !important;
         min-width: 100% !important;
         display: table !important;
         height:auto;
         }
         img[class=imgfix],
         * [lang~="x-normage"] {
         width: 100% !important;
         min-width: 100%;
         height: auto;
         }
         img[class=smallimg],
         * [lang~="x-simage"] {
         width: 40% !important;
         height: auto;
         }
         img[class=medimg],
         * [lang~="x-mimage"] {
         width: 50% !important;
         height: auto;
         }
     }

 font {
   font-family: 'Montserrat', 'Gotham Rounded', 'Gotham', 'Noto Sans', 'Open Sans', 'Source Sans Pro', 'Segoe UI','Segoe UI Web Regular','Segoe UI Symbol','Helvetica Neue',Arial,sans-serif;
   -webkit-text-size-adjust:100%;
   -ms-text-size-adjust:100%;text-size-adjust:100%;
 }
</style>
 </head>
 <body bgcolor="#f2f2f2" style="background-color: #f2f2f2; font-family: 'Open Sans', 'Source Sans Pro', 'Segoe UI','Segoe UI Web Regular','Segoe UI Symbol','Helvetica Neue','BBAlpha Sans','S60 Sans',Arial,sans-serif; margin: 0px; padding: 0px;" yahoo="fix" leftmargin="0" marginwidth="0" topmargin="0" marginheight="0" offset="0">
  <div style="background-color: #f2f2f2; width: 100%;">
   <!--[if gte mso 9]>
   <v:background xmlns:v="urn:schemas-microsoft-com:vml" fill="true" stroke="false">
   <v:fill type="tile" src="" color="#f2f2f2"/>
   </v:background>
  <![endif]-->
   <table align="center" border="0" cellpadding="0" cellspacing="0" height="100%" style="border-collapse: collapse; min-width: 100% !important; mso-table-lspace: 0pt; mso-table-rspace: 0pt; width: 100%;" width="100%">
    <tr>
     <td align="center" bgcolor="f2f2f2" valign="top" style="background-color: #f2f2f2;">
      <!--BackgroundColor Fix Start-->
      <table align="center" border="0" cellpadding="20" cellspacing="0" class="contenttable" lang="x-content" style="border-collapse: collapse; min-width: 280px; mso-table-lspace: 0pt; mso-table-rspace: 0pt; width: 600px;" width="600">
       <tbody>
        <tr>
         <td align="center" style="color: #b0b0b0; font-size: 13px; font-weight: 300; line-height: 18px; padding: 20px 20px 20px 20px;"><font color="#b0b0b0" size="2" style="font-family: 'Open Sans', 'Source Sans Pro', sans-serif; font-weight: 400; text-size-adjust: 100%;"> Having trouble viewing in your inbox? <a href="#" style="text-decoration: none;">View this email in your browser.</a> </font></td>
        </tr>
       </tbody>
      </table>
      <table align="center" bgcolor="#dddddd" border="0" cellpadding="1" cellspacing="0" style="-moz-box-shadow: 0px 0px 5px #dddddd; -webkit-box-shadow: 0px 0px 5px #dddddd; border-collapse: collapse; box-shadow: 0px 0px 5px #dddddd; mso-table-lspace: 0pt; mso-table-rspace: 0pt;">
       <tbody>
        <tr>
         <td align="center" style="padding-bottom: 1px; padding-left: 1px; padding-right: 1px; padding-top: 1px;">
          <!--Shadow Start-->
          <table align="center" bgcolor="#2196f3" border="0" cellpadding="0" cellspacing="0" class="contenttable" lang="x-content" style="background-color: #2196f3; border-collapse: collapse; mso-table-lspace: 0pt; mso-table-rspace: 0pt;" width="600">
           <tbody>
            <tr>
             <td background="https://robust.email/files/template_2/images/bg-shape.png" bgcolor="#2196f3" height="auto" style="background-image: url('https://robust.email/files/template_2/images/bg-shape.png'); background-position: top center; background-repeat: no-repeat; padding-bottom: 0px; padding-left: 0px; padding-right: 0px; padding-top: 0px;" valign="top" width="600">
              <!--[if gte mso 9]>
             <v:rect xmlns:v="urn:schemas-microsoft-com:vml" fill="true" stroke="false" style="width:600px;height:445px;">
               <v:fill type="tile" src="https://robust.email/files/template_2/images/bg-shape.png" color="#2196f3" />
               <v:textbox inset="0,0,0,0">
             <![endif]-->
              <div>
               <!--Background Start-->
               <table align="center" bgcolor="transparent" border="0" cellpadding="20" cellspacing="0" class="contenttable" lang="x-content" style="border-collapse: collapse; min-width: 280px; mso-table-lspace: 0pt; mso-table-rspace: 0pt; width: 600px;" width="600">
                <tbody>
                 <tr>
                  <td align="center" height="20" style="color: transparent; font-size: 1px; height: 20px; line-height: 20px; padding: 0px 0px 0px 0px;"> &nbsp; </td>
                 </tr>
                 <tr>
                  <td align="center" style="color: #ffffff; font-size: 16px; font-weight: 400; line-height: 20px; padding: 0px 0px 0px 20px; text-transform: uppercase;"><font color="#ffffff" size="3" style="font-family: 'Montserrat', 'Gotham Rounded', 'Gotham', 'Noto Sans', 'Open Sans', 'Source Sans Pro', 'Segoe UI','Segoe UI Web Regular','Segoe UI Symbol','Helvetica Neue',Arial,sans-serif; text-size-adjust: 100%; text-transform: uppercase;"> Please update your address <br />book with the details below </font></td>
                 </tr>
                 <tr>
                  <td align="center" height="10" style="color: transparent; font-size: 1px; height: 10px; line-height: 10px; padding: 0px 0px 0px 0px;"> &nbsp; </td>
                 </tr>
                </tbody>
               </table>
               <table align="center" bgcolor="transparent" border="0" cellpadding="0" cellspacing="0" class="contenttable" lang="x-content" style="border-collapse: collapse; min-width: 280px; mso-table-lspace: 0pt; mso-table-rspace: 0pt; width: 600px;" width="600">
                <tbody>
                 <tr>
                  <td align="center" style="padding-bottom: 0px; padding-left: 0px; padding-right: 0px; padding-top: 10px;" valign="middle">
                   <table align="center" bgcolor="#ffffff" cellpadding="0" cellspacing="0" class="tablemobile" lang="x-tablem" style="-moz-box-shadow: 0px 0px 5px #dddddd; -webkit-box-shadow: 0px 0px 5px #dddddd; background-color: #ffffff; border-collapse: collapse; box-shadow: 0px 0px 5px #dddddd; mso-table-lspace: 0pt; mso-table-rspace: 0pt;" width="550">
                    <tbody>
                     <tr>
                      <td align="center" style="padding-bottom: 0px; padding-left: 0px; padding-right: 0px; padding-top: 0px;" valign="middle">
                       <table align="left" border="0" cellpadding="20" cellspacing="0" class="tablemobile" lang="x-tablem" style="border-collapse: collapse; mso-table-lspace: 0pt; mso-table-rspace: 0pt;" width="190">
                        <tbody>
                         <tr>
                          <td align="center" height="25" style="color: transparent; font-size: 1px; height: 25px; line-height: 25px; padding: 0px 0px 0px 0px;"> &nbsp; </td>
                         </tr>
                         <tr>
                          <td align="center" style="padding-bottom: 0px; padding-left: 0px; padding-right: 0px; padding-top: 0px;"><img alt="" border="0" height="150" src="https://robust.email/files/template_2/images/circle.jpg" style="display: block; height: 150px; max-width: 150px; width: 150px;" width="150" /></td>
                         </tr>
                         <tr>
                          <td align="center" height="20" style="color: transparent; font-size: 1px; height: 20px; line-height: 20px; padding: 0px 0px 0px 0px;"> &nbsp; </td>
                         </tr>
                        </tbody>
                       </table>
                        <!--[if (gte mso 9)|(IE)]>
                        </td>
                        <td align="center" valign="top" style="padding-top:0;padding-bottom:0;padding-right:0px;padding-left:0px;">
                        <![endif]-->
                       <table align="right" border="0" cellpadding="20" cellspacing="0" class="tablemobile" lang="x-tablem" style="border-collapse: collapse; mso-table-lspace: 0pt; mso-table-rspace: 0pt;" width="360">
                        <tbody>
                         <tr>
                          <td align="center" height="20" style="color: transparent; font-size: 1px; height: 20px; line-height: 20px; padding: 0px 0px 0px 0px;"> &nbsp; </td>
                         </tr>
                         <tr>
                          <td align="center" style="padding-bottom: 0px; padding-left: 10px; padding-right: 10px; padding-top: 0px;" valign="middle">
                           <table align="center" border="0" cellpadding="0" cellspacing="0" class="tablemobile" lang="x-tablem" style="border-collapse: collapse; mso-table-lspace: 0pt; mso-table-rspace: 0pt;" width="340">
                            <tbody>
                             <tr>
                              <td align="left" style="color: #777777; font-size: 16px; line-height: 20px; padding-bottom: 0px; padding-left: 0px; padding-right: 0px; padding-top: 0px; text-align: left;"><font color="#5c6bc0" size="3" style="font-family: 'Montserrat', 'Gotham Rounded', 'Gotham', 'Noto Sans', 'Open Sans', 'Source Sans Pro', 'Segoe UI','Segoe UI Web Regular','Segoe UI Symbol','Helvetica Neue',Arial,sans-serif; text-size-adjust: 100%;"> BEN MALLAH </font><br /><font color="#777777" size="2" style="font-family: 'Montserrat', 'Gotham Rounded', 'Gotham', 'Noto Sans', 'Open Sans', 'Source Sans Pro', 'Segoe UI','Segoe UI Web Regular','Segoe UI Symbol','Helvetica Neue',Arial,sans-serif; text-size-adjust: 100%;"> Real Estate Tycoon </font></td>
                             </tr>
                             <tr>
                              <td align="center" style="padding-bottom: 0px; padding-left: 0px; padding-right: 0px; padding-top: 10px;" valign="middle">
                               <table align="left" border="0" cellpadding="0" cellspacing="0" class="tablemobile" lang="x-tablem" style="border-collapse: collapse; mso-table-lspace: 0pt; mso-table-rspace: 0pt;" width="330">
                                <tbody>
                                 <tr>
                                  <td align="center" bgcolor="#dddddd" height="1" style="color: transparent; font-size: 1px; height: 1px; line-height: 1px; padding-bottom: 0px; padding-left: 0px; padding-right: 0px; padding-top: 0px;"> &nbsp; </td>
                                 </tr>
                                </tbody>
                               </table>
                              </td>
                             </tr>
                             <tr>
                              <td align="center" height="10" style="color: transparent; font-size: 1px; height: 10px; line-height: 10px; padding: 0px 0px 0px 0px;"> &nbsp; </td>
                             </tr>
                             <tr>
                              <td align="left" style="padding-bottom: 10px; padding-left: 0px; padding-right: 0px; padding-top: 0px;" valign="top">
                               <table align="left" border="0" cellpadding="0" cellspacing="0" class="tablemobile" lang="x-tablem" style="border-collapse: collapse; mso-table-lspace: 0pt; mso-table-rspace: 0pt;">
                                <tbody>
                                 <tr>
                                  <td align="center" style="padding-bottom: 0px; padding-left: 0px; padding-right: 0px; padding-top: 0px;" valign="top">
                                   <table align="left" border="0" cellpadding="0" cellspacing="0" class="tablemobile" lang="x-tablem" style="border-collapse: collapse; mso-table-lspace: 0pt; mso-table-rspace: 0pt;">
                                    <tbody>
                                     <tr>
                                      <td align="left" style="color: #333333; font-size: 13px; line-height: 18px; padding-bottom: 10px; padding-left: 0px; padding-right: 0px; padding-top: 0px;" valign="top"><font color="#b9bec2" size="2" style="font-family: 'Montserrat', 'Gotham Rounded', 'Gotham', 'Noto Sans', 'Open Sans', 'Source Sans Pro', 'Segoe UI','Segoe UI Web Regular','Segoe UI Symbol','Helvetica Neue',Arial,sans-serif; text-size-adjust: 100%; text-transform: uppercase;"> Phone </font><br /><font color="#333333" size="2" style="font-family: 'Montserrat', 'Gotham Rounded', 'Gotham', 'Noto Sans', 'Open Sans', 'Source Sans Pro', 'Segoe UI','Segoe UI Web Regular','Segoe UI Symbol','Helvetica Neue',Arial,sans-serif; text-size-adjust: 100%;"> <a href="tel:+100612769" style="color: #333333; text-decoration: none;">+100-612-769</a><br /><a href="tel:+100516261" style="color: #333333; text-decoration: none;">+100-516-261</a> </font></td>
                                     </tr>
                                     <tr>
                                      <td align="left" style="color: #333333; font-size: 13px; line-height: 18px; padding-bottom: 10px; padding-left: 0px; padding-right: 0px; padding-top: 0px;" valign="top"><font color="#b9bec2" size="2" style="font-family: 'Montserrat', 'Gotham Rounded', 'Gotham', 'Noto Sans', 'Open Sans', 'Source Sans Pro', 'Segoe UI','Segoe UI Web Regular','Segoe UI Symbol','Helvetica Neue',Arial,sans-serif; text-size-adjust: 100%; text-transform: uppercase;"> Email </font><br /><font color="#333333" size="2" style="font-family: 'Montserrat', 'Gotham Rounded', 'Gotham', 'Noto Sans', 'Open Sans', 'Source Sans Pro', 'Segoe UI','Segoe UI Web Regular','Segoe UI Symbol','Helvetica Neue',Arial,sans-serif; text-size-adjust: 100%;"> <a href="mailto:benjamin@mallah.com" style="color: #333333; text-decoration: none;">benjamin@mallah.com</a> </font></td>
                                     </tr>
                                    </tbody>
                                   </table>
                                  </td>
                                  <td align="center" style="color: transparent; font-size: 1px; line-height: 100%; padding: 0px 0px 0px 0px; width: 20px;" width="20"> &nbsp; </td>
                                  <td align="left" style="padding-bottom: 0px; padding-left: 0px; padding-right: 0px; padding-top: 0px;" valign="top">
                                   <table align="left" border="0" cellpadding="0" cellspacing="0" class="tablemobile" lang="x-tablem" style="border-collapse: collapse; mso-table-lspace: 0pt; mso-table-rspace: 0pt;">
                                    <tbody>
                                     <tr>
                                      <td align="left" style="color: #333333; font-size: 13px; line-height: 18px; padding-bottom: 10px; padding-left: 0px; padding-right: 0px; padding-top: 0px;" valign="top"><font color="#b9bec2" size="2" style="font-family: 'Montserrat', 'Gotham Rounded', 'Gotham', 'Noto Sans', 'Open Sans', 'Source Sans Pro', 'Segoe UI','Segoe UI Web Regular','Segoe UI Symbol','Helvetica Neue',Arial,sans-serif; text-size-adjust: 100%; text-transform: uppercase;"> Address </font><br /><font color="#333333" size="2" style="font-family: 'Montserrat', 'Gotham Rounded', 'Gotham', 'Noto Sans', 'Open Sans', 'Source Sans Pro', 'Segoe UI','Segoe UI Web Regular','Segoe UI Symbol','Helvetica Neue',Arial,sans-serif; text-size-adjust: 100%;"> 23rd Street, Markway Road, Dublinhill CA </font></td>
                                     </tr>
                                     <tr>
                                      <td align="left" style="color: #333333; font-size: 13px; line-height: 18px; padding-bottom: 10px; padding-left: 0px; padding-right: 0px; padding-top: 0px;" valign="top"><font color="#b9bec2" size="2" style="font-family: 'Montserrat', 'Gotham Rounded', 'Gotham', 'Noto Sans', 'Open Sans', 'Source Sans Pro', 'Segoe UI','Segoe UI Web Regular','Segoe UI Symbol','Helvetica Neue',Arial,sans-serif; text-size-adjust: 100%; text-transform: uppercase;"> Website </font><br /><font color="#333333" size="2" style="font-family: 'Montserrat', 'Gotham Rounded', 'Gotham', 'Noto Sans', 'Open Sans', 'Source Sans Pro', 'Segoe UI','Segoe UI Web Regular','Segoe UI Symbol','Helvetica Neue',Arial,sans-serif; text-size-adjust: 100%;"> <a href="http://lifeforsale.net" style="color: #333333; text-decoration: none;">lifeforsale.net</a> </font></td>
                                     </tr>
                                    </tbody>
                                   </table>
                                  </td>
                                 </tr>
                                </tbody>
                               </table>
                              </td>
                             </tr>
                            </tbody>
                           </table>
                          </td>
                         </tr>
                        </tbody>
                       </table>
                      </td>
                     </tr>
                    </tbody>
                   </table>
                  </td>
                 </tr>
                </tbody>
               </table>
               <table align="center" bgcolor="transparent" border="0" cellpadding="0" cellspacing="0" class="contenttable" lang="x-content" style="border-collapse: collapse; min-width: 280px; mso-table-lspace: 0pt; mso-table-rspace: 0pt; width: 600px;" width="600">
                <tbody>
                 <tr>
                  <td align="center" height="20" style="color: transparent; font-size: 1px; height: 20px; line-height: 20px; padding-bottom: 0px; padding-left: 0px; padding-right: 0px; padding-top: 0px;"> &nbsp; </td>
                 </tr>
                </tbody>
               </table>
               <table align="center" bgcolor="transparent" border="0" cellpadding="0" cellspacing="0" class="contenttable" lang="x-content" style="border-collapse: collapse; min-width: 280px; mso-table-lspace: 0pt; mso-table-rspace: 0pt; width: 600px;" width="600">
                <tbody>
                 <tr>
                  <td align="center" style="padding-bottom: 5px; padding-left: 0px; padding-right: 0px; padding-top: 0px;">
                   <table align="center" bgcolor="#ffffff" cellpadding="20" cellspacing="0" class="tablemobile" lang="x-tablem" style="-moz-box-shadow: 0px 0px 5px #dddddd; -webkit-box-shadow: 0px 0px 5px #dddddd; background-color: #ffffff; border-collapse: collapse; box-shadow: 0px 0px 5px #dddddd; mso-table-lspace: 0pt; mso-table-rspace: 0pt;" width="550">
                    <tbody>
                     <tr>
                      <td align="center" height="20" style="color: transparent; font-size: 1px; height: 20px; line-height: 20px; padding: 0px 0px 0px 0px;"> &nbsp; </td>
                     </tr>
                     <tr>
                      <td align="left" style="color: #333333; font-size: 13px; line-height: 18px; padding-bottom: 0px; padding-left: 20px; padding-right: 20px; padding-top: 0px; text-align: left;"><font color="#333333" size="2" style="font-family: 'Noto Sans', 'Open Sans', 'Source Sans Pro', sans-serif; font-weight: 400; text-size-adjust: 100%;"> Mr. Benjamin Mallah, the Managing Member of Equity Management, has had extensive experience throughout his twenty-five year career in the ownership. </font></td>
                     </tr>
                     <tr>
                      <td align="center" height="20" style="color: transparent; font-size: 1px; height: 20px; line-height: 20px; padding: 0px 0px 0px 0px;"> &nbsp; </td>
                     </tr>
                    </tbody>
                   </table>
                  </td>
                 </tr>
                </tbody>
               </table>
               <table align="center" bgcolor="#f1fbfb" border="0" cellpadding="0" cellspacing="0" class="contenttable" lang="x-content" style="background-color: #f1fbfb; border-collapse: collapse; min-width: 280px; mso-table-lspace: 0pt; mso-table-rspace: 0pt; width: 600px;" width="600">
                <tbody>
                 <tr>
                  <td align="center" height="20" style="color: transparent; font-size: 1px; height: 20px; line-height: 20px; padding: 0px 0px 0px 0px;"> &nbsp; </td>
                 </tr>
                 <tr>
                  <td align="center" style="padding-bottom: 0px; padding-left: 20px; padding-right: 20px; padding-top: 0px;">
                   <table align="center" border="0" cellpadding="0" cellspacing="0" class="tablemobile" lang="x-tablem" style="border-collapse: collapse; mso-table-lspace: 0pt; mso-table-rspace: 0pt;" width="550">
                    <tbody>
                     <tr>
                      <td align="center" style="padding-bottom: 0px; padding-left: 0px; padding-right: 0px; padding-top: 0px;">
                       <table align="left" border="0" cellpadding="0" cellspacing="0" class="tablemobile" lang="x-tablem" style="border-collapse: collapse; mso-table-lspace: 0pt; mso-table-rspace: 0pt;">
                        <tbody>
                         <tr>
                          <td align="center" editable="social" style="font-size: 12px; line-height: 100%; padding-bottom: 0px; padding-left: 0px; padding-right: 0px; padding-top: 0px;"><a href="#" style="color: inherit; text-decoration: none;"><img alt="twitter" border="0" height="16" src="https://robust.email/files/template_2/images/twitter-color.gif" style="" width="16" /></a>&nbsp;&nbsp;&nbsp;<a href="#" style="color: inherit; text-decoration: none;"><img alt="youtube" border="0" height="16" src="https://robust.email/files/template_2/images/youtube-color.gif" style="" width="16" /></a>&nbsp;&nbsp;&nbsp;<a href="#" style="color: inherit; text-decoration: none;"><img alt="facebook" border="0" height="16" src="https://robust.email/files/template_2/images/facebook-color.gif" style="" width="16" /></a>&nbsp;&nbsp;&nbsp;<a href="#" style="color: inherit; text-decoration: none;"><img alt="googleplus" border="0" height="16" src="https://robust.email/files/template_2/images/googleplus-color.gif" style="" width="16" /></a>&nbsp;&nbsp;&nbsp;<a href="#" style="color: inherit; text-decoration: none;"><img alt="linkedin" border="0" height="16" src="https://robust.email/files/template_2/images/linkedin-color.gif" style="" width="16" /></a>&nbsp;&nbsp;&nbsp;<a href="#" style="color: inherit; text-decoration: none;"><img alt="pinterest" border="0" height="16" src="https://robust.email/files/template_2/images/pinterest-color.gif" style="" width="16" /></a>&nbsp;&nbsp;&nbsp;<a href="#" style="color: inherit; text-decoration: none;"><img alt="rss" border="0" height="16" src="https://robust.email/files/template_2/images/rss-color.gif" style="" width="16" /></a></td>
                         </tr>
                        </tbody>
                       </table>
                       <table align="left" border="0" cellpadding="0" cellspacing="0" class="tablemobile" lang="x-tablem" style="border-collapse: collapse; mso-table-lspace: 0pt; mso-table-rspace: 0pt;">
                        <tbody>
                         <tr>
                          <td align="center" height="20" style="color: transparent; font-size: 1px; height: 20px; line-height: 20px; padding-bottom: 0px; padding-left: 0px; padding-right: 0px; padding-top: 0px; width: 10px;" width="10"> &nbsp; </td>
                         </tr>
                        </tbody>
                       </table>
                       <table align="right" border="0" cellpadding="0" cellspacing="0" class="tablemobile" lang="x-tablem" style="border-collapse: collapse; mso-table-lspace: 0pt; mso-table-rspace: 0pt;">
                        <tbody>
                         <tr>
                          <td align="center" style="color: #777777; font-size: 12px; line-height: 18px; padding-bottom: 0px; padding-left: 0px; padding-right: 0px; padding-top: 0px;"><font color="#777777" size="2" style="font-family: 'Montserrat', 'Gotham Rounded', 'Gotham', 'Noto Sans', 'Open Sans', 'Source Sans Pro', 'Segoe UI','Segoe UI Web Regular','Segoe UI Symbol','Helvetica Neue',Arial,sans-serif; font-size: 12px; text-size-adjust: 100%; text-transform: uppercase;"><a href="#" style="color: #777777; text-decoration: none;">Privacy</a>&nbsp;&nbsp;/&nbsp;&nbsp;<a href="#" style="color: #777777; text-decoration: none;">Contact</a> </font></td>
                         </tr>
                        </tbody>
                       </table>
                      </td>
                     </tr>
                    </tbody>
                   </table>
                  </td>
                 </tr>
                 <tr>
                  <td align="center" height="20" style="color: transparent; font-size: 1px; height: 20px; line-height: 20px; padding: 0px 0px 0px 0px;"> &nbsp; </td>
                 </tr>
                </tbody>
               </table>
               <!--Background End-->
              </div>
              <!--[if gte mso 9]>
               </v:textbox>
             </v:rect>
             <![endif]-->
             </td>
            </tr>
           </tbody>
          </table>
          <!--Shadow End-->
         </td>
        </tr>
       </tbody>
      </table>
      <table align="center" bgcolor="transparent" border="0" cellpadding="0" cellspacing="0" class="contenttable" lang="x-content" style="border-collapse: collapse; min-width: 280px; mso-table-lspace: 0pt; mso-table-rspace: 0pt; width: 600px;" width="600">
       <tbody>
        <tr>
         <td align="center" style="color: #b0b0b0; font-size: 13px; font-weight: 400; line-height: 18px; padding: 20px 30px 20px 30px;"><font color="#b0b0b0" size="2" style="font-family: 'Open Sans', 'Source Sans Pro', sans-serif; font-weight: 400; text-size-adjust: 100%;"> <a href="#" style="text-decoration: none;">unsubscribe</a> to no longer receive posts from us <br /> Copyright&copy; 2016 Robust Designs LLC </font></td>
        </tr>
       </tbody>
      </table>
      <!--Gmail Font Size Fix Start-->
      <div style="color: #eaeaea; display: none; font: 15px courier; white-space: nowrap;"> - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - </div>
      <!--Gmail Font Size Fix End-->
      <!--BackgroundColor Fix End-->
     </td>
    </tr>
   </table>
  </div>
 </body>
</html>
