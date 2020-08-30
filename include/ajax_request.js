function ajax_request(){
  try{
    request = new XMLHttpRequest();
  }
  catch (error){
    alert(error);
    try{
      request = new ActiveXObject("Msxml2.XMLHTTP");
    } 
    catch (error){
      alert(error);
      try{
	request = new ActiveXObject("Microsoft.XMLHTTP");
      } 
      catch (error){
	alert(error);
	return false;
      }
    }  
  }
  return request;
}
