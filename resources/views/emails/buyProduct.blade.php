<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html>
   <head>
      <link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.15.4/css/all.css" >
      <meta name="viewport" content="width=device-width, initial-scale=1.0" />
      <meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
      <meta name="color-scheme" content="light" />
      <meta name="supported-color-schemes" content="light" />
      <style>
         h4, p,h3{
            font-size: 1.3rem;
         }

         h2{
            font-size: 2rem;
         }
      </style>
   </head>
   <body marginheight=0 marginwidth=0 topmargin=0 leftmargin=0 style="height: 100% !important; margin: 0; padding: 0; width: 100% !important;min-width: 100%;">
      <table width="100%" cellspacing="0" cellpadding="0" border="0" name="bmeMainBody" style="background-color: #FFF;" >
         <tbody>
            <tr>
               <td width="100%" valign="top" align="center">
                  <table cellspacing="0" cellpadding="0" border="0" name="bmeMainColumnParentTable">
                     <tbody>
                        <tr>
                           <td name="bmeMainColumnParent" style="border: 0px none transparent; border-radius: 0px; border-collapse: separate;">
                              <table name="bmeMainColumn" class="bmeHolder bmeMainColumn" style="max-width: 600px; overflow: visible; border-radius: 0px; border-collapse: separate; border-spacing: 0px;" cellspacing="0" cellpadding="0" border="0" align="center">
                                 <tbody>
                                    <tr>
                                       <td width="100%" class="blk_container bmeHolder" name="bmePreHeader" valign="top" align="center" style="color: rgb(102, 102, 102); border: 0px none transparent;"></td>
                                    </tr>
                                    <tr>
                                       <td width="100%" class="bmeHolder" valign="top" align="center" name="bmeMainContentParent" style="border: 0px none rgb(102, 102, 102); border-radius: 0px; border-collapse: separate; border-spacing: 0px; overflow: hidden;">
                                          <table name="bmeMainContent" style="border-radius: 0px; border-collapse: separate; border-spacing: 0px; border: 0px none transparent;" width="100%" cellspacing="0" cellpadding="0" border="0" align="center">
                                             <tbody>
                                                <tr>
                                                   <td width="100%" class="blk_container bmeHolder" name="bmeHeader" valign="top" align="center" style="color: rgb(56, 56, 56); border: 0px none transparent; background-color: transparent;" >
                                                      <div id="dv_1" class="blk_wrapper" style="">
                                                         <table width="600" cellspacing="0" cellpadding="0" border="0" class="blk" name="blk_text">
                                                            <tbody>
                                                               <tr>
                                                                  <td>
                                                                     <table cellpadding="0" cellspacing="0" border="0" width="100%" class="bmeContainerRow">
                                                                        <tbody>
                                                                           <tr>
                                                                              <td class="tdPart" valign="top" align="center">
                                                                                 <table cellspacing="0" cellpadding="0" border="0" width="600" name="tblText" style="float:left; background-color:transparent;" align="left" class="tblText">
                                                                                    <tbody>
                                                                                       <tr>
                                                                                          <td valign="top" align="left" name="tblCell" style="padding: 20px; font-family: Arial, Helvetica, sans-serif; font-size: 14px; font-weight: 400; color: rgb(56, 56, 56); text-align: left;" class="tblCell">
                                                                                                <div style="line-height: 150%; text-align: center;">
                                                                                                    <span style="font-size: 12px; font-family: Helvetica, Arial, sans-serif; color: #2E86C1; line-height: 150%;">
                                                                                                        {{-- <strong>My lemon clean</strong> --}}
                                                                                                    </span>
                                                                                                </div>
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
                                                      </div>
                                                   </td>
                                                </tr>
                                                <tr>
                                                   <td width="100%" class="blk_container bmeHolder bmeBody" name="bmeBody" valign="top" align="center" style="color: rgb(56, 56, 56); border: 0px none transparent; background-color: rgb(255, 255, 255);" bgcolor="#ffffff">

                                                      <div id="dv_3" class="blk_wrapper" style="background-color: #FFF;">

                                                         <table width="100%" cellspacing="0" cellpadding="0" border="0">
                                                            <tbody>

                                                               <tr>
                                                                  <td align="center" class="bmeImage" style="border-collapse: collapse; padding: 10px;">
                                                                        <h2>{{$productName}}</h2>
                                                                        <img src="{{ $routeGlobal }}{{ $imageProduct }}" alt="{{$productName}}" style="width: 100% !important;">
                                                                        <h4>Comprador:</h4>
                                                                        <p>{{$userName}}</p>
                                                                        <h4>Correo:</h4>
                                                                        <p>{{$email}}</p>
                                                                        <h4>Telefono:</h4>
                                                                        <p>{{ $cellphone }}</p>
                                                                        {{-- <h4>Your plan:</h4>
                                                                        <p><b>{{$plan}} - </b><small>({{ $recurring }})</small></p> --}}
                                                                        <h4>Puntos utilizados:</h4>
                                                                        <p><b>{{number_format($price)}}</b></p>
                                                                    </td>
                                                               </tr>
                                                            </tbody>
                                                         </table>

                                                      </div>

                                                      <div id="dv_4" class="blk_wrapper" style="">
                                                         <table width="600" cellspacing="0" cellpadding="0" border="0" class="blk" name="blk_divider" style="">
                                                            <tbody>
                                                               <tr>
                                                                  <td class="tblCellMain" style="padding: 20px 0px;">
                                                                     <table class="tblLine" cellspacing="0" cellpadding="0" border="0" width="100%" style="border-top-width: 1px; border-top-color: rgb(148, 148, 148); border-top-style: solid; min-width: 1px;">
                                                                        <tbody>
                                                                           <tr>
                                                                               <td></td>
                                                                           </tr>
                                                                        </tbody>
                                                                     </table>
                                                                  </td>
                                                               </tr>
                                                            </tbody>
                                                         </table>
                                                      </div>

                                                      <div id="dv_3" class="blk_wrapper" style="background-color: #FFF;">

                                                         <table width="100%" cellspacing="0" cellpadding="0" border="0">
                                                            <tbody>

                                                               <tr>
                                                                  <td align="center" class="bmeImage" style="border-collapse: collapse; padding: 10px;">
                                                                     <h3>Estatus de la orden: <small style="color: #F4D03F">Pendiende por validar</small></h3>
                                                                     <br>
                                                                     <h3>Dirección de entrega </h3>
                                                                     {{-- <img src="{{ asset('app/img/pencil.png') }}" alt="pencil"> --}}
                                                                     <p><b>Calle:</b> {{$street}}</p>
                                                                     {{-- <img src="{{ asset('app/img/heart.png') }}" alt="heart"> --}}
                                                                     <p><b>Ciudad: </b>{{ $city }} <b>  Estado: </b>{{ $state }}</p>
                                                                     <p><b>Municipio:</b> {{$suburb}} </p>
                                                                     <p><b>CP: </b> {{ $zip }}</b></p>
                                                                     <br>
                                                                  </td>
                                                               </tr>
                                                            </tbody>
                                                         </table>

                                                      </div>

                                                      <div id="dv_4" class="blk_wrapper" style="">
                                                         <table width="600" cellspacing="0" cellpadding="0" border="0" class="blk" name="blk_divider" style="">
                                                            <tbody>
                                                               <tr>
                                                                  <td class="tblCellMain" style="padding: 20px 0px;">
                                                                     <table class="tblLine" cellspacing="0" cellpadding="0" border="0" width="100%" style="border-top-width: 1px; border-top-color: rgb(148, 148, 148); border-top-style: solid; min-width: 1px;">
                                                                        <tbody>
                                                                           <tr>
                                                                               <td></td>
                                                                           </tr>
                                                                        </tbody>
                                                                     </table>
                                                                  </td>
                                                               </tr>
                                                            </tbody>
                                                         </table>
                                                      </div>

                                                      <div id="dv_3" class="blk_wrapper" style="background-color: #FFF;">

                                                         <table width="100%" cellspacing="0" cellpadding="0" border="0">
                                                            <tbody>

                                                               <tr>
                                                                  <td align="center" class="bmeImage" style="border-collapse: collapse; padding: 10px;">
                                                                     <div style="border-bottom-width: 1px; border-top-color: rgba(148, 148, 148, 0.712); border-bottom-style: solid; min-width: 1px;width: 70%;">
                                                                        <h3>Tú pedido esta pendiente por validad por uno de nuestros administradores!</h3>
                                                                     </div>
                                                                     <br>
                                                                     <p>Cuando aceptemos tú pedido nos comunicaremos con tigo</p>

                                                                     {{-- {{ route('calendarResidencial') }} --}}
                                                                     {{-- <a href="" style="color:#FFFFFF;text-decoration:none;" target="_blank">
                                                                        <div style="width: 50%;border-radius: 20px; border: 0px none transparent; text-align: center; font-family: Arial, Helvetica, sans-serif; font-size: 14px; padding: 10px 40px; font-weight: bold; background-color: #7EC242;" class="bmeButtonText">
                                                                           <span style="font-family: Helvetica, Arial, sans-serif; font-size: 14px; color: rgb(255, 255, 255);">
                                                                              Visit our page
                                                                           </span>
                                                                        </div>
                                                                     </a> --}}
                                                                  </td>
                                                               </tr>
                                                            </tbody>
                                                         </table>

                                                      </div>


                                                      <div id="dv_4" class="blk_wrapper" style="">
                                                         <table width="600" cellspacing="0" cellpadding="0" border="0" class="blk" name="blk_divider" style="">
                                                            <tbody>
                                                               <tr>
                                                                  <td class="tblCellMain" style="padding: 20px 0px;">
                                                                     <table class="tblLine" cellspacing="0" cellpadding="0" border="0" width="100%" style="border-top-width: 1px; border-top-color: #FFF; border-top-style: solid; min-width: 1px;">
                                                                        <tbody>
                                                                           <tr>
                                                                              <td><span>
                                                                                {{-- rgb(223, 223, 223)       --}}
                                                                            </span></td>
                                                                           </tr>
                                                                        </tbody>
                                                                     </table>
                                                                  </td>
                                                               </tr>
                                                            </tbody>
                                                         </table>
                                                      </div>
                                                   </td>
                                                </tr>

                                                <!-- footer -->

                                                <tr>
                                                   <td width="100%" class="blk_container bmeHolder" name="bmePreFooter" valign="top" align="center" style="border: 0px none transparent; background-color: rgb(255, 255, 255);">
                                                      <div id="dv_17" class="blk_wrapper" style="">
                                                         <table width="600" cellspacing="0" cellpadding="0" border="0" class="blk" name="blk_divider" style="">
                                                            <tbody>
                                                               <tr>
                                                                  <td class="tblCellMain" style="padding: 20px 0px;">
                                                                     <table class="tblLine" cellspacing="0" cellpadding="0" border="0" width="100%" style="border-top-width: 0px; border-top-style: none; min-width: 1px;">
                                                                        <tbody>
                                                                           <tr>
                                                                              <td>
                                                                                 <span>Para cualquier duda favor de contactarnos: <a style="color:#2980B9">promociones@desafiosoccerfans.com, (81) 4444 1019.</a></span>
                                                                              </td>
                                                                           </tr>
                                                                        </tbody>
                                                                     </table>
                                                                  </td>
                                                               </tr>
                                                            </tbody>
                                                         </table>
                                                      </div>
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
   </body>
</html>
