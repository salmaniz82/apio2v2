<?php $user = $data['users'] ?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xmlns:o="urn:schemas-microsoft-com:office:office" xmlns:v="urn:schemas-microsoft-com:vml">
 <head>
  <title><?= $data['title']?></title>
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

  <div style="height: 50px">&nbsp;</div>


   <table align="center" border="0" cellpadding="0" cellspacing="0" height="100%" style="border-collapse: collapse; min-width: 100% !important; mso-table-lspace: 0pt; mso-table-rspace: 0pt; width: 100%;" width="100%">
    <tr>
     <td align="center" bgcolor="f2f2f2" valign="top" style="background-color: #f2f2f2;">
      <!--BackgroundColor Fix Start-->
      <table align="center" border="0" cellpadding="20" cellspacing="0" class="contenttable" lang="x-content" style="border-collapse: collapse; min-width: 280px; mso-table-lspace: 0pt; mso-table-rspace: 0pt; width: 500px;" width="500">
       <tbody style="margin-top: 50px; ">
        
       </tbody>
      </table>
      <table align="center" bgcolor="#dddddd" border="0" cellpadding="0" cellspacing="0" style="-moz-box-shadow: 0px 0px 5px #dddddd; -webkit-box-shadow: 0px 0px 5px #dddddd; border-collapse: collapse; box-shadow: 0px 0px 5px #dddddd; mso-table-lspace: 0pt; mso-table-rspace: 0pt;">
       <tbody>
        <tr>
         <td align="center">
          <!--Shadow Start-->
          <table align="center" bgcolor="ffffff" border="0" cellpadding="0" cellspacing="0" class="contenttable" lang="x-content" style="background-color: #ffffff; border-collapse: collapse; min-width: 280px; mso-table-lspace: 0pt; mso-table-rspace: 0pt; width: 500px;" width="500">
           <tbody>
           <tr>
              <td align="center" height="20" style="color: transparent; font-size: 1px; height: 20px; line-height: 20px; padding: 0px 0px 0px 0px;"> &nbsp; </td>
            </tr>
            <tr>
             <td align="center" style="padding-bottom: 0px; padding-left: 0px; padding-right: 0px; padding-top: 0px; text-align: center;">
              <table border='0' class='tablemobile' lang='x-tablem' cellpadding='0' cellspacing='0' align='center' style="border-collapse: collapse; mso-table-lspace: 0pt; mso-table-rspace: 0pt; width: 500px" width="500">
              <tbody>
              <tr>
              <td align='center' style="padding-top: 0px; padding-bottom: 0px; padding-left: 20px; padding-right: 20px; line-height: 100%;">
                  <table align="left" border="0" cellpadding="0" cellspacing="0" class="tablemobile" lang="x-tablem" style="border-collapse: collapse; mso-table-lspace: 0pt; mso-table-rspace: 0pt; direction: ltr; vertical-align: top; display: inline-block;">
                   <tbody>
                    <tr>
                     <td align="center" style="line-height: 100%; padding-bottom: 0px; padding-left: 0px; padding-right: 0px; padding-top: 0px;">

                      <a href="#" style="color: inherit; text-decoration: none;"> 

                      <!--  

                      <img alt="" border="0" height="21" src="https://robust.email/files/template_2/images/logo.png" style="display: block; max-width: 220px; min-width: 150px; height: 21px" width="180" /> 

                    -->

                      <img width="200" height="23" src="https://api.iskillmetrics.com/assets/images/iskillmetrics-logo-email.png">

                    </a>


                    </td>
                    </tr>
                   </tbody>
                  </table>
                  
                  <!--[if !mso]><!-->
                  <table align="left" border="0" cellpadding="0" cellspacing="0" class="tablemobile" lang="x-tablem" style="border-collapse: collapse; mso-table-lspace: 0pt; mso-table-rspace: 0pt;">
                   <tbody>
                    <tr>
                     <td align="center" height="20" style="color: transparent; font-size: 1px; height: 20px; line-height: 20px; padding-bottom: 0px; padding-left: 0px; padding-right: 0px; padding-top: 0px; width: 10px;" width="10"> &nbsp; </td>
                    </tr>
                   </tbody>
                  </table>
                  <!--<![endif]-->
                  <!--[if (gte mso 9)|(IE)]>
                  </td>
                  <td align="center" valign="top" style="padding-top:0;padding-bottom:0;padding-right:20px;padding-left:0px;">
                  <![endif]-->

                  <table align="right" border="0" cellpadding="0" cellspacing="0" class="tablemobile" lang="x-tablem" style="border-collapse: collapse; mso-table-lspace: 0pt; mso-table-rspace: 0pt; direction: ltr; vertical-align: top; display: inline-block;">
                   <tbody>
                    <tr>
                     <td align="center" style="padding-bottom: 0px; padding-left: 0px; padding-right: 0px; padding-top: 0px;">
                      <table align="center" border="0" cellpadding="0" cellspacing="0" style="border-collapse: collapse; mso-table-lspace: 0pt; mso-table-rspace: 0pt;">
                       <tbody>
                        <tr>
                         <td align="center" style="color: #777777; font-size: 12px; line-height: 18px; padding-bottom: 0px; padding-left: 0px; padding-right: 0px; padding-top: 0px;"><a href="#" style="text-decoration: none; color: #777777;"><font color="#777777" size="2" style="font-family: 'Montserrat', 'Gotham Rounded', 'Gotham', 'Noto Sans', 'Open Sans', 'Source Sans Pro', 'Segoe UI','Segoe UI Web Regular','Segoe UI Symbol','Helvetica Neue',Arial,sans-serif; font-size: 12px; text-size-adjust: 100%; text-transform: uppercase;">  Status </font></a></td>
                         <td align="center" style="padding-bottom: 0px; padding-left: 10px; padding-right: 0px; padding-top: 0px;">
                          <table align="center" bgcolor="#5c6bc0" border="0" cellpadding="0" cellspacing="0" style="border-collapse: collapse; mso-table-lspace: 0pt; mso-table-rspace: 0pt;">
                           <tbody>
                            <tr>
                             <td align="center" style="background-color: <?php echo ($user['status'] == 1) ? '#3db862;' : 'red;' ?>  color: #ffffff; font-size: 12px; line-height: 16px; padding-bottom: 3px; padding-left: 8px; padding-right: 8px; padding-top: 3px;"><a href="#" style="color: #ffffff; text-decoration: none;"> <font color="#ffffff" size="2" style="font-family: 'Montserrat', 'Gotham Rounded', 'Gotham', 'Noto Sans', 'Open Sans', 'Source Sans Pro', 'Segoe UI','Segoe UI Web Regular','Segoe UI Symbol','Helvetica Neue',Arial,sans-serif; font-size: 12px; text-size-adjust: 100%; text-transform: uppercase;"> <?php echo ($user['status'] == 1) ? 'Active' : 'InActive' ?> </font> </a></td>
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
                  
                  <!--[if !mso]><!-->
                  <table align="right" border="0" cellpadding="0" cellspacing="0" class="tablemobile" lang="x-tablem" style="border-collapse: collapse; mso-table-lspace: 0pt; mso-table-rspace: 0pt; direction: ltr; vertical-align: top; display: inline-block;">
                   <tbody>
                    <tr>
                     <td align="center" height="20" style="color: transparent; font-size: 0px; height: 20px; line-height: 20px; padding-bottom: 0px; padding-left: 0px; padding-right: 0px; padding-top: 0px; width: 1px;" width="1"> &nbsp; </td>
                    </tr>
                   </tbody>
                  </table>
                  <!--<![endif]-->
                </td>
                </tr>
               </tbody>
              </table>
             </td>
            </tr>
           </tbody>
          </table>
          <table bgcolor="#ffffff" align="center" border="0" cellpadding="0" cellspacing="0" class="contenttable" lang="x-content" style="background-color: #ffffff; border-collapse: collapse; min-width: 280px; mso-table-lspace: 0pt; mso-table-rspace: 0pt; width: 500px;" width="500">
           <tbody>
           <tr>
              <td align="center" height="10" style="color: transparent; font-size: 1px; height: 10px; line-height: 10px; padding: 0px 0px 0px 0px;"> &nbsp; </td>
            </tr>
            <tr>
             <td align="center" style="padding-bottom: 0px; padding-left: 0px; padding-right: 0px; padding-top: 0px;">
              <table align="center" border="0" cellpadding="0" cellspacing="0" style="border-collapse: collapse; mso-table-lspace: 0pt; mso-table-rspace: 0pt;">
               <tbody>
                <tr>
                <td align="center" style="padding-top: 0px; padding-bottom: 0px; padding-left: 0px; padding-right: 0px;">
                  <table align="center" bgcolor="#dddddd" border="0" cellpadding="0" cellspacing="0" class="tablemobile" height="1" lang="x-tablem" style="background-color: #dddddd; border-collapse: collapse; mso-table-lspace: 0pt; mso-table-rspace: 0pt; min-width: 80px; width: 210px;" width="210">
                   <tbody>
                    <tr>
                     <td align="center" bgcolor="#dddddd" height="1" style="color: transparent; font-size: 0px; height: 1px; line-height: 1px; padding-bottom: 0px; padding-left: 0px; padding-right: 0px; padding-top: 0px;">&nbsp;</td>
                    </tr>
                   </tbody>
                  </table>
                </td>
                 <td align="center" style="line-height: 100%; padding-bottom: 0px; padding-left: 20px; padding-right: 20px; padding-top: 0px;"><img alt="" border="0" height="40" src="https://robust.email/files/template_2/images/check-mark4.png" style="display: block; height: 40px; width: 40px;" width="40" /></td>
                 <td align="center" style="padding-top: 0px; padding-bottom: 0px; padding-left: 0px; padding-right: 0px;">
                  <table align="center" bgcolor="#dddddd" border="0" cellpadding="0" cellspacing="0" class="tablemobile" height="1" lang="x-tablem" style="background-color: #dddddd; border-collapse: collapse; mso-table-lspace: 0pt; mso-table-rspace: 0pt; min-width: 80px; width: 210px;" width="210">
                   <tbody>
                    <tr>
                     <td align="center" bgcolor="#dddddd" height="1" style="color: transparent; font-size: 0px; height: 1px; line-height: 1px; padding-bottom: 0px; padding-left: 0px; padding-right: 0px; padding-top: 0px;">&nbsp;</td>
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
          <table align="center" bgcolor="#ffffff" border="0" cellpadding="0" cellspacing="0" class="contenttable" lang="x-content" style="border-collapse: collapse; min-width: 280px; mso-table-lspace: 0pt; mso-table-rspace: 0pt; width: 500px;" width="500">
           <tbody>
            <tr>
             <td align="center" style="padding-bottom: 0px; padding-left: 20px; padding-right: 20px; padding-top: 0px;">
              <table align="center" cellpadding="0" cellspacing="0" class="tablemobile" lang="x-tablem" style="border-collapse: collapse; mso-table-lspace: 0pt; mso-table-rspace: 0pt;" width="400">
               <tbody>
                <tr>
                 <td align="center" height="20" style="color: transparent; font-size: 1px; height: 20px; line-height: 20px; padding: 0px 0px 0px 0px;"> &nbsp; </td>
                </tr>
                <tr>
                 <td align="center" style="color: #5c6bc0; font-size: 16px; line-height: 20px; padding-bottom: 0px; padding-left: 0px; padding-right: 0px; padding-top: 0px;"><font color="#5c6bc0" size="3" style="font-family: 'Montserrat', 'Gotham Rounded', 'Gotham', 'Noto Sans', 'Open Sans', 'Source Sans Pro', 'Segoe UI','Segoe UI Web Regular','Segoe UI Symbol','Helvetica Neue',Arial,sans-serif; text-size-adjust: 100%; text-transform: uppercase;"> Thanks for signing up! <br /> Here are your credentials </font></td>
                </tr>
               </tbody>
              </table>
             </td>
            </tr>
           </tbody>
          </table>
          <table align="center" bgcolor="#ffffff" border="0" cellpadding="0" cellspacing="0" class="contenttable" lang="x-content" style="border-collapse: collapse; min-width: 280px; mso-table-lspace: 0pt; mso-table-rspace: 0pt; width: 500px;" width="500">
           <tbody>
            <tr>
             <td align="center" height="20" style="color: transparent; font-size: 1px; height: 20px; line-height: 20px; padding: 0px 0px 0px 0px;"> &nbsp; </td>
            </tr>
            <tr>
             <td align="center" style="padding-bottom: 5px; padding-left: 20px; padding-right: 20px; padding-top: 0px;">
              <table align="center" bgcolor="#f5f5f5" border="0" cellpadding="0" cellspacing="0" class="tablemobile" lang="x-tablem" style="border-collapse: collapse; border-radius: 5px; box-shadow: 0px 0px 5px #dddddd; mso-table-lspace: 0pt; mso-table-rspace: 0pt;" width="300">
               <tbody>
                <tr>
                 <td align="center" height="20" style="color: transparent; font-size: 1px; height: 20px; line-height: 20px; padding: 0px 0px 0px 0px;"> &nbsp; </td>
                </tr>
                <tr>
                 <td align="center" style="padding-bottom: 0px; padding-left: 20px; padding-right: 20px; padding-top: 0px;">
                  <table align="center" border="0" cellpadding="0" cellspacing="0" class="tablemobile" lang="x-tablem" style="border-collapse: collapse; mso-table-lspace: 0pt; mso-table-rspace: 0pt;">
                   <tbody>
                    <tr>
                     <td align="center" bgcolor="#68829E" height="30" valign="middle" style="height: 30px; line-height: 100%; padding-bottom: 2px; padding-left: 10px; padding-right: 10px; padding-top: 2px;"><img alt="" border="0" height="12" src="https://robust.email/files/template_2/images/email-icon0.png" style="height: 12px; width: 16px;" width="16" /></td>
                     <td align="left" bgcolor="#ffffff" height="30" style="color: #777777; font-size: 13px; height: 30px; line-height: 16px; padding-bottom: 2px; padding-left: 15px; padding-right: 15px; padding-top: 2px;"><font color="#777777" size="2" style="font-family: 'Noto Sans', 'Open Sans', sans-serif; text-size-adjust: 100%;"> <a href="mailto:username@host.com" style="color: #777777; text-decoration: none;">
                       <?= $user['email']?>
                     </a> </font></td>
                    </tr>
                    <tr>
                     <td align="center" height="5" style="color: transparent; font-size: 1px; height: 5px; line-height: 5px; padding: 0px 0px 0px 0px;"> &nbsp; </td>
                    </tr>
                    <tr>
                     <td align="center" bgcolor="#68829E" valign="middle" height="30" style="height: 30px; line-height: 100%; padding-bottom: 2px; padding-left: 15px; padding-right: 15px; padding-top: 2px;"><img alt="" border="0" height="15" src="https://robust.email/files/template_2/images/lock_0.png" style="height: 15px; width: 12px;" width="12" /></td>
                     <td align="left" bgcolor="#ffffff" height="30" style="color: #777777; font-size: 13px; height: 30px; line-height: 16px; padding-bottom: 2px; padding-left: 15px; padding-right: 15px; padding-top: 2px;"><font color="#777777" size="2" style="font-family: 'Noto Sans', 'Open Sans', sans-serif; text-size-adjust: 100%;"> <?= $user['ticket']?> </font></td>
                    </tr>
                   </tbody>
                  </table>
                 </td>
                </tr>
                <tr>
                 <td align="center" height="20" style="color: transparent; font-size: 1px; height: 20px; line-height: 20px; padding: 0px 0px 0px 0px;"> &nbsp; </td>
                </tr>
                <tr>
                 <td align="center" style="color: #777777; font-size: 13px; line-height: 18px; padding-bottom: 0px; padding-left: 20px; padding-right: 20px; padding-top: 0px;"><font color="#777777" size="2" style="font-family: 'Noto Sans', 'Open Sans', sans-serif; text-size-adjust: 100%;"> You may use these logins for <br />your account on <a href="https://alpha.iskillmetrics.com/login">iskillmetrics.com</a> </font></td>
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
          <table align="center" bgcolor="#ffffff" border="0" cellpadding="0" cellspacing="0" class="contenttable" lang="x-content" style="border-collapse: collapse; min-width: 280px; mso-table-lspace: 0pt; mso-table-rspace: 0pt; width: 500px;" width="500">
           <tbody>
            <tr>
             <td align="center" height="20" style="color: transparent; font-size: 1px; height: 20px; line-height: 20px; padding: 0px 0px 0px 0px;"> &nbsp; </td>
            </tr>
           </tbody>
          </table>
          <table align="center" bgcolor="#ffffff" border="0" cellpadding="0" cellspacing="0" class="contenttable" lang="x-content" style="border-collapse: collapse; min-width: 280px; mso-table-lspace: 0pt; mso-table-rspace: 0pt; width: 500px;" width="500">
           <tbody>
            <tr>
             <td align="center" style="padding-bottom: 0px; padding-left: 0px; padding-right: 0px; padding-top: 0px;">
              <table align="center" border="0" cellpadding="0" cellspacing="0" class="tablemobile" lang="x-tablem" style="border-collapse: collapse; mso-table-lspace: 0pt; mso-table-rspace: 0pt;" width="500">
               <tbody>
                <tr>
                 <td align="center" style="color: #333333; font-size: 13px; line-height: 18px; padding-bottom: 0px; padding-left: 20px; padding-right: 20px; padding-top: 0px;"><font color="#333333" size="2" style="font-family: 'Noto Sans', 'Open Sans', sans-serif; text-size-adjust: 100%;"> If you did not signup please report <a href="#" style="text-decoration: none; color: red">Not Me</a>. </font></td>
                </tr>

                <td align="center" style="color: #333333; font-size: 13px; line-height: 18px; padding-bottom: 0px; padding-left: 20px; padding-right: 20px; padding-top: 0px;"><font color="#333333" size="2" style="font-family: 'Noto Sans', 'Open Sans', sans-serif; text-size-adjust: 100%;"> 

          
                  <?php if($user['status'] == 0) {?>


                    <p> Your account is subjected to be activated before you can use these credentails to login </p>


                  <?php } ?>



                 </font></td>
                </tr>


               </tbody>
              </table>
             </td>
            </tr>
           </tbody>
          </table>
          <table align="center" bgcolor="#ffffff" border="0" cellpadding="0" cellspacing="0" class="contenttable" lang="x-content" style="border-collapse: collapse; min-width: 280px; mso-table-lspace: 0pt; mso-table-rspace: 0pt; width: 500px;" width="500">
           <tbody>
            <tr>
             <td align="center" height="20" style="color: transparent; font-size: 1px; height: 20px; line-height: 20px; padding: 0px 0px 0px 0px;"> &nbsp; </td>
            </tr>
           </tbody>
          </table>
          <table align="center" bgcolor="#5c6bc0" border="0" cellpadding="0" cellspacing="0" class="contenttable" lang="x-content" style="border-collapse: collapse; min-width: 280px; mso-table-lspace: 0pt; mso-table-rspace: 0pt; width: 500px;" width="500">
           <tbody>
            <tr>
             <td align="center" style="padding-bottom: 20px; padding-left: 20px; padding-right: 20px; padding-top: 20px;">
              <table align="left" border="0" cellpadding="0" cellspacing="0" class="tablemobile" lang="x-tablem" style="border-collapse: collapse; mso-table-lspace: 0pt; mso-table-rspace: 0pt;">
               <tbody>
                <tr>
                 <td align="center" editable="social" style="font-size: 12px; line-height: 100%; padding-bottom: 0px; padding-left: 0px; padding-right: 0px; padding-top: 0px;"><a href="#" style="color: inherit; text-decoration: none;"><img alt="twitter" border="0" height="16" src="https://robust.email/files/template_2/images/twitter.gif" style="" width="16" /></a>&nbsp;&nbsp;&nbsp;<a href="#" style="color: inherit; text-decoration: none;"><img alt="youtube" border="0" height="16" src="https://robust.email/files/template_2/images/youtube.gif" style="" width="16" /></a>&nbsp;&nbsp;&nbsp;<a href="#" style="color: inherit; text-decoration: none;"><img alt="facebook" border="0" height="16" src="https://robust.email/files/template_2/images/facebook.gif" style="" width="16" /></a>&nbsp;&nbsp;&nbsp;<a href="#" style="color: inherit; text-decoration: none;"><img alt="googleplus" border="0" height="16" src="https://robust.email/files/template_2/images/googleplus.gif" style="" width="16" /></a>&nbsp;&nbsp;&nbsp;<a href="#" style="color: inherit; text-decoration: none;"><img alt="linkedin" border="0" height="16" src="https://robust.email/files/template_2/images/linkedin.gif" style="" width="16" /></a>&nbsp;&nbsp;&nbsp;<a href="#" style="color: inherit; text-decoration: none;"><img alt="rss" border="0" height="16" src="https://robust.email/files/template_2/images/rss.gif" style="" width="16" /></a></td>
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
                 <td align="center" style="color: #ffffff; font-size: 12px; line-height: 18px; padding-bottom: 0px; padding-left: 0px; padding-right: 0px; padding-top: 0px;"><font color="#ffffff" size="2" style="font-family: 'Montserrat', 'Gotham Rounded', 'Gotham', 'Noto Sans', 'Open Sans', 'Source Sans Pro', 'Segoe UI','Segoe UI Web Regular','Segoe UI Symbol','Helvetica Neue',Arial,sans-serif; font-size: 12px; text-size-adjust: 100%; text-transform: uppercase;"><a href="#" style="color: #ffffff; text-decoration: none;">Privacy</a>&nbsp;&nbsp;/&nbsp;&nbsp;<a href="#" style="color: #ffffff; text-decoration: none;">Contact</a> </font></td>
                </tr>
               </tbody>
              </table>
             </td>
            </tr>
           </tbody>
          </table>
          <!--Shadow End-->
         </td>
        </tr>
       </tbody>
      </table>
      <table align="center" bgcolor="transparent" border="0" cellpadding="0" cellspacing="0" class="contenttable" lang="x-content" style="border-collapse: collapse; min-width: 280px; mso-table-lspace: 0pt; mso-table-rspace: 0pt; width: 500px;" width="500">
       <tbody>
        <tr>
         <td align="center" style="color: #b0b0b0; font-size: 13px; font-weight: 400; line-height: 18px; padding: 20px 30px 20px 30px;"><font color="#b0b0b0" size="2" style="font-family: 'Open Sans', 'Source Sans Pro', sans-serif; font-weight: 400; text-size-adjust: 100%;"> Copyright&copy; <?= DATE('Y')?> iSystmatic.com LLC </font></td>
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
