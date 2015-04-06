<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
      class NuSoap_lib{
          function Nusoap_lib(){
          		require_once APPPATH.'third_party/Sync/nusoap'.EXT;
               // require_once(str_replace("\\","/",APPPATH).'third_party/NuSoap'.EXT); //If we are executing this script on a Windows server
          }
      }
