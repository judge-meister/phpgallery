
// WiredPussy scripts
function showall()
{
  var wpsites=['devicebondage','boundgangbangs','electrosluts','everythingbutt','theupperfloor','fuckingmachines','hogtied','sexandsubmission','thetrainingofo','waterbondage','whippedass','wiredpussy','pissing','publicdisgrace'];
  for (i=0;i<wpsites.length;i++) {
    document.getElementById("1"+wpsites[i]).style.display = "block";
    element = document.getElementById("2"+wpsites[i]);
    if (element != null) { element.style.display = "block"; }
    document.getElementById("x"+wpsites[i]).style.backgroundColor = "#000000";
    //document.getElementById("x"+wpsites[i]+"1").style.backgroundColor = "#000000";
  }
  //document.getElementById("all").style.display = "block";
  document.getElementById("xall").style.backgroundColor = "#f47c0e";
}

// WiredPussy scripts
function hideshow(id, action)
{
  var wpsites=['devicebondage','boundgangbangs','electrosluts','everythingbutt','theupperfloor','fuckingmachines','hogtied','sexandsubmission','thetrainingofo','waterbondage','whippedass','wiredpussy','pissing','publicdisgrace'];
  if (action=="show") {
    for (i=0;i<wpsites.length;i++) {
      //document.getElementById(wpsites[i]).style.display = "none";
      document.getElementById("1"+wpsites[i]).style.display = "none";
      element = document.getElementById("2"+wpsites[i]);
      if (element != null) { element.style.display = "none"; }
      document.getElementById("x"+wpsites[i]).style.backgroundColor = "#000000";
      //document.getElementById("x"+wpsites[i]+"1").style.backgroundColor = "#000000";
    }
    document.getElementById("1"+id).style.display = "block";
    element = document.getElementById("2"+id);
    if (element != null) { element.style.display = "block"; }
    document.getElementById("x"+id).style.backgroundColor = "#f47c0e";
    //document.getElementById("x"+id+"1").style.backgroundColor = "#f47c0e";
  }
}

