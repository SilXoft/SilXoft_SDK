//function below added by logan (cailongqun [at] yahoo [dot] com [dot] cn) from www.phpletter.com
function selectFile(url)
{
   console.log(url);
  if(url != '' )
  {     console.log($('#cke_79_textInput', window.opener.document));
      $('#cke_79_textInput', window.opener.document).val(url);
      //console.log($('#cke_77_textInput', window.parent.document));
    //  window.opener.SetUrl( url ) ;
      window.close() ;
      
  }else
  {
     alert(noFileSelected);
  }
  return false;
  

}



function cancelSelectFile()
{
  // close popup window
  window.close() ;
  return false;
}